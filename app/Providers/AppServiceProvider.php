<?php

namespace App\Providers;

use App\Services\PluginManager;
use App\Services\ThemeManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, fn($app) => new ThemeManager());
        $this->app->singleton(PluginManager::class, fn($app) => new PluginManager());

        require_once app_path('Helpers/plugins.php');
    }

    public function boot(): void
    {
        try {
            $this->app->make(ThemeManager::class)->registerViews();
        } catch (\Throwable $e) {
        }

        try {
            $this->app->make(PluginManager::class)->boot();
        } catch (\Throwable $e) {
        }
    }
}