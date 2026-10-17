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
        return $append === ''
            ? $this->pluginPath
            : $this->pluginPath . '/' . ltrim($append, '/');
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
        $this->registerRoutes();
        $this->registerHooks();
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

        if (empty($items)) {
            return;
        }

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

        if (empty($widgets)) {
            return;
        }

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
            'shopping-cart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>',
            'chart-bar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
            'cog' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
            'mail' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
            'credit-card' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
            'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
        ];

        $path = $paths[$name] ?? $paths['cog'];

        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">' . $path . '</svg>';
    }
}