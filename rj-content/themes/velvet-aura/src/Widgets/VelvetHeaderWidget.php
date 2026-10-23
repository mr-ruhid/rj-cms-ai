<?php

namespace VelvetAura\Widgets;

use App\Widgets\BaseWidget;

class VelvetHeaderWidget extends BaseWidget
{
    public function id(): string
    {
        return 'velvet-header';
    }

    public function name(): string
    {
        return __('Velvet Header');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>';
    }

    public function category(): string
    {
        return 'velvet-aura';
    }

    public function description(): string
    {
        return __('Load a static header and optionally override the logo.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'style',
                'type' => 'select',
                'label' => __('Header Style'),
                'default' => 'style-1',
                'options' => [
                    'style-1' => __('Style 1 (Luxury Dark)'),
                    'style-2' => __('Style 2 (Elegant Light)'),
                    'style-3' => __('Style 3 (Editorial)'),
                ],
            ],
            [
                'name' => 'logo',
                'type' => 'image',
                'label' => __('Custom Logo (Override)'),
                'default' => '',
            ],
        ];
    }

    public function render(array $settings): string
    {
        $style = $settings['style'] ?? 'style-1';
        $customLogo = $settings['logo'] ?? '';

        $view = 'theme::partials.headers.' . $style;

        if (!view()->exists($view)) {
            $view = 'theme::partials.headers.style-1';
        }

        return view($view, compact('customLogo'))->render();
    }
}
