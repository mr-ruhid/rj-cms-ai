<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\Option;
use Illuminate\Http\Request;

class MediaCleanupController extends Controller
{
    public function index()
    {
        $orphans = $this->scanOrphans();
        $missing = $this->scanMissingFiles();

        $stats = [
            'orphan_count' => count($orphans),
            'orphan_size' => array_sum(array_column($orphans, 'size')),
            'missing_count' => count($missing),
            'db_count' => Media::count(),
        ];

        return view('admin.media.cleanup', compact('orphans', 'missing', 'stats'));
    }

    public function delete(Request $request)
    {
        $data = $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['required', 'string'],
        ]);

        $deleted = 0;
        $freed = 0;

        foreach ($data['files'] as $relative) {
            $path = public_path($relative);

            if (!file_exists($path) || !is_file($path)) {
                continue;
            }

            $size = filesize($path);

            if (@unlink($path)) {
                $deleted++;
                $freed += $size;
            }
        }

        return back()->with('success', __(':count file(s) deleted. :size freed.', [
            'count' => $deleted,
            'size' => $this->humanSize($freed),
        ]));
    }

    public function pruneDatabase()
    {
        $items = Media::all();
        $deleted = 0;

        foreach ($items as $item) {
            if (!file_exists(public_path($item->path))) {
                $item->delete();
                $deleted++;
            }
        }

        return back()->with('success', __(':count orphaned database record(s) removed.', ['count' => $deleted]));
    }

    protected function scanOrphans(): array
    {
        $uploadDir = public_path('uploads');

        if (!is_dir($uploadDir)) {
            return [];
        }

        $diskFiles = $this->scanFiles($uploadDir);

        $knownPaths = Media::pluck('path')->toArray();
        $knownMap = array_flip($knownPaths);

        $optionReferences = $this->collectOptionReferences();

        $orphans = [];

        foreach ($diskFiles as $relative => $absolute) {
            if (isset($knownMap[$relative])) {
                continue;
            }

            if ($this->isReferencedInOptions($relative, $optionReferences)) {
                continue;
            }

            $orphans[] = [
                'path' => $relative,
                'name' => basename($relative),
                'size' => filesize($absolute),
                'size_human' => $this->humanSize(filesize($absolute)),
                'modified' => date('Y-m-d H:i:s', filemtime($absolute)),
                'age' => $this->humanAge(filemtime($absolute)),
                'url' => asset($relative),
                'type' => $this->detectType($relative),
            ];
        }

        usort($orphans, fn($a, $b) => $b['size'] <=> $a['size']);

        return $orphans;
    }

    protected function scanMissingFiles(): array
    {
        $items = Media::all();
        $missing = [];

        foreach ($items as $item) {
            if (!file_exists(public_path($item->path))) {
                $missing[] = [
                    'id' => $item->id,
                    'name' => $item->original_name,
                    'path' => $item->path,
                    'size_human' => $item->humanSize(),
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'edit_url' => route('admin.media.edit', $item),
                ];
            }
        }

        return $missing;
    }

    protected function scanFiles(string $dir): array
    {
        $result = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        $baseLen = strlen(public_path()) + 1;

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $relative = str_replace('\\', '/', substr($file->getPathname(), $baseLen));
            $result[$relative] = $file->getPathname();
        }

        return $result;
    }

    protected function collectOptionReferences(): string
    {
        $options = Option::where('option_group', '!=', 'backup')
            ->where('option_value', 'like', '%uploads/%')
            ->pluck('option_value')
            ->toArray();

        return implode("\n", $options);
    }

    protected function isReferencedInOptions(string $relative, string $haystack): bool
    {
        if ($haystack === '') {
            return false;
        }

        $name = basename($relative);

        return str_contains($haystack, $relative) || str_contains($haystack, $name);
    }

    protected function detectType(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'])) {
            return 'image';
        }

        if (in_array($ext, ['mp4', 'webm', 'mov', 'avi', 'mkv'])) {
            return 'video';
        }

        if (in_array($ext, ['mp3', 'wav', 'ogg', 'm4a', 'flac'])) {
            return 'audio';
        }

        if (in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv'])) {
            return 'document';
        }

        return 'other';
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
}