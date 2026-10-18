<?php

namespace App\Plugins;

use Illuminate\Support\ServiceProvider;

abstract class PluginServiceProvider extends ServiceProvider
{
    protected string $pluginPath;
    protected array $manifest;

    public function __construct($app, string $pluginPath, array $manifest)
    {
        parent::__construct($app);
        $this->pluginPath = $pluginPath;
        $this->manifest = $manifest;
    }

    public function slug(): string
    {
        return $this->manifest['slug'] ?? basename($this->pluginPath);
    }

    public function namespace(): string
    {
        return $this->manifest['namespace'] ?? '';
    }

    public function path(string $append = ''): string
    {
        return $append === '' ? $this->pluginPath : $this->pluginPath . '/' . ltrim($append, '/');
    }

    public function manifest(): array
    {
        return $this->manifest;
    }

    public function register(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->registerMigrations();
    }

    public function boot(): void
    {
        // ƏVVƏLCƏ HOOK-LARI QEYDİYYATDAN KEÇİRİRİK (Menyu və Widget)
        $this->registerHooks();

        // SONRA ROUTE-LARI YÜKLƏYİRİK
        $this->registerRoutes();
    }

    protected function registerConfig(): void
    {
        $configFile = $this->path('config/config.php');
        if (file_exists($configFile)) {
            $this->mergeConfigFrom($configFile, 'plugins.' . $this->slug());
        }
    }

    protected function registerViews(): void
    {
        $views = $this->manifest['provides']['views'] ?? 'views';
        if (is_dir($this->path($views))) {
            $this->loadViewsFrom($this->path($views), 'plugin-' . $this->slug());
        }
    }

    protected function registerMigrations(): void
    {
        $migrations = $this->manifest['provides']['migrations'] ?? null;
        if ($migrations && is_dir($this->path($migrations))) {
            $this->loadMigrationsFrom($this->path($migrations));
        }
    }

    protected function registerRoutes(): void
    {
        $routes = $this->manifest['provides']['routes'] ?? [];

        if (!empty($routes['admin']) && file_exists($this->path($routes['admin']))) {
            $this->loadRoutesFrom($this->path($routes['admin']));
        }

        if (!empty($routes['frontend']) && file_exists($this->path($routes['frontend']))) {
            $this->loadRoutesFrom($this->path($routes['frontend']));
        }
    }

    protected function registerHooks(): void
    {
        $this->registerAdminMenu();
        $this->registerBuilderWidgets();
    }

    protected function registerAdminMenu(): void
    {
        $items = $this->manifest['provides']['admin_menu'] ?? [];

        if (empty($items)) return;

        $slug = $this->slug();

        rj_add_filter('admin.menu', function ($existing) use ($items, $slug) {
            foreach ($items as $item) {
                $item['plugin'] = $slug;
                if (empty($item['icon_svg']) && !empty($item['icon'])) {
                    $item['icon_svg'] = $this->iconSvg($item['icon']);
                }
                $existing[] = $item;
            }
            return $existing;
        });
    }

    protected function registerBuilderWidgets(): void
    {
        $widgets = $this->manifest['provides']['widgets'] ?? [];

        if (empty($widgets)) return;

        rj_add_filter('builder.widgets', function ($existing) use ($widgets) {
            foreach ($widgets as $class) {
                if (!is_string($class) || !class_exists($class)) {
                    continue;
                }
                try {
                    $instance = new $class();
                    if ($instance instanceof \App\Widgets\BaseWidget) {
                        $existing[] = $instance;
                    }
                } catch (\Throwable $e) {
                    continue;
                }
            }
            return $existing;
        });
    }

    protected function iconSvg(string $name): string
    {
        $paths = [
            'clipboard-list' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
            'inbox' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>',
            'cog' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        ];

        $path = $paths[$name] ?? $paths['cog'];

        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">' . $path . '</svg>';
    }
}
