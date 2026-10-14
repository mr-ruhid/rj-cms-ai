<?php

namespace App\Widgets\Builtin;

use App\Widgets\BaseWidget;

class ImageWidget extends BaseWidget
{
    public function id(): string
    {
        return 'image';
    }

    public function name(): string
    {
        return __('Image');
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>';
    }

    public function category(): string
    {
        return 'media';
    }

    public function description(): string
    {
        return __('Add an image.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'url',
                'type' => 'media',
                'label' => __('Image'),
                'default' => '',
                'tab' => 'content',
            ],
            [
                'name' => 'alt',
                'type' => 'text',
                'label' => __('Alt Text'),
                'default' => '',
                'tab' => 'content',
                'placeholder' => __('Describe the image'),
            ],
            [
                'name' => 'link',
                'type' => 'url',
                'label' => __('Link'),
                'default' => '',
                'tab' => 'content',
                'placeholder' => 'https://',
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
                'default' => 'full',
                'tab' => 'style',
                'options' => [
                    'auto' => __('Auto'),
                    'full' => __('Full Width'),
                    'half' => '50%',
                    'third' => '33%',
                    'custom' => __('Custom'),
                ],
            ],
            [
                'name' => 'custom_width',
                'type' => 'text',
                'label' => __('Custom Width'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 320px, 40%',
            ],
            [
                'name' => 'height',
                'type' => 'text',
                'label' => __('Height'),
                'default' => '',
                'tab' => 'style',
                'placeholder' => 'e.g. 200px, auto',
            ],
            [
                'name' => 'object_fit',
                'type' => 'select',
                'label' => __('Object Fit'),
                'default' => 'cover',
                'tab' => 'style',
                'options' => [
                    'cover' => __('Cover'),
                    'contain' => __('Contain'),
                    'fill' => __('Fill'),
                    'none' => __('None'),
                ],
            ],
            [
                'name' => 'align',
                'type' => 'select',
                'label' => __('Alignment'),
                'default' => 'center',
                'tab' => 'style',
                'options' => [
                    'left' => __('Left'),
                    'center' => __('Center'),
                    'right' => __('Right'),
                ],
            ],
            [
                'name' => 'rounded',
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
            [
                'name' => 'opacity',
                'type' => 'range',
                'label' => __('Opacity'),
                'default' => 100,
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'tab' => 'style',
            ],
            [
                'name' => 'box_shadow',
                'type' => 'select',
                'label' => __('Box Shadow'),
                'default' => 'none',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
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
        $url = $settings['url'] ?? '';

        if ($url === '') {
            return '';
        }

        $widthMap = [
            'auto' => 'auto',
            'full' => '100%',
            'half' => '50%',
            'third' => '33.333%',
        ];

        $roundedMap = [
            'none' => '0',
            'sm' => '4px',
            'md' => '8px',
            'lg' => '16px',
            'full' => '9999px',
        ];

        $alignMap = [
            'left' => '0 auto 0 0',
            'center' => '0 auto',
            'right' => '0 0 0 auto',
        ];

        $width = $widthMap[$settings['width'] ?? 'full'] ?? '100%';

        if (($settings['width'] ?? '') === 'custom' && !empty($settings['custom_width'])) {
            $width = $this->escape($settings['custom_width']);
        }

        $height = !empty($settings['height']) ? $this->escape($settings['height']) : 'auto';
        $objectFit = in_array($settings['object_fit'] ?? 'cover', ['cover', 'contain', 'fill', 'none'], true)
            ? $settings['object_fit']
            : 'cover';

        $radius = $roundedMap[$settings['rounded'] ?? 'md'] ?? '8px';
        $align = $alignMap[$settings['align'] ?? 'center'] ?? '0 auto';

        $opacity = isset($settings['opacity']) && $settings['opacity'] !== ''
            ? (int) $settings['opacity'] / 100
            : 1;

        $imgStyles = [
            'width:' . $width,
            'height:' . $height,
            'object-fit:' . ($height !== 'auto' ? $objectFit : 'fill'),
            'border-radius:' . $radius,
            'opacity:' . $opacity,
            'margin:' . $align,
            'display:block',
            'max-width:100%',
        ];

        $shadow = $this->boxShadowCss($settings['box_shadow'] ?? null);
        if ($shadow !== '') {
            $imgStyles[] = rtrim($shadow, ';');
        }

        $imgAttrs = [
            'src' => $url,
            'alt' => $settings['alt'] ?? '',
            'style' => implode(';', $imgStyles),
            'loading' => 'lazy',
        ];

        $imgHtml = '<img' . $this->attr($imgAttrs) . '>';

        $link = $settings['link'] ?? '';

        if ($link !== '') {
            $target = $settings['target'] ?? '_self';
            $linkAttrs = [
                'href' => $link,
                'style' => 'display:inline-block;text-decoration:none;',
            ];

            if ($target === '_blank') {
                $linkAttrs['target'] = '_blank';
                $linkAttrs['rel'] = 'noopener';
            }

            $imgHtml = '<a' . $this->attr($linkAttrs) . '>' . $imgHtml . '</a>';
        }

        $figureStyles = [];

        $padding = $this->dimensionCss($settings, 'padding_dim');
        if ($padding !== '') {
            $figureStyles[] = 'padding:' . $padding;
        }

        $margin = $this->dimensionCss($settings, 'margin_dim');
        if ($margin !== '') {
            $figureStyles[] = 'margin:' . $margin;
        } else {
            $figureStyles[] = 'margin:0';
        }

        $bg = $this->backgroundCss($settings);
        if ($bg !== '') {
            $figureStyles[] = rtrim($bg, ';');
        }

        $figureAttrs = [];

        if (!empty($figureStyles)) {
            $figureAttrs['style'] = implode(';', $figureStyles);
        }

        $id = $this->cssId($settings);
        if ($id !== '') {
            $figureAttrs['id'] = $id;
        }

        $class = $this->cssClass($settings);
        if ($class !== '') {
            $figureAttrs['class'] = $class;
        }

        $html = '<figure' . $this->attr($figureAttrs) . '>' . $imgHtml . '</figure>';

        return $this->applyAnimation($html, $settings);
    }
}