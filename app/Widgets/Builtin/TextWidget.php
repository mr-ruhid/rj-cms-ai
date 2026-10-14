<?php

namespace App\Widgets\Builtin;

use App\Widgets\BaseWidget;

class TextWidget extends BaseWidget
{
    public function id(): string
    {
        return 'text';
    }

    public function name(): string
    {
        return __('Text');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"/></svg>';
    }

    public function category(): string
    {
        return 'basic';
    }

    public function description(): string
    {
        return __('Add a paragraph of text.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'text',
                'type' => 'textarea',
                'label' => __('Text'),
                'default' => __('Write something here.'),
                'tab' => 'content',
                'placeholder' => __('Enter your text'),
            ],
            [
                'name' => 'size',
                'type' => 'select',
                'label' => __('Size'),
                'default' => 'base',
                'tab' => 'style',
                'options' => [
                    'sm' => __('Small'),
                    'base' => __('Normal'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
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
                    'justify' => __('Justify'),
                ],
            ],
            [
                'name' => 'color',
                'type' => 'color',
                'label' => __('Text Color'),
                'default' => '#374151',
                'tab' => 'style',
            ],
            [
                'name' => 'font_size',
                'type' => 'text',
                'label' => __('Font Size'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 16px, 1rem',
            ],
            [
                'name' => 'font_weight',
                'type' => 'select',
                'label' => __('Font Weight'),
                'default' => '400',
                'tab' => 'style',
                'options' => [
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                ],
            ],
            [
                'name' => 'line_height',
                'type' => 'text',
                'label' => __('Line Height'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 1.6, 24px',
            ],
            [
                'name' => 'letter_spacing',
                'type' => 'text',
                'label' => __('Letter Spacing'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 0.5px, 0.02em',
            ],
            [
                'name' => 'text_transform',
                'type' => 'select',
                'label' => __('Text Transform'),
                'default' => 'none',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'uppercase' => __('Uppercase'),
                    'lowercase' => __('Lowercase'),
                    'capitalize' => __('Capitalize'),
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