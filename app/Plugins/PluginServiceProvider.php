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
    }
}