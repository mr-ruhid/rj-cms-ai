<?php

namespace App\Providers;

use App\Hooks\HookRegistry;
use App\Services\PluginManager;
use App\Services\ThemeManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, fn($app) => new ThemeManager());
        $this->app->singleton(PluginManager::class, fn($app) => new PluginManager());
        $this->app->singleton(HookRegistry::class);

        require_once app_path('Helpers/plugins.php');
    }

    public function boot(): void
    {
        // Xəta olsa ekranda görünsün deyə try-catch-i sildik
        $this->app->make(ThemeManager::class)->registerViews();
        $this->app->make(PluginManager::class)->boot();
    }
}
