<?php

namespace RjCommerce;

use App\Plugins\PluginServiceProvider;
use App\Widgets\WidgetRegistry;
use RjCommerce\Widgets\ProductGridWidget;
use RjCommerce\Widgets\CategoriesWidget;
use RjCommerce\Widgets\SingleProductWidget;
use RjCommerce\Widgets\CartWidget;

class CommerceServiceProvider extends PluginServiceProvider
{
    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();

        $shortcodesFile = __DIR__ . '/Shortcodes.php';
        if (file_exists($shortcodesFile)) {
            require_once $shortcodesFile;
        }

        $helpersFile = __DIR__ . '/CommerceHelpers.php';
        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }

        WidgetRegistry::register(new ProductGridWidget());
        WidgetRegistry::register(new CategoriesWidget());
        WidgetRegistry::register(new SingleProductWidget());
        WidgetRegistry::register(new CartWidget());
    }
}
