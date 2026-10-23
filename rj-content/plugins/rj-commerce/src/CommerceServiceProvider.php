<?php

namespace RjCommerce;

use App\Plugins\PluginServiceProvider;

class CommerceServiceProvider extends PluginServiceProvider
{
    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();
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
                'position' => 20,
            ];

            $items[] = ['id' => 'rj-commerce-products', 'parent' => 'rj-commerce', 'title' => __('Products'), 'route' => 'admin.commerce.products.index', 'position' => 21];
            $items[] = ['id' => 'rj-commerce-orders', 'parent' => 'rj-commerce', 'title' => __('Orders'), 'route' => 'admin.commerce.orders.index', 'position' => 22];
            $items[] = ['id' => 'rj-commerce-coupons', 'parent' => 'rj-commerce', 'title' => __('Coupons'), 'route' => 'admin.commerce.coupons.index', 'position' => 23];
            $items[] = ['id' => 'rj-commerce-reviews', 'parent' => 'rj-commerce', 'title' => __('Reviews'), 'route' => 'admin.commerce.reviews.index', 'position' => 24];
            $items[] = ['id' => 'rj-commerce-shipping', 'parent' => 'rj-commerce', 'title' => __('Shipping'), 'route' => 'admin.commerce.shipping.zones.index', 'position' => 25];
            $items[] = ['id' => 'rj-commerce-categories', 'parent' => 'rj-commerce', 'title' => __('Categories'), 'route' => 'admin.commerce.categories.index', 'position' => 26];
            $items[] = ['id' => 'rj-commerce-brands', 'parent' => 'rj-commerce', 'title' => __('Brands'), 'route' => 'admin.commerce.brands.index', 'position' => 27];
            $items[] = ['id' => 'rj-commerce-attributes', 'parent' => 'rj-commerce', 'title' => __('Attributes'), 'route' => 'admin.commerce.attributes.index', 'position' => 28];
            $items[] = ['id' => 'rj-commerce-tags', 'parent' => 'rj-commerce', 'title' => __('Tags'), 'route' => 'admin.commerce.tags.index', 'position' => 29];
            $items[] = ['id' => 'rj-commerce-settings', 'parent' => 'rj-commerce', 'title' => __('Settings'), 'route' => 'admin.commerce.settings.index', 'position' => 30];

            return $items;
        });
    }
}
