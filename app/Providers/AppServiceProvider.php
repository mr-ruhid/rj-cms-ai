<?php

namespace App\Providers;

use App\Services\ThemeManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeManager::class, fn($app) => new ThemeManager());
    }

    public function boot(): void
    {
        try {
            $this->app->make(ThemeManager::class)->registerViews();
        } catch (\Throwable $e) {
        }
    }
}
