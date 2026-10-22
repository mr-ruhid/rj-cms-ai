<?php

namespace RjCommerce;

use Illuminate\Support\ServiceProvider;

class CommerceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $basePath = dirname(__DIR__);

        $this->loadRoutesFrom($basePath . '/routes/web.php');
        $this->loadRoutesFrom($basePath . '/routes/admin.php');
        $this->loadViewsFrom($basePath . '/resources/views', 'rj-commerce');
        $this->loadMigrationsFrom($basePath . '/database/migrations');
    }
}
