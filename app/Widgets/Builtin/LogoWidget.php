<?php

namespace App\Widgets\Builtin;

use App\Widgets\BaseWidget;

class LogoWidget extends BaseWidget
{
    public function id(): string
    {
        return 'logo';
    }

    public function name(): string
    {
        return __('Logo');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/></svg>';
    }

    public function category(): string
    {
        return 'header';
    }

    public function description(): string
    {
        return __('Site logo with link.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'url',
                'type' => 'media',
                'label' => __('Logo Image'),
                'default' => '',
                'tab' => 'content',
            ],
            [
                'name' => 'alt',
                'type' => 'text',
                'label' => __('Alt Text'),
                'default' => '',
                'tab' => 'content',
                'placeholder' => __('Site name or brand'),
            ],
            [
                'name' => 'link',
                'type' => 'url',
                'label' => __('Link'),
                'default' => '/',
                'tab' => 'content',
                'placeholder' => '/',
            ],
            [
                'name' => 'target',
                'type' => 'select',
                'label' => __('Link Target'),
                'default' => '_self',
                'tab' => 'content',
                'options' => [
                    '_self' => __('Same Tab'),
                    '_blank' => __('New Tab'),
                ],
            ],
            [
                'name' => 'width',
                'type' => 'select',
                'label' => __('Width'),
                'default' => 'auto',
                'tab' => 'style',
                'options' => [
                    'auto' => __('Auto'),
                    'xs' => '80px',
                    'sm' => '120px',
                    'md' => '160px',
                    'lg' => '200px',
                    'xl' => '280px',
                    'custom' => __('Custom'),
                ],
            ],
            [
                'name' => 'custom_width',
                'type' => 'text',
                'label' => __('Custom Width'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 240px, 15rem',
            ],
            [
                'name' => 'height',
                'type' => 'select',
                'label' => __('Height'),
                'default' => 'auto',
                'tab' => 'style',
                'options' => [
                    'auto' => __('Auto'),
                    'xs' => '32px',
                    'sm' => '48px',
                    'md' => '64px',
                    'lg' => '80px',
                    'xl' => '120px',
                ],
            ],
            [
                'name' => 'align',
                'type' => 'select',
                'label' => __('Alignment'),
                'default' => 'left',
                'tab' => 'style',
                'options' => [
                    'left' => __('Left'),
                    'center' => __('Center'),
                    'right' => __('Right'),
                ],
            ],
            [
                'name' => 'padding_dim',
                'type' => 'dimensions',
                'label' => __('Padding'),
                'default' => ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'linked' => false],
                'tab' => 'style',
            ],
            [
                'name' => 'margin_dim',
                'type' => 'dimensions',
                'label' => __('Margin'),
                'default' => ['top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'linked' => false],
                'tab' => 'style',
            ],
        ];
    }

    public function render(array $settings): string
    {
        return '';
    }
}