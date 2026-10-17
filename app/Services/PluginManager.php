<?php

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PluginManager
{
    protected string $pluginsPath;

    public function __construct()
    {
        $this->pluginsPath = base_path('rj-content/plugins');
    }

    public function boot(): void
    {
        if (!File::isDirectory($this->pluginsPath)) {
            return;
        }

        $activePlugins = Plugin::active()->get();

        foreach ($activePlugins as $plugin) {
            $this->bootSinglePlugin($plugin);
        }
    }

    protected function bootSinglePlugin(Plugin $plugin): void
    {
        $metadata = $plugin->metadata();
        $providerClass = $metadata['provider'] ?? null;

        if ($providerClass && class_exists($providerClass)) {
            try {
                $reflection = new \ReflectionClass($providerClass);

                if ($reflection->isSubclassOf(\App\Plugins\PluginServiceProvider::class)) {
                    $instance = $reflection->newInstance(
                        app(),
                        $plugin->path(),
                        $metadata
                    );

                    $instance->register();
                    $instance->boot();

                    return;
                }
            } catch (\Throwable $e) {
                $plugin->markAsError($e->getMessage());
                return;
            }
        }

        $mainFile = $plugin->mainFilePath();

        if (File::exists($mainFile)) {
            try {
                require_once $mainFile;
            } catch (\Throwable $e) {
                $plugin->markAsError($e->getMessage());
            }
        }
    }

    public function assetUrl(string $slug, string $path = ''): ?string
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return null;
        }

        return $plugin->assetUrl($path);
    }

    public function resolveAsset(string $slug, string $path): ?string
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return null;
        }

        $manifest = $plugin->metadata();
        $assetsDir = $manifest['provides']['assets'] ?? 'assets';

        $base = realpath($plugin->path($assetsDir));

        if (!$base) {
            return null;
        }

        $full = realpath($base . '/' . ltrim($path, '/'));

        if (!$full || !str_starts_with($full, $base)) {
            return null;
        }

        if (!File::isFile($full)) {
            return null;
        }

        return $full;
    }

    public function discover(): int
    {
        if (!File::isDirectory($this->pluginsPath)) {
            File::makeDirectory($this->pluginsPath, 0755, true);
            return 0;
        }

        $folders = File::directories($this->pluginsPath);
        $count = 0;

        foreach ($folders as $folder) {
            $manifestPath = $folder . '/plugin.json';

            if (!File::exists($manifestPath)) {
                continue;
            }

            $manifest = json_decode(File::get($manifestPath), true);

            if (!is_array($manifest) || empty($manifest['name']) || empty($manifest['main'])) {
                continue;
            }

            $slug = $manifest['slug'] ?? Str::slug($manifest['name']);

            Plugin::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $manifest['name'],
                    'version' => $manifest['version'] ?? '1.0.0',
                    'description' => $manifest['description'] ?? null,
                    'author' => $manifest['author'] ?? null,
                    'author_url' => $manifest['author_url'] ?? null,
                    'plugin_url' => $manifest['plugin_url'] ?? null,
                    'folder' => basename($folder),
                    'main_file' => $manifest['main'],
                    'requires' => $manifest['requires'] ?? null,
                    'metadata' => $manifest,
                ]
            );

            $count++;
        }

        return $count;
    }

    public function activate(Plugin $plugin): array
    {
        if ($plugin->isActive()) {
            return ['success' => false, 'message' => __('Plugin is already active.')];
        }

        if (!File::exists($plugin->mainFilePath())) {
            $plugin->markAsError(__('Main file not found.'));
            return ['success' => false, 'message' => __('Main file not found.')];
        }

        if (!$plugin->meetsRequirements()) {
            $plugin->markAsError(__('Plugin requirements are not met.'));
            return ['success' => false, 'message' => __('Plugin requirements are not met.')];
        }

        try {
            $this->bootPlugin($plugin);

            $plugin->markAsActive();

            return ['success' => true, 'message' => __('Plugin activated.')];
        } catch (\Throwable $e) {
            $plugin->markAsError($e->getMessage());

            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deactivate(Plugin $plugin): array
    {
        if (!$plugin->isActive()) {
            return ['success' => false, 'message' => __('Plugin is not active.')];
        }

        try {
            $deactivator = $this->resolveClass($plugin, 'deactivate');

            if (is_callable($deactivator)) {
                call_user_func($deactivator);
            }

            $plugin->markAsInactive();

            return ['success' => true, 'message' => __('Plugin deactivated.')];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete(Plugin $plugin): array
    {
        if ($plugin->isActive()) {
            return ['success' => false, 'message' => __('Deactivate the plugin before deleting.')];
        }

        try {
            $path = $plugin->path();

            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }

            $plugin->delete();

            return ['success' => true, 'message' => __('Plugin deleted.')];
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

        $extractPath = storage_path('app/plugin-temp/' . uniqid('plugin_'));

        File::makeDirectory($extractPath, 0755, true);

        $zip->extractTo($extractPath);
        $zip->close();

        $manifestPath = $this->findManifest($extractPath);

        if (!$manifestPath) {
            File::deleteDirectory($extractPath);
            return ['success' => false, 'message' => __('plugin.json not found in archive.')];
        }

        $manifest = json_decode(File::get($manifestPath), true);

        if (!is_array($manifest) || empty($manifest['name']) || empty($manifest['main'])) {
            File::deleteDirectory($extractPath);
            return ['success' => false, 'message' => __('Invalid plugin.json.')];
        }

        $slug = $manifest['slug'] ?? Str::slug($manifest['name']);

        if (Plugin::where('slug', $slug)->exists()) {
            File::deleteDirectory($extractPath);
            return ['success' => false, 'message' => __('Plugin already installed.')];
        }

        $sourceDir = dirname($manifestPath);
        $targetDir = $this->pluginsPath . '/' . basename($sourceDir);

        if (!File::isDirectory($this->pluginsPath)) {
            File::makeDirectory($this->pluginsPath, 0755, true);
        }

        File::moveDirectory($sourceDir, $targetDir, true);
        File::deleteDirectory($extractPath);

        $this->discover();

        return ['success' => true, 'message' => __('Plugin uploaded.')];
    }

    public function checkForUpdates(): array
    {
        $count = 0;

        foreach (Plugin::all() as $plugin) {
            $metadata = $plugin->metadata();
            $updateUrl = $metadata['update_url'] ?? null;

            if (!$updateUrl) {
                continue;
            }

            try {
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get($updateUrl);

                if (!$response->successful()) {
                    continue;
                }

                $remote = $response->json();

                if (!empty($remote['version']) && version_compare($remote['version'], $plugin->version, '>')) {
                    $count++;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return ['success' => true, 'count' => $count];
    }

    public function updatesAvailable(): int
    {
        $count = 0;

        foreach (Plugin::all() as $plugin) {
            $metadata = $plugin->metadata();

            if (!empty($metadata['latest_version']) && version_compare($metadata['latest_version'], $plugin->version, '>')) {
                $count++;
            }
        }

        return $count;
    }

    public function update(Plugin $plugin): array
    {
        $metadata = $plugin->metadata();
        $downloadUrl = $metadata['download_url'] ?? null;

        if (!$downloadUrl) {
            return ['success' => false, 'message' => __('No update source configured.')];
        }

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(60)->get($downloadUrl);

            if (!$response->successful()) {
                return ['success' => false, 'message' => __('Could not download update.')];
            }

            $tmpFile = tempnam(sys_get_temp_dir(), 'plugin_update_');
            file_put_contents($tmpFile, $response->body());

            $fake = new UploadedFile($tmpFile, 'update.zip', 'application/zip', null, true);

            return $this->installFromZip($fake);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function bulkAction(array $slugs, string $action): array
    {
        $plugins = Plugin::whereIn('slug', $slugs)->get();
        $success = 0;
        $failed = 0;

        foreach ($plugins as $plugin) {
            $result = match ($action) {
                'activate' => $this->activate($plugin),
                'deactivate' => $this->deactivate($plugin),
                'delete' => $this->delete($plugin),
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

    protected function bootPlugin(Plugin $plugin): void
    {
        $file = $plugin->mainFilePath();

        if (!File::exists($file)) {
            throw new \RuntimeException(__('Main plugin file not found.'));
        }

        require_once $file;
    }

    protected function resolveClass(Plugin $plugin, string $action): ?callable
    {
        $metadata = $plugin->metadata();
        $class = $metadata['class'] ?? null;

        if (!$class || !class_exists($class)) {
            return null;
        }

        $method = $action;

        if (method_exists($class, $method)) {
            return [$class, $method];
        }

        return null;
    }

    protected function findManifest(string $dir): ?string
    {
        $direct = $dir . '/plugin.json';

        if (File::exists($direct)) {
            return $direct;
        }

        $folders = File::directories($dir);

        foreach ($folders as $folder) {
            $path = $folder . '/plugin.json';

            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}