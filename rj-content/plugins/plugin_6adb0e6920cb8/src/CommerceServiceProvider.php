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

        $shortcodesFile = __DIR__ . '/Shortcodes.php';
        if (file_exists($shortcodesFile)) {
            require_once $shortcodesFile;
        }

        $helpersFile = __DIR__ . '/CommerceHelpers.php';
        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }
}
