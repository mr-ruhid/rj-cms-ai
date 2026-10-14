<?php

namespace App\Widgets\Builtin;

use App\Widgets\BaseWidget;

class MenuWidget extends BaseWidget
{
    public function id(): string
    {
        return 'menu';
    }

    public function name(): string
    {
        return __('Menu');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>';
    }

    public function category(): string
    {
        return 'header';
    }

    public function description(): string
    {
        return __('Display a navigation menu.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'menu_id',
                'type' => 'menu',
                'label' => __('Menu'),
                'default' => '',
                'tab' => 'content',
            ],
            [
                'name' => 'layout',
                'type' => 'select',
                'label' => __('Layout'),
                'default' => 'horizontal',
                'tab' => 'content',
                'options' => [
                    'horizontal' => __('Horizontal'),
                    'vertical' => __('Vertical'),
                ],
            ],
            [
                'name' => 'align',
                'type' => 'select',
                'label' => __('Alignment'),
                'default' => 'right',
                'tab' => 'content',
                'options' => [
                    'left' => __('Left'),
                    'center' => __('Center'),
                    'right' => __('Right'),
                ],
            ],
            [
                'name' => 'mobile_behavior',
                'type' => 'select',
                'label' => __('Mobile Behavior'),
                'default' => 'hamburger',
                'tab' => 'content',
                'options' => [
                    'hamburger' => __('Hamburger'),
                    'stack' => __('Stack'),
                    'scroll' => __('Scroll'),
                ],
            ],
            [
                'name' => 'gap',
                'type' => 'select',
                'label' => __('Gap'),
                'default' => 'md',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
            ],
            [
                'name' => 'font_size',
                'type' => 'select',
                'label' => __('Font Size'),
                'default' => 'base',
                'tab' => 'style',
                'options' => [
                    'sm' => __('Small'),
                    'base' => __('Normal'),
                    'lg' => __('Large'),
                ],
            ],
            [
                'name' => 'font_weight',
                'type' => 'select',
                'label' => __('Font Weight'),
                'default' => '500',
                'tab' => 'style',
                'options' => [
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                ],
            ],
            [
                'name' => 'text_color',
                'type' => 'color',
                'label' => __('Text Color'),
                'default' => '#111827',
                'tab' => 'style',
            ],
            [
                'name' => 'hover_color',
                'type' => 'color',
                'label' => __('Hover Color'),
                'default' => '#7c3aed',
                'tab' => 'style',
            ],
            [
                'name' => 'hover_style',
                'type' => 'select',
                'label' => __('Hover Style'),
                'default' => 'underline',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'underline' => __('Underline'),
                    'background' => __('Background'),
                    'both' => __('Both'),
                ],
            ],
            [
                'name' => 'active_color',
                'type' => 'color',
                'label' => __('Active Color'),
                'default' => '#7c3aed',
                'tab' => 'style',
            ],
            [
                'name' => 'dropdown_bg',
                'type' => 'color',
                'label' => __('Dropdown Background'),
                'default' => '#ffffff',
                'tab' => 'style',
            ],
            [
                'name' => 'dropdown_border',
                'type' => 'select',
                'label' => __('Dropdown Border'),
                'default' => 'yes',
                'tab' => 'style',
                'options' => [
                    'yes' => __('Yes'),
                    'no' => __('No'),
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