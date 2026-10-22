<?php

namespace RjCommerce\Providers;

use Illuminate\Support\ServiceProvider;

class CommerceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $basePath = dirname(__DIR__, 2);

        $this->loadRoutesFrom($basePath . '/routes/web.php');
        $this->loadRoutesFrom($basePath . '/routes/admin.php');
        $this->loadViewsFrom($basePath . '/resources/views', 'rj-commerce');
        $this->loadMigrationsFrom($basePath . '/database/migrations');

        $this->registerAdminMenu();
    }

    protected function registerAdminMenu(): void
    {
        add_filter('admin.menu', function ($items) {
            $items[] = [
                'id' => 'rj-commerce',
                'title' => __('Commerce'),
                'route' => 'admin.commerce.products.index',
                'icon_svg' => '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                'position' => 25,
                'children' => [
                    [
                        'title' => __('Products'),
                        'route' => 'admin.commerce.products.index',
                    ],
                    [
                        'title' => __('Add New'),
                        'route' => 'admin.commerce.products.create',
                    ],
                ],
            ];
            return $items;
        });
    }
}
