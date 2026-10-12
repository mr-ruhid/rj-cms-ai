<?php

namespace App\Http\Controllers\Admin\Settings;

use Illuminate\Http\Request;
use App\Models\Option;

class BackupSettingsController extends BaseSettingsController
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');

        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    public function index()
    {
        $options = $this->getGroup('backup');

        $data = [
            'schedules' => $this->schedules(),
            'backups' => $this->listBackups(),
            'stats' => $this->stats(),
        ];

        return view('admin.settings.backup', compact('options', 'data'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'backup_storage_mode' => ['required', 'in:local,download'],
            'backup_schedule' => ['required', 'in:never,daily,weekly,monthly'],
            'backup_retention' => ['nullable', 'integer', 'min:1', 'max:100'],
            'backup_include_db' => ['nullable', 'boolean'],
            'backup_include_files' => ['nullable', 'boolean'],
        ]);

        $data['backup_include_db'] = $request->boolean('backup_include_db') ? '1' : '0';
        $data['backup_include_files'] = $request->boolean('backup_include_files') ? '1' : '0';

        if ($data['backup_storage_mode'] === 'download') {
            $data['backup_schedule'] = 'never';
        }

        foreach ($data as $name => $value) {
            Option::set($name, $value, 'backup');
        }

        return back()->with('success', __('Backup settings saved.'));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:site,database,full'],
        ]);

        try {
            $result = match ($data['type']) {
                'database' => $this->makeDatabaseBackup(),
                'site' => $this->makeFilesBackup(),
                'full' => $this->makeFullBackup(),
            };

            if (!$result['success']) {
                return back()->with('error', $result['error']);
            }

            $storageMode = rj_get_option('backup_storage_mode', 'local');

            if ($storageMode === 'download') {
                return response()->download($result['path'], $result['file'])->deleteFileAfterSend(true);
            }

            return back()->with('success', __('Backup created: :file', ['file' => $result['file']]));
        } catch (\Throwable $e) {
            return back()->with('error', __('Backup failed: :message', ['message' => $e->getMessage()]));
        }
    }

    public function download(string $file)
    {
        $file = basename($file);
        $path = $this->backupPath . DIRECTORY_SEPARATOR . $file;

        if (!file_exists($path)) {
            return back()->with('error', __('Backup file not found.'));
        }

        return response()->download($path);
    }

    public function delete(string $file)
    {
        $file = basename($file);
        $path = $this->backupPath . DIRECTORY_SEPARATOR . $file;

        if (!file_exists($path)) {
            return back()->with('error', __('Backup file not found.'));
        }

        @unlink($path);

        return back()->with('success', __('Backup deleted.'));
    }

    protected function makeDatabaseBackup(): array
    {
        $filename = 'db-' . date('Y-m-d-His') . '.sql';
        $path = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        $result = $this->dumpDatabase($path);

        if (!$result['success']) {
            return $result;
        }

        return ['success' => true, 'file' => $filename, 'path' => $path];
    }

    protected function makeFilesBackup(): array
    {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'error' => 'ZipArchive PHP extension is not enabled.'];
        }

        $filename = 'site-' . date('Y-m-d-His') . '.zip';
        $path = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        $zip = new \ZipArchive();

        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return ['success' => false, 'error' => 'Cannot create zip archive.'];
        }

        $sources = [
            storage_path('app/public') => 'storage',
            public_path('uploads') => 'uploads',
        ];

        foreach ($sources as $dir => $prefix) {
            if (is_dir($dir)) {
                $this->addDirectoryToZip($zip, $dir, $prefix);
            }
        }

        $zip->close();

        return ['success' => true, 'file' => $filename, 'path' => $path];
    }

    protected function makeFullBackup(): array
    {
        if (!class_exists('ZipArchive')) {
            return ['success' => false, 'error' => 'ZipArchive PHP extension is not enabled.'];
        }

        $dbFile = 'db-temp-' . date('Y-m-d-His') . '.sql';
        $dbPath = $this->backupPath . DIRECTORY_SEPARATOR . $dbFile;

        $dbResult = $this->dumpDatabase($dbPath);

        if (!$dbResult['success']) {
            return $dbResult;
        }

        $filename = 'full-' . date('Y-m-d-His') . '.zip';
        $path = $this->backupPath . DIRECTORY_SEPARATOR . $filename;

        $zip = new \ZipArchive();

        if ($zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            @unlink($dbPath);

            return ['success' => false, 'error' => 'Cannot create zip archive.'];
        }

        $zip->addFile($dbPath, 'database.sql');

        $sources = [
            storage_path('app/public') => 'storage',
            public_path('uploads') => 'uploads',
        ];

        foreach ($sources as $dir => $prefix) {
            if (is_dir($dir)) {
                $this->addDirectoryToZip($zip, $dir, $prefix);
            }
        }

        $zip->close();
        @unlink($dbPath);

        return ['success' => true, 'file' => $filename, 'path' => $path];
    }

    protected function dumpDatabase(string $path): array
    {
        $db = config('database.connections.mysql');

        $cmd = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
            escapeshellarg($db['host']),
            escapeshellarg($db['port'] ?? '3306'),
            escapeshellarg($db['username']),
            escapeshellarg($db['password']),
            escapeshellarg($db['database']),
            escapeshellarg($path)
        );

        exec($cmd, $output, $code);

        if ($code !== 0 || !file_exists($path) || filesize($path) === 0) {
            if (file_exists($path)) {
                @unlink($path);
            }

            return ['success' => false, 'error' => 'mysqldump failed. Check if mysqldump is available in PATH.'];
        }

        return ['success' => true];
    }

    protected function addDirectoryToZip(\ZipArchive $zip, string $dir, string $prefix): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $baseLen = strlen($dir) + 1;

        foreach ($iterator as $file) {
            $relative = substr($file->getPathname(), $baseLen);
            $localPath = $prefix . '/' . str_replace('\\', '/', $relative);

            if ($file->isDir()) {
                $zip->addEmptyDir($localPath);
            } else {
                $zip->addFile($file->getPathname(), $localPath);
            }
        }
    }

    protected function listBackups(): array
    {
        $files = glob($this->backupPath . DIRECTORY_SEPARATOR . '*');

        if (!$files) {
            return [];
        }

        $result = [];

        foreach ($files as $path) {
            if (!is_file($path)) {
                continue;
            }

            $result[] = [
                'name' => basename($path),
                'size' => $this->humanSize(filesize($path)),
                'created_at' => date('Y-m-d H:i:s', filemtime($path)),
                'age' => $this->humanAge(filemtime($path)),
            ];
        }

        usort($result, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $result;
    }

    protected function stats(): array
    {
        $files = glob($this->backupPath . DIRECTORY_SEPARATOR . '*') ?: [];
        $totalSize = 0;
        $count = 0;

        foreach ($files as $f) {
            if (is_file($f)) {
                $totalSize += filesize($f);
                $count++;
            }
        }

        return [
            'count' => $count,
            'total_size' => $this->humanSize($totalSize),
            'disk_free' => $this->humanSize(@disk_free_space(storage_path()) ?: 0),
            'disk_total' => $this->humanSize(@disk_total_space(storage_path()) ?: 0),
        ];
    }

    protected function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    protected function humanAge(int $timestamp): string
    {
        $diff = time() - $timestamp;

        if ($diff < 60) return $diff . 's ' . __('ago');
        if ($diff < 3600) return floor($diff / 60) . 'm ' . __('ago');
        if ($diff < 86400) return floor($diff / 3600) . 'h ' . __('ago');
        if ($diff < 2592000) return floor($diff / 86400) . 'd ' . __('ago');

        return floor($diff / 2592000) . 'mo ' . __('ago');
    }

    protected function schedules(): array
    {
        return [
            'never' => __('Never') . ' — ' . __('Manual backups only'),
            'daily' => __('Daily') . ' — ' . __('Every day at 3:00 AM'),
            'weekly' => __('Weekly') . ' — ' . __('Every Sunday at 3:00 AM'),
            'monthly' => __('Monthly') . ' — ' . __('1st day of month at 3:00 AM'),
        ];
    }
}