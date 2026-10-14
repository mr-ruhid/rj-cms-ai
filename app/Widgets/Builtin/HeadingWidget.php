<?php

namespace App\Widgets\Builtin;

use App\Widgets\BaseWidget;

class HeadingWidget extends BaseWidget
{
    public function id(): string
    {
        return 'heading';
    }

    public function name(): string
    {
        return __('Heading');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4v16M18 4v16M6 12h12"/></svg>';
    }

    public function category(): string
    {
        return 'basic';
    }

    public function description(): string
    {
        return __('Add a title or headline.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'text',
                'type' => 'text',
                'label' => __('Text'),
                'default' => __('Your heading goes here'),
                'tab' => 'content',
                'placeholder' => __('Enter heading text'),
            ],
            [
                'name' => 'level',
                'type' => 'select',
                'label' => __('HTML Tag'),
                'default' => 'h2',
                'tab' => 'content',
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
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
                'name' => 'color',
                'type' => 'color',
                'label' => __('Text Color'),
                'default' => '#111827',
                'tab' => 'style',
            ],
            [
                'name' => 'font_size',
                'type' => 'text',
                'label' => __('Font Size'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 2rem, 32px',
            ],
            [
                'name' => 'font_weight',
                'type' => 'select',
                'label' => __('Font Weight'),
                'default' => '700',
                'tab' => 'style',
                'options' => [
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900',
                ],
            ],
            [
                'name' => 'line_height',
                'type' => 'text',
                'label' => __('Line Height'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 1.2, 24px',
            ],
            [
                'name' => 'letter_spacing',
                'type' => 'text',
                'label' => __('Letter Spacing'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 1px, 0.05em',
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
        $level = in_array($settings['level'] ?? 'h2', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'], true)
            ? $settings['level']
            : 'h2';

        $text = $settings['text'] ?? '';

        if ($text === '') {
            return '';
        }

        $styles = [];

        if (!empty($settings['color'])) {
            $styles[] = 'color:' . $this->escape($settings['color']);
        }

        if (!empty($settings['font_size'])) {
            $styles[] = 'font-size:' . $this->escape($settings['font_size']);
        }

        if (!empty($settings['font_weight'])) {
            $styles[] = 'font-weight:' . $this->escape($settings['font_weight']);
        }

        if (!empty($settings['line_height'])) {
            $styles[] = 'line-height:' . $this->escape($settings['line_height']);
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

        $html = '<' . $level . $this->attr($attrs) . '>' . $this->escape($text) . '</' . $level . '>';

        return $this->applyAnimation($html, $settings);
    }
}