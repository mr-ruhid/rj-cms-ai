<?php

namespace VelvetAura\Widgets;

use App\Widgets\BaseWidget;

class VelvetFooterWidget extends BaseWidget
{
    public function id(): string
    {
        return 'velvet-footer';
    }

    public function name(): string
    {
        return __('Velvet Footer');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>';
    }

    public function category(): string
    {
        return 'velvet-aura';
    }

    public function description(): string
    {
        return __('Load a static footer and optionally override the content.');
    }

    public function schema(): array
    {
        $baseUrl = 'https://raw.githubusercontent.com/rj-nexus/velvet-aura/main/assets/images/';

        return [
            [
                'name' => 'description',
                'type' => 'textarea',
                'label' => __('Company Description'),
                'default' => __('Curating exceptional fragrances and premium essentials for the modern individual. Experience the art of fine living.'),
            ],
            [
                'name' => 'copyright',
                'type' => 'text',
                'label' => __('Copyright Text'),
                'default' => '© ' . date('Y') . ' VelvetAura. All rights reserved.',
            ],
            [
                'name' => 'bg_text',
                'type' => 'text',
                'label' => __('Background Text (Style 3 only)'),
                'default' => 'VelvetAura',
            ],
            [
                'name' => 'style',
                'type' => 'image_select',
                'label' => __('Footer Style'),
                'default' => 'style-1',
                'options' => [
                    'style-1' => [
                        'image' => $baseUrl . 'footer1.png',
                        'label' => __('Style 1 (Dark Luxury)'),
                    ],
                    'style-2' => [
                        'image' => $baseUrl . 'footer2.png',
                        'label' => __('Style 2 (Light Minimal)'),
                    ],
                    'style-3' => [
                        'image' => $baseUrl . 'footer3.png',
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

        $view = 'theme::partials.footers.' . $style;

        if (!view()->exists($view)) {
            $view = 'theme::partials.footers.style-1';
        }

        return view($view, compact('customLogo', 'settings'))->render();
    }
}
