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
        $text = $settings['text'] ?? '';

        if ($text === '') {
            return '';
        }

        $sizes = [
            'sm' => '0.875rem',
            'base' => '1rem',
            'lg' => '1.125rem',
            'xl' => '1.25rem',
        ];

        $styles = [];

        if (!empty($settings['color'])) {
            $styles[] = 'color:' . $this->escape($settings['color']);
        }

        if (!empty($settings['font_size'])) {
            $styles[] = 'font-size:' . $this->escape($settings['font_size']);
        } elseif (!empty($settings['size']) && isset($sizes[$settings['size']])) {
            $styles[] = 'font-size:' . $sizes[$settings['size']];
        }

        if (!empty($settings['font_weight'])) {
            $styles[] = 'font-weight:' . $this->escape($settings['font_weight']);
        }

        if (!empty($settings['line_height'])) {
            $styles[] = 'line-height:' . $this->escape($settings['line_height']);
        } else {
            $styles[] = 'line-height:1.6';
        }

        if (!empty($settings['letter_spacing'])) {
            $styles[] = 'letter-spacing:' . $this->escape($settings['letter_spacing']);
        }

        if (!empty($settings['text_transform']) && $settings['text_transform'] !== 'none') {
            $styles[] = 'text-transform:' . $this->escape($settings['text_transform']);
        }

        if (!empty($settings['align'])) {
            $styles[] = 'text-align:' . $this->escape($settings['align']);
        }

        $padding = $this->dimensionCss($settings, 'padding_dim');
        if ($padding !== '') {
            $styles[] = 'padding:' . $padding;
        }

        $margin = $this->dimensionCss($settings, 'margin_dim');
        if ($margin !== '') {
            $styles[] = 'margin:' . $margin;
        }

        $bg = $this->backgroundCss($settings);
        if ($bg !== '') {
            $styles[] = rtrim($bg, ';');
        }

        $shadow = $this->boxShadowCss($settings['box_shadow'] ?? null);
        if ($shadow !== '') {
            $styles[] = rtrim($shadow, ';');
        }

        $attrs = [];

        if (!empty($styles)) {
            $attrs['style'] = implode(';', $styles);
        }

        $id = $this->cssId($settings);
        if ($id !== '') {
            $attrs['id'] = $id;
        }

        $class = $this->cssClass($settings);
        if ($class !== '') {
            $attrs['class'] = $class;
        }

        $safeText = nl2br($this->escape($text));
        $html = '<p' . $this->attr($attrs) . '>' . $safeText . '</p>';

        return $this->applyAnimation($html, $settings);
    }
}