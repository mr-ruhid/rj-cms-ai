<?php

namespace App\Services;

use App\Models\Plugin;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class PluginManager
{
    protected string $pluginsPath;
    protected static array $autoloaded = [];

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

    protected function registerPluginAutoloader(Plugin $plugin): void
    {
        $manifest = $plugin->metadata();
        $namespace = $manifest['namespace'] ?? null;

        if (!$namespace) {
            return;
        }

        $srcPath = $plugin->path() . '/src';

        if (!File::isDirectory($srcPath)) {
            return;
        }

        $key = 'plugin_' . $plugin->slug;

        if (isset(static::$autoloaded[$key])) {
            return;
        }

        static::$autoloaded[$key] = true;

        $namespace = rtrim($namespace, '\\') . '\\';
        $srcPath = str_replace('\\', '/', rtrim($srcPath, '/'));

        spl_autoload_register(function ($class) use ($namespace, $srcPath) {
            if (!str_starts_with($class, $namespace)) {
                return;
            }

            $relative = substr($class, strlen($namespace));
            $file = $srcPath . '/' . str_replace('\\', '/', $relative) . '.php';

            if (File::exists($file)) {
                require_once $file;
            }
        });
    }

    protected function bootSinglePlugin(Plugin $plugin): void
    {
        $this->registerPluginAutoloader($plugin);

        $metadata = $plugin->metadata();
        $providerClass = $metadata['provider'] ?? null;

        if ($providerClass && !class_exists($providerClass)) {
            $namespace = rtrim($metadata['namespace'] ?? '', '\\');
            if ($namespace && str_starts_with($providerClass, $namespace . '\\')) {
                $relative = substr($providerClass, strlen($namespace) + 1);
                $file = $plugin->path() . '/src/' . str_replace('\\', '/', $relative) . '.php';
                if (File::exists($file)) {
                    require_once $file;
                }
            }
        }

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
            $this->registerPluginAutoloader($plugin);
            $this->bootPlugin($plugin);

            \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => 'rj-content/plugins/' . $plugin->folder . '/database/migrations',
                '--force' => true,
            ]);

            $plugin->markAsActive();

            \App\Widgets\WidgetRegistry::reset();

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

            \App\Widgets\WidgetRegistry::reset();

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

            \App\Widgets\WidgetRegistry::reset();

            return ['success' => true, 'message' => __('Plugin deleted.')];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
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

        $base = realpath($plugin->path() . '/' . $assetsDir);

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

            $existing = Plugin::where('slug', $slug)->first();
            $oldMeta = $existing ? $existing->metadata() : [];

            if (isset($oldMeta['latest_version'])) {
                $manifest['latest_version'] = $oldMeta['latest_version'];
            }
            if (isset($oldMeta['download_url'])) {
                $manifest['download_url'] = $oldMeta['download_url'];
            }
            if (isset($oldMeta['is_prerelease'])) {
                $manifest['is_prerelease'] = $oldMeta['is_prerelease'];
            }

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
            $pluginUrl = $metadata['plugin_url'] ?? null;

            if (!$pluginUrl) continue;

            if (preg_match('/github\.com\/([^\/]+)\/([^\/]+)/', $pluginUrl, $matches)) {
                $repo = $matches[1] . '/' . $matches[2];

                try {
                    $headers = ['Accept' => 'application/vnd.github+json', 'User-Agent' => 'RJ-CMS'];
                    if ($token = rj_get_option('github_token')) {
                        $headers['Authorization'] = 'Bearer ' . $token;
                    }

                    $response = \Illuminate\Support\Facades\Http::timeout(10)->withHeaders($headers)->get("https://api.github.com/repos/{$repo}/releases");

                    if (!$response->successful() || empty($response->json())) continue;

                    $releases = $response->json();
                    $release = $releases[0];
                    $latestVersion = ltrim($release['tag_name'] ?? '', 'vV');
                    $isPrerelease = $release['prerelease'] ?? false;

                    if ($latestVersion && version_compare($latestVersion, $plugin->version, '>')) {
                        $zipUrl = null;
                        foreach ($release['assets'] ?? [] as $asset) {
                            if (str_ends_with(strtolower($asset['name'] ?? ''), '.zip')) {
                                $zipUrl = $asset['browser_download_url'];
                                break;
                            }
                        }

                        if ($zipUrl) {
                            $metadata['latest_version'] = $latestVersion;
                            $metadata['download_url'] = $zipUrl;
                            $metadata['is_prerelease'] = $isPrerelease;
                            $plugin->update(['metadata' => $metadata]);
                            $count++;
                        }
                    }
                } catch (\Throwable $e) {
                    continue;
                }
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

            $zip = new \ZipArchive();

            if ($zip->open($tmpFile) !== true) {
                return ['success' => false, 'message' => __('Could not open ZIP archive.')];
            }

            $extractPath = storage_path('app/plugin-temp/' . uniqid('plugin_update_'));
            File::makeDirectory($extractPath, 0755, true);
            $zip->extractTo($extractPath);
            $zip->close();
            @unlink($tmpFile);

            $manifestPath = $this->findManifest($extractPath);

            if (!$manifestPath) {
                File::deleteDirectory($extractPath);
                return ['success' => false, 'message' => __('plugin.json not found in archive.')];
            }

            $newManifest = json_decode(File::get($manifestPath), true);

            if (!is_array($newManifest) || empty($newManifest['name']) || empty($newManifest['main'])) {
                File::deleteDirectory($extractPath);
                return ['success' => false, 'message' => __('Invalid plugin.json.')];
            }

            $sourceDir = dirname($manifestPath);
            $targetDir = $plugin->path();

            if (File::isDirectory($targetDir)) {
                File::deleteDirectory($targetDir);
            }

            File::moveDirectory($sourceDir, $targetDir, true);
            File::deleteDirectory($extractPath);

            $newMeta = array_merge($metadata, $newManifest);

            unset($newMeta['latest_version']);
            unset($newMeta['download_url']);
            unset($newMeta['is_prerelease']);

            $plugin->update([
                'version' => $newManifest['version'] ?? $plugin->version,
                'metadata' => $newMeta,
            ]);

            Artisan::call('migrate', ['--force' => true]);

            \App\Widgets\WidgetRegistry::reset();

            return ['success' => true, 'message' => __('Plugin updated successfully to v:version', ['version' => $plugin->version])];
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
