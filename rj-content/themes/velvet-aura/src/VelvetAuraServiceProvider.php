<?php

namespace VelvetAura;

use Illuminate\Support\ServiceProvider;
use VelvetAura\Widgets\HeroSliderWidget;
use VelvetAura\Widgets\FeaturedProductsWidget;
use VelvetAura\Widgets\VelvetHeaderWidget;
use VelvetAura\Widgets\VelvetFooterWidget;

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
                $widgets = [
                    new HeroSliderWidget(),
                    new FeaturedProductsWidget(),
                    new VelvetHeaderWidget(),
                    new VelvetFooterWidget(),
                ];
                return array_merge($existing, $widgets);
            });
        }
    }
}
