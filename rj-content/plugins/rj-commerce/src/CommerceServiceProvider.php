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
    }
}
