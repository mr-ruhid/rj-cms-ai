<?php

namespace VelvetAura\Widgets;

use App\Widgets\BaseWidget;

class HeroSliderWidget extends BaseWidget
{
    public function id(): string
    {
        return 'velvet-hero-slider';
    }

    public function name(): string
    {
        return __('Velvet Hero Slider');
    }

    public function icon(): string
    {
        return '<svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
    }

    public function category(): string
    {
        return 'velvet-aura';
    }

    public function description(): string
    {
        return __('A full-width luxury hero section with background image, title, and CTA button.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'bg_image',
                'type' => 'image',
                'label' => __('Background Image'),
                'default' => '',
            ],
            [
                'name' => 'title',
                'type' => 'text',
                'label' => __('Main Title'),
                'default' => __('The Cocktails Collection'),
            ],
            [
                'name' => 'subtitle',
                'type' => 'text',
                'label' => __('Subtitle'),
                'default' => __('Perfumes inspired by the art of mixology'),
            ],
            [
                'name' => 'button_text',
                'type' => 'text',
                'label' => __('Button Text'),
                'default' => __('Discover The Collection'),
            ],
            [
                'name' => 'button_url',
                'type' => 'text',
                'label' => __('Button URL'),
                'default' => '/shop',
            ],
            [
                'name' => 'height',
                'type' => 'select',
                'label' => __('Section Height'),
                'default' => 'min-h-[80vh]',
                'options' => [
                    'min-h-[60vh]' => __('Medium'),
                    'min-h-[80vh]' => __('Large'),
                    'min-h-screen' => __('Full Screen'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        return view('theme::widgets.hero-slider', compact('settings'))->render();
    }
}
