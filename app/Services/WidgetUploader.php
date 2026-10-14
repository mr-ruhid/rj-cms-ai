<?php

namespace App\Services;

use App\Models\UploadedWidget;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WidgetUploader
{
    protected string $widgetsPath;

    public function __construct()
    {
        $this->widgetsPath = base_path('rj-content/widgets');
    }

    public function discover(): int
    {
        if (!File::isDirectory($this->widgetsPath)) {
            File::makeDirectory($this->widgetsPath, 0755, true);
            return 0;
        }

        $folders = File::directories($this->widgetsPath);
        $count = 0;

        foreach ($folders as $folder) {
            $manifestPath = $folder . '/widget.json';

            if (!File::exists($manifestPath)) {
                continue;
            }

            $manifest = json_decode(File::get($manifestPath), true);

            if (!is_array($manifest) || empty($manifest['name']) || empty($manifest['class'])) {
                continue;
            }

            $slug = $manifest['slug'] ?? Str::slug($manifest['name']);

            UploadedWidget::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $manifest['name'],
                    'class' => $manifest['class'],
                    'version' => $manifest['version'] ?? '1.0.0',
                    'description' => $manifest['description'] ?? null,
                    'author' => $manifest['author'] ?? null,
                    'author_url' => $manifest['author_url'] ?? null,
                    'icon' => $manifest['icon'] ?? null,
                    'category' => $manifest['category'] ?? 'custom',
                    'folder' => basename($folder),
                    'main_file' => $manifest['main'] ?? 'Widget.php',
                    'schema' => $manifest['schema'] ?? [],
                    'defaults' => $manifest['defaults'] ?? [],
                    'requires' => $manifest['requires'] ?? [],
                    'metadata' => $manifest,
                ]
            );

            $count++;
        }

        return $count;
    }

    public function activate(UploadedWidget $widget): array
    {
        if ($widget->isActive()) {
            return ['success' => false, 'message' => __('Widget is already active.')];
        }

        if (!File::exists($widget->mainFilePath())) {
            $widget->markAsError(__('Main file not found.'));
            return ['success' => false, 'message' => __('Main file not found.')];
        }

        try {
            $this->verifyClass($widget);

            $widget->markAsActive();

            \App\Widgets\WidgetRegistry::reset();
            $this->clearCache();

            return ['success' => true, 'message' => __('Widget activated.')];
        } catch (\Throwable $e) {
            $widget->markAsError($e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deactivate(UploadedWidget $widget): array
    {
        if (!$widget->isActive()) {
            return ['success' => false, 'message' => __('Widget is not active.')];
        }

        try {
            $widget->markAsInactive();

            \App\Widgets\WidgetRegistry::reset();
            $this->clearCache();

            return ['success' => true, 'message' => __('Widget deactivated.')];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete(UploadedWidget $widget): array
    {
        if ($widget->isActive()) {
            return ['success' => false, 'message' => __('Deactivate the widget before deleting.')];
        }

        try {
            $path = $widget->path();

            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }

            $widget->delete();

            \App\Widgets\WidgetRegistry::reset();
            $this->clearCache();

            return ['success' => true, 'message' => __('Widget deleted.')];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function installFromZip(UploadedFile $file): array
    {
        $tempPath = $file->getRealPath();

        $zip = new \ZipArchive();

        if ($zip->open($tempPath) !== true) {
            return ['success' => false, 'message' => __('Could not open ZIP archive.')];
        }

        $extractPath = storage_path('app/widget-temp/' . uniqid('widget_'));

        File::makeDirectory($extractPath, 0755, true);

        $zip->extractTo($extractPath);
        $zip->close();

        $manifestPath = $this->findManifest($extractPath);

        if (!$manifestPath) {
            File::deleteDirectory($extractPath);
            return ['success' => false, 'message' => __('widget.json not found in archive.')];
        }

        $manifest = json_decode(File::get($manifestPath), true);

        if (!is_array($manifest) || empty($manifest['name']) || empty($manifest['class'])) {
            File::deleteDirectory($extractPath);
            return ['success' => false, 'message' => __('Invalid widget.json.')];
        }

        $slug = $manifest['slug'] ?? Str::slug($manifest['name']);

        if (UploadedWidget::where('slug', $slug)->exists()) {
            File::deleteDirectory($extractPath);
            return ['success' => false, 'message' => __('Widget already installed.')];
        }

        $sourceDir = dirname($manifestPath);
        $targetDir = $this->widgetsPath . '/' . basename($sourceDir);

        if (!File::isDirectory($this->widgetsPath)) {
            File::makeDirectory($this->widgetsPath, 0755, true);
        }

        File::moveDirectory($sourceDir, $targetDir, true);
        File::deleteDirectory($extractPath);

        $this->discover();

        $widget = UploadedWidget::where('slug', $slug)->first();

        if ($widget) {
            $widget->update(['uploaded_by' => auth()->id()]);
        }

        \App\Widgets\WidgetRegistry::reset();

        return ['success' => true, 'message' => __('Widget uploaded.')];
    }

    public function bulkAction(array $slugs, string $action): array
    {
        $widgets = UploadedWidget::whereIn('slug', $slugs)->get();
        $success = 0;
        $failed = 0;

        foreach ($widgets as $widget) {
            $result = match ($action) {
                'activate' => $this->activate($widget),
                'deactivate' => $this->deactivate($widget),
                'delete' => $this->delete($widget),
                default => ['success' => false],
            };

            if ($result['success']) $success++;
            else $failed++;
        }

        return [
            'success' => true,
            'count' => $success,
            'failed' => $failed,
            'message' => __(':s succeeded, :f failed.', ['s' => $success, 'f' => $failed]),
        ];
    }

    public function loadActive(): int
    {
        $widgets = UploadedWidget::active()->get();
        $loaded = 0;

        foreach ($widgets as $widget) {
            $file = $widget->mainFilePath();

            if (!File::exists($file)) {
                $widget->markAsError(__('Main file not found.'));
                continue;
            }

            try {
                require_once $file;

                if (class_exists($widget->class)) {
                    $loaded++;
                } else {
                    $widget->markAsError(__('Class :c not found.', ['c' => $widget->class]));
                }
            } catch (\Throwable $e) {
                $widget->markAsError($e->getMessage());
            }
        }

        return $loaded;
    }

    public function clearCache(): void
    {
        Cache::forget('rj_widget_registry');
        Cache::forget('rj_widgets_uploaded');
    }

    protected function verifyClass(UploadedWidget $widget): void
    {
        $file = $widget->mainFilePath();

        if (!File::exists($file)) {
            throw new \RuntimeException(__('Main file not found.'));
        }

        require_once $file;

        if (!class_exists($widget->class)) {
            throw new \RuntimeException(__('Class :c not found.', ['c' => $widget->class]));
        }

        $reflection = new \ReflectionClass($widget->class);

        if (!$reflection->isSubclassOf(\App\Widgets\BaseWidget::class)) {
            throw new \RuntimeException(__('Class must extend BaseWidget.'));
        }
    }

    protected function findManifest(string $dir): ?string
    {
        $direct = $dir . '/widget.json';

        if (File::exists($direct)) {
            return $direct;
        }

        $folders = File::directories($dir);

        foreach ($folders as $folder) {
            $path = $folder . '/widget.json';

            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}