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
                'tab' => 'content',
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
                'tab' => 'content',
                'options' => [
                    '1' => '1 ' . __('Column'),
                    '2' => '2 ' . __('Columns'),
                    '3' => '3 ' . __('Columns'),
                    '4' => '4 ' . __('Columns'),
                    '6' => '6 ' . __('Columns'),
                ],
            ],
            [
                'name' => 'column_widths',
                'type' => 'text',
                'label' => __('Column Widths'),
                'default' => '',
                'tab' => 'content',
                'placeholder' => '70,30',
                'help' => __('Comma-separated percentages, e.g. 70,30. Leave empty for equal columns.'),
            ],
            [
                'name' => 'flex_direction',
                'type' => 'select',
                'label' => __('Direction'),
                'default' => 'row',
                'tab' => 'content',
                'options' => [
                    'row' => __('Row'),
                    'column' => __('Column'),
                    'row-reverse' => __('Row Reverse'),
                    'column-reverse' => __('Column Reverse'),
                ],
            ],
            [
                'name' => 'justify_content',
                'type' => 'select',
                'label' => __('Justify Content'),
                'default' => 'start',
                'tab' => 'content',
                'options' => [
                    'start' => __('Start'),
                    'center' => __('Center'),
                    'end' => __('End'),
                    'space-between' => __('Space Between'),
                    'space-around' => __('Space Around'),
                    'space-evenly' => __('Space Evenly'),
                ],
            ],
            [
                'name' => 'align_items',
                'type' => 'select',
                'label' => __('Align Items'),
                'default' => 'stretch',
                'tab' => 'content',
                'options' => [
                    'start' => __('Top'),
                    'center' => __('Center'),
                    'end' => __('Bottom'),
                    'stretch' => __('Stretch'),
                    'baseline' => __('Baseline'),
                ],
            ],
            [
                'name' => 'flex_wrap',
                'type' => 'select',
                'label' => __('Wrap'),
                'default' => 'wrap',
                'tab' => 'content',
                'options' => [
                    'nowrap' => __('No Wrap'),
                    'wrap' => __('Wrap'),
                    'wrap-reverse' => __('Wrap Reverse'),
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
            [
                'name' => 'min_height',
                'type' => 'select',
                'label' => __('Min Height'),
                'default' => 'auto',
                'tab' => 'style',
                'options' => [
                    'auto' => __('Auto'),
                    'sm' => '200px',
                    'md' => '400px',
                    'lg' => '600px',
                    'xl' => '800px',
                    'screen' => '100vh',
                    'custom' => __('Custom'),
                ],
            ],
            [
                'name' => 'min_height_custom',
                'type' => 'text',
                'label' => __('Custom Min Height'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 500px, 80vh',
            ],
            [
                'name' => 'border',
                'type' => 'select',
                'label' => __('Border'),
                'default' => 'none',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'solid' => __('Solid'),
                    'dashed' => __('Dashed'),
                ],
            ],
            [
                'name' => 'border_radius',
                'type' => 'select',
                'label' => __('Border Radius'),
                'default' => 'md',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'sm' => '4px',
                    'md' => '8px',
                    'lg' => '16px',
                    'full' => __('Full'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        return '';
    }
}