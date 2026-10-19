<?php

namespace App\Services;

use App\Models\Theme;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ThemeEditorService
{
    protected array $allowedExtensions = [
        'php', 'js', 'css', 'json', 'md', 'txt', 'html', 'svg', 'xml', 'webp', 'png', 'jpg', 'jpeg', 'gif', 'ico',
    ];

    protected array $readonlyExtensions = [
        'png', 'jpg', 'jpeg', 'gif', 'webp', 'ico',
    ];

    public function backupPath(Theme $theme): string
    {
        return storage_path('app/theme-backups/' . $theme->slug);
    }

    public function discoverFiles(Theme $theme): array
    {
        $basePath = $theme->path();

        if (!File::isDirectory($basePath)) {
            return [];
        }

        $files = [];

        foreach (File::allFiles($basePath) as $file) {
            $relativePath = str_replace('\\', '/', $file->getRelativePathname());

            if (str_contains($relativePath, 'vendor/') || str_contains($relativePath, 'node_modules/')) {
                continue;
            }

            if (str_contains($relativePath, 'theme-backups/')) {
                continue;
            }

            $ext = strtolower($file->getExtension());

            if (!in_array($ext, $this->allowedExtensions, true) && !str_ends_with($relativePath, '.blade.php')) {
                continue;
            }

            $files[] = [
                'path' => $relativePath,
                'name' => $file->getFilename(),
                'dir' => str_replace('\\', '/', $file->getRelativePath()),
                'size' => $file->getSize(),
                'ext' => $ext,
                'readonly' => in_array($ext, $this->readonlyExtensions, true),
                'modified' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        usort($files, fn($a, $b) => strcmp($a['path'], $b['path']));

        return $files;
    }

    public function readFile(Theme $theme, string $relativePath): array
    {
        $path = $this->resolveSafePath($theme, $relativePath);

        if (!$path || !File::exists($path)) {
            return ['success' => false, 'error' => __('File not found.')];
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, $this->readonlyExtensions, true)) {
            return [
                'success' => true,
                'content' => '',
                'path' => $relativePath,
                'readonly' => true,
                'binary' => true,
            ];
        }

        try {
            return [
                'success' => true,
                'content' => File::get($path),
                'path' => $relativePath,
                'readonly' => false,
                'binary' => false,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function saveFile(Theme $theme, string $relativePath, ?string $content): array
    {
        $path = $this->resolveSafePath($theme, $relativePath);

        if (!$path || !File::exists($path)) {
            return ['success' => false, 'error' => __('Invalid file path.')];
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, $this->readonlyExtensions, true)) {
            return ['success' => false, 'error' => __('This file type cannot be edited.')];
        }

        $content = $content ?? '';

        $validation = $this->validate($path, $content, $relativePath);

        if (!$validation['success']) {
            return $validation;
        }

        try {
            $backup = $this->createBackup($theme, $relativePath, File::get($path));

            File::put($path, $content);

            return [
                'success' => true,
                'message' => __('File saved.'),
                'backup' => $backup,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function validate(string $path, string $content, string $relativePath): array
    {
        $filename = basename($relativePath);

        if ($filename === 'theme.json') {
            json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'error' => __('theme.json is not valid JSON: :msg', ['msg' => json_last_error_msg()]),
                ];
            }

            return ['success' => true];
        }

        if (str_ends_with($relativePath, '.json')) {
            json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'error' => __('Invalid JSON: :msg', ['msg' => json_last_error_msg()]),
                ];
            }
        }

        if (str_ends_with($relativePath, '.blade.php') || str_ends_with($relativePath, '.php')) {
            try {
                $compiled = \Illuminate\Support\Facades\Blade::compileString($content);

                $tmp = tempnam(sys_get_temp_dir(), 'rj_theme_check_');
                file_put_contents($tmp, "<?php\n" . $compiled);

                $output = null;
                $returnCode = 0;

                exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $output, $returnCode);

                @unlink($tmp);

                if ($returnCode !== 0) {
                    $message = implode("\n", $output);

                    if (preg_match('/Parse error:\s*(.+?)\s+in/', $message, $m)) {
                        $message = $m[1];
                    }

                    return [
                        'success' => false,
                        'error' => __('Syntax error: :msg', ['msg' => $message]),
                    ];
                }
            } catch (\Throwable $e) {
                return [
                    'success' => false,
                    'error' => __('Blade compile error: :msg', ['msg' => $e->getMessage()]),
                ];
            }
        }

        return ['success' => true];
    }

    public function createBackup(Theme $theme, string $relativePath, string $content): ?string
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s_u');
            $safePath = str_replace(['/', '\\'], '_', $relativePath);

            $dir = $this->backupPath($theme) . '/' . $timestamp;

            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $backupFile = $dir . '/' . $safePath . '.bak';

            File::put($backupFile, $content);

            File::put($dir . '/meta.json', json_encode([
                'file' => $relativePath,
                'theme' => $theme->slug,
                'created_at' => now()->toDateTimeString(),
                'size' => strlen($content),
            ], JSON_PRETTY_PRINT));

            $this->pruneBackups($theme);

            return $timestamp;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function listBackups(Theme $theme, ?string $relativePath = null): array
    {
        $basePath = $this->backupPath($theme);

        if (!File::isDirectory($basePath)) {
            return [];
        }

        $backups = [];

        foreach (File::directories($basePath) as $dir) {
            $metaFile = $dir . '/meta.json';

            if (!File::exists($metaFile)) {
                continue;
            }

            $meta = json_decode(File::get($metaFile), true);

            if (!is_array($meta)) {
                continue;
            }

            if ($relativePath !== null && ($meta['file'] ?? '') !== $relativePath) {
                continue;
            }

            $backups[] = [
                'timestamp' => basename($dir),
                'file' => $meta['file'] ?? '',
                'theme' => $meta['theme'] ?? '',
                'created_at' => $meta['created_at'] ?? '',
                'size' => $meta['size'] ?? 0,
                'path' => $dir,
            ];
        }

        usort($backups, fn($a, $b) => strcmp($b['timestamp'], $a['timestamp']));

        return $backups;
    }

    public function restoreBackup(Theme $theme, string $timestamp): array
    {
        $dir = $this->backupPath($theme) . '/' . $timestamp;

        if (!File::isDirectory($dir)) {
            return ['success' => false, 'error' => __('Backup not found.')];
        }

        $metaFile = $dir . '/meta.json';

        if (!File::exists($metaFile)) {
            return ['success' => false, 'error' => __('Backup metadata not found.')];
        }

        $meta = json_decode(File::get($metaFile), true);
        $relativePath = $meta['file'] ?? '';

        if (!$relativePath) {
            return ['success' => false, 'error' => __('Backup file path missing.')];
        }

        $targetPath = $this->resolveSafePath($theme, $relativePath);

        if (!$targetPath) {
            return ['success' => false, 'error' => __('Invalid target path.')];
        }

        $safePath = str_replace(['/', '\\'], '_', $relativePath);
        $backupFile = $dir . '/' . $safePath . '.bak';

        if (!File::exists($backupFile)) {
            return ['success' => false, 'error' => __('Backup content missing.')];
        }

        try {
            if (File::exists($targetPath)) {
                $this->createBackup($theme, $relativePath, File::get($targetPath));
            }

            File::put($targetPath, File::get($backupFile));

            return [
                'success' => true,
                'message' => __('File restored from backup.'),
                'file' => $relativePath,
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function deleteBackup(Theme $theme, string $timestamp): array
    {
        $dir = $this->backupPath($theme) . '/' . $timestamp;

        if (!File::isDirectory($dir)) {
            return ['success' => false, 'error' => __('Backup not found.')];
        }

        try {
            File::deleteDirectory($dir);
            return ['success' => true, 'message' => __('Backup deleted.')];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function pruneBackups(Theme $theme, int $keep = 20): void
    {
        $backups = $this->listBackups($theme);

        if (count($backups) <= $keep) {
            return;
        }

        $toDelete = array_slice($backups, $keep);

        foreach ($toDelete as $backup) {
            File::deleteDirectory($backup['path']);
        }
    }

    public function themeInfo(Theme $theme): array
    {
        $basePath = $theme->path();

        $totalSize = 0;
        $fileCount = 0;

        if (File::isDirectory($basePath)) {
            foreach (File::allFiles($basePath) as $file) {
                $totalSize += $file->getSize();
                $fileCount++;
            }
        }

        $manifest = $theme->metadata();

        return [
            'name' => $theme->name,
            'slug' => $theme->slug,
            'version' => $theme->version,
            'author' => $theme->author,
            'description' => $theme->description,
            'status' => $theme->status,
            'is_active' => $theme->isActive(),
            'folder' => $theme->folder,
            'path' => $basePath,
            'path_exists' => File::isDirectory($basePath),
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_human' => $this->formatSize($totalSize),
            'manifest' => $manifest,
            'supports' => $theme->supports(),
            'customizations_count' => $theme->customizations()->count(),
            'readonly' => !empty($manifest['readonly']),
        ];
    }

    public function resolveSafePath(Theme $theme, ?string $relativePath): ?string
    {
        if (!$relativePath || str_contains($relativePath, '..')) {
            return null;
        }

        $basePath = str_replace('\\', '/', $theme->path());
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        $fullPath = $basePath . '/' . $relativePath;

        $realBase = realpath($basePath);
        $realFull = realpath($fullPath);

        if (!$realBase || !$realFull || !str_starts_with($realFull, $realBase)) {
            return null;
        }

        return $fullPath;
    }

    protected function formatSize(int $bytes): string
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
