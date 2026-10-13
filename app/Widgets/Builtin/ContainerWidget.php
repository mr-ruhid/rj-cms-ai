<?php

namespace App\Widgets\Builtin;

use App\Widgets\BaseWidget;

class ContainerWidget extends BaseWidget
{
    public function id(): string
    {
        return 'container';
    }

    public function name(): string
    {
        return __('Container');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"/></svg>';
    }

    public function category(): string
    {
        return 'layout';
    }

    public function description(): string
    {
        return __('Group widgets into rows or columns.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'width',
                'type' => 'select',
                'label' => __('Width'),
                'default' => 'boxed',
                'options' => [
                    'boxed' => __('Boxed') . ' — ' . __('Centered with max width'),
                    'full' => __('Full Width') . ' — ' . __('Edge to edge'),
                ],
            ],
            [
                'name' => 'columns',
                'type' => 'select',
                'label' => __('Columns'),
                'default' => '1',
                'options' => [
                    '1' => '1 ' . __('Column'),
                    '2' => '2 ' . __('Columns'),
                    '3' => '3 ' . __('Columns'),
                    '4' => '4 ' . __('Columns'),
                    '6' => '6 ' . __('Columns'),
                ],
            ],
            [
                'name' => 'gap',
                'type' => 'select',
                'label' => __('Gap'),
                'default' => 'md',
                'options' => [
                    'none' => __('None'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
            ],
            [
                'name' => 'padding',
                'type' => 'select',
                'label' => __('Padding'),
                'default' => 'md',
                'options' => [
                    'none' => __('None'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
            ],
            [
                'name' => 'background',
                'type' => 'color',
                'label' => __('Background'),
                'default' => '',
            ],
            [
                'name' => 'align',
                'type' => 'select',
                'label' => __('Vertical Align'),
                'default' => 'stretch',
                'options' => [
                    'start' => __('Top'),
                    'center' => __('Center'),
                    'end' => __('Bottom'),
                    'stretch' => __('Stretch'),
                ],
            ],
            [
                'name' => 'min_height',
                'type' => 'select',
                'label' => __('Min Height'),
                'default' => 'auto',
                'options' => [
                    'auto' => __('Auto'),
                    'sm' => '200px',
                    'md' => '400px',
                    'lg' => '600px',
                ],
            ],
            [
                'name' => 'border',
                'type' => 'select',
                'label' => __('Border'),
                'default' => 'none',
                'options' => [
                    'none' => __('None'),
                    'solid' => __('Solid'),
                    'dashed' => __('Dashed'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        return '';
    }
}