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
        $baseUrl = 'https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/';

        return [
            [
                'name' => 'announcement',
                'type' => 'text',
                'label' => __('Announcement Bar Text'),
                'default' => __('Enjoy 2 samples and complimentary shipping with every order.'),
            ],
            [
                'name' => 'style',
                'type' => 'image_select',
                'label' => __('Header Style'),
                'default' => 'style-1',
                'options' => [
                    'style-1' => [
                        'image' => $baseUrl . 'header1.png',
                        'label' => __('Style 1 (Luxury Dark)'),
                    ],
                    'style-2' => [
                        'image' => $baseUrl . 'header2.png',
                        'label' => __('Style 2 (Elegant Light)'),
                    ],
                    'style-3' => [
                        'image' => $baseUrl . 'header3.png',
                        'label' => __('Style 3 (Editorial)'),
                    ],
                ],
            ],
            [
                'name' => 'logo',
                'type' => 'media',
                'label' => __('Custom Logo (Override)'),
                'default' => '',
            ],
        ];
    }

    public function render(array $settings): string
    {
        $style = $settings['style'] ?? 'style-1';
        $customLogo = $settings['logo'] ?? '';
        $announcement = $settings['announcement'] ?? rj_get_option('header_announcement', __('Enjoy 2 samples and complimentary shipping with every order.'));

        $view = 'theme::partials.headers.' . $style;

        if (!view()->exists($view)) {
            $view = 'theme::partials.headers.style-1';
        }

        return view($view, compact('customLogo', 'announcement'))->render();
    }
}
