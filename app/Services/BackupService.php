<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackupService
{
    protected string $basePath;

    public function __construct()
    {
        $this->basePath = storage_path('app/backups');
        $this->ensureDirs();
    }

    public function create(string $type = 'local'): array
    {
        $filename = 'backup-' . date('Y-m-d-H-i-s') . '-' . Str::random(4) . '.zip';
        $dir = $this->basePath . '/' . $type;

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $zipPath = $dir . '/' . $filename;
        $sqlPath = storage_path('app/temp-backup.sql');

        try {
            $this->dumpDatabase($sqlPath);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => __('Database dump failed: :msg', ['msg' => $e->getMessage()])];
        }

        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            if (File::exists($sqlPath)) File::delete($sqlPath);
            return ['success' => false, 'message' => __('Could not create ZIP archive.')];
        }

        $zip->addFile($sqlPath, 'database.sql');

        $publicPath = public_path('uploads');
        if (File::isDirectory($publicPath)) {
            $this->addDirectoryToZip($zip, $publicPath, 'uploads');
        }

        $storageAppPublic = storage_path('app/public');
        if (File::isDirectory($storageAppPublic)) {
            $this->addDirectoryToZip($zip, $storageAppPublic, 'storage/app/public');
        }

        $zip->close();

        if (File::exists($sqlPath)) {
            File::delete($sqlPath);
        }

        $this->applyRetention($type);

        return [
            'success' => true,
            'filename' => $filename,
            'size' => File::size($zipPath),
            'message' => __('Backup created successfully.'),
        ];
    }

    public function getBackups(string $type = 'local'): array
    {
        $dir = $this->basePath . '/' . $type;

        if (!File::isDirectory($dir)) {
            return [];
        }

        $files = File::files($dir);
        $result = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'zip') continue;
            $result[] = [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'size_human' => $this->formatSize($file->getSize()),
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                'timestamp' => $file->getMTime(),
            ];
        }

        usort($result, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $result;
    }

    public function delete(string $filename, string $type = 'local'): array
    {
        $path = $this->basePath . '/' . $type . '/' . basename($filename);

        if (File::exists($path)) {
            File::delete($path);
            return ['success' => true, 'message' => __('Backup deleted.')];
        }

        return ['success' => false, 'message' => __('Backup not found.')];
    }

    public function restore(string $filename, string $type = 'local'): array
    {
        $path = $this->basePath . '/' . $type . '/' . basename($filename);

        if (!File::exists($path)) {
            return ['success' => false, 'message' => __('Backup not found.')];
        }

        $tempDir = storage_path('app/temp-restore-' . time());

        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            File::deleteDirectory($tempDir);
            return ['success' => false, 'message' => __('Could not open ZIP file.')];
        }

        $sqlFile = $tempDir . '/database.sql';
        if (File::exists($sqlFile)) {
            try {
                $this->restoreDatabase($sqlFile);
            } catch (\Throwable $e) {
                File::deleteDirectory($tempDir);
                return ['success' => false, 'message' => __('Database restore failed: :msg', ['msg' => $e->getMessage()])];
            }
        }

        $uploadsDir = $tempDir . '/uploads';
        if (File::isDirectory($uploadsDir)) {
            File::copyDirectory($uploadsDir, public_path('uploads'));
        }

        $storageDir = $tempDir . '/storage/app/public';
        if (File::isDirectory($storageDir)) {
            File::copyDirectory($storageDir, storage_path('app/public'));
        }

        File::deleteDirectory($tempDir);

        return ['success' => true, 'message' => __('Restored successfully.')];
    }

    protected function applyRetention(string $type): void
    {
        $limit = $type === 'download'
            ? (int) rj_get_option('backup_download_limit', 2)
            : (int) rj_get_option('backup_local_limit', 5);

        if ($type === 'download') {
            $limit = min(3, max(1, $limit));
        } else {
            $limit = max(1, $limit);
        }

        $backups = $this->getBackups($type);

        if (count($backups) > $limit) {
            $toDelete = array_slice($backups, $limit);
            foreach ($toDelete as $b) {
                File::delete($this->basePath . '/' . $type . '/' . $b['filename']);
            }
        }
    }

    protected function dumpDatabase(string $path): void
    {
        $conn = DB::connection();
        $driver = $conn->getDriverName();
        $handle = fopen($path, 'w+');

        if ($driver === 'mysql') {
            $tables = $conn->select('SHOW TABLES');
            $dbName = $conn->getDatabaseName();
            $key = 'Tables_in_' . $dbName;

            foreach ($tables as $table) {
                $tableName = $table->$key ?? array_values((array) $table)[0];
                $createTable = $conn->select("SHOW CREATE TABLE `$tableName`");
                $createSql = $createTable[0]->{'Create Table'} ?? '';

                fwrite($handle, "\n\n-- Table structure for $tableName\n");
                fwrite($handle, "DROP TABLE IF EXISTS `$tableName`;\n");
                fwrite($handle, $createSql . ";\n\n");

                $rows = $conn->table($tableName)->get();
                if ($rows->count() > 0) {
                    fwrite($handle, "-- Dumping data for table `$tableName`\n");
                    foreach ($rows as $row) {
                        $data = (array) $row;
                        $keys = array_map(fn($k) => "`$k`", array_keys($data));
                        $values = array_map(function ($v) use ($conn) {
                            if (is_null($v)) return 'NULL';
                            return $conn->getPdo()->quote((string) $v);
                        }, array_values($data));

                        fwrite($handle, "INSERT INTO `$tableName` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $values) . ");\n");
                    }
                }
            }
        } else {
            fwrite($handle, "-- Database dump for driver $driver not supported in this simple backup.\n");
        }

        fclose($handle);
    }

    protected function restoreDatabase(string $sqlFile): void
    {
        $sql = File::get($sqlFile);
        DB::unprepared($sql);
    }

    protected function addDirectoryToZip(\ZipArchive $zip, string $dir, string $base): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if ($file->isDir()) continue;
            $filePath = $file->getRealPath();
            $relative = $base . '/' . substr($filePath, strlen($dir) + 1);
            $zip->addFile($filePath, $relative);
        }
    }

    protected function formatSize(float $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}