<?php

namespace VelvetAura;

use Illuminate\Support\ServiceProvider;

class VelvetAuraServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        $this->registerWidgets();
    }

    protected function registerWidgets(): void
    {
        if (function_exists('rj_add_filter')) {
            rj_add_filter('builder.widgets', function ($existing) {
                $widgets = [];
                return array_merge($existing, $widgets);
            });
        }
    }
}
