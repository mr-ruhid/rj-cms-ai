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
                'name' => 'direction',
                'type' => 'select',
                'label' => __('Direction'),
                'default' => 'column',
                'options' => [
                    'column' => __('Vertical (stack)'),
                    'row' => __('Horizontal (side by side)'),
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
                'label' => __('Alignment'),
                'default' => 'stretch',
                'options' => [
                    'start' => __('Start'),
                    'center' => __('Center'),
                    'end' => __('End'),
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
        return '<div class="rj-container-placeholder p-4 text-center text-xs text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">' . $this->escape(__('Container — add widgets inside')) . '</div>';
    }
}