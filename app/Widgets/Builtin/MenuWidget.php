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
        return __('Navigation menu with hover effects and dropdown support.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'menu_id',
                'type' => 'menu',
                'label' => __('Menu'),
                'default' => '',
            ],
            [
                'name' => 'layout',
                'type' => 'select',
                'label' => __('Layout'),
                'default' => 'horizontal',
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
                'options' => [
                    'left' => __('Left'),
                    'center' => __('Center'),
                    'right' => __('Right'),
                ],
            ],
            [
                'name' => 'gap',
                'type' => 'select',
                'label' => __('Gap between items'),
                'default' => 'md',
                'options' => [
                    'none' => __('None'),
                    'sm' => __('Small — 12px'),
                    'md' => __('Medium — 24px'),
                    'lg' => __('Large — 36px'),
                    'xl' => __('Extra Large — 48px'),
                ],
            ],
            [
                'name' => 'font_size',
                'type' => 'select',
                'label' => __('Font Size'),
                'default' => 'base',
                'options' => [
                    'sm' => __('Small — 13px'),
                    'base' => __('Normal — 15px'),
                    'lg' => __('Large — 17px'),
                ],
            ],
            [
                'name' => 'font_weight',
                'type' => 'select',
                'label' => __('Font Weight'),
                'default' => '500',
                'options' => [
                    '400' => __('Normal'),
                    '500' => __('Medium'),
                    '600' => __('Semibold'),
                    '700' => __('Bold'),
                ],
            ],
            [
                'name' => 'text_color',
                'type' => 'color',
                'label' => __('Text Color'),
                'default' => '#111827',
            ],
            [
                'name' => 'hover_color',
                'type' => 'color',
                'label' => __('Hover Color'),
                'default' => '#7c3aed',
            ],
            [
                'name' => 'hover_style',
                'type' => 'select',
                'label' => __('Hover Style'),
                'default' => 'underline',
                'options' => [
                    'none' => __('None'),
                    'underline' => __('Underline'),
                    'background' => __('Background'),
                    'both' => __('Underline + Background'),
                ],
            ],
            [
                'name' => 'active_color',
                'type' => 'color',
                'label' => __('Active Link Color'),
                'default' => '#7c3aed',
            ],
            [
                'name' => 'dropdown_bg',
                'type' => 'color',
                'label' => __('Dropdown Background'),
                'default' => '#ffffff',
            ],
            [
                'name' => 'dropdown_border',
                'type' => 'select',
                'label' => __('Dropdown Border'),
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes'),
                    'no' => __('No'),
                ],
            ],
            [
                'name' => 'mobile_behavior',
                'type' => 'select',
                'label' => __('Mobile Behavior'),
                'default' => 'hamburger',
                'options' => [
                    'hamburger' => __('Hamburger menu'),
                    'stack' => __('Stack vertically'),
                    'scroll' => __('Horizontal scroll'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        return '';
    }
}