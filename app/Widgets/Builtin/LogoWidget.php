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
        $url = $settings['url'] ?? '';

        if ($url === '') {
            return '';
        }

        $widthMap = [
            'auto' => 'auto',
            'xs' => '80px',
            'sm' => '120px',
            'md' => '160px',
            'lg' => '200px',
            'xl' => '280px',
        ];

        $heightMap = [
            'auto' => 'auto',
            'xs' => '32px',
            'sm' => '48px',
            'md' => '64px',
            'lg' => '80px',
            'xl' => '120px',
        ];

        $jcMap = [
            'left' => 'flex-start',
            'center' => 'center',
            'right' => 'flex-end',
        ];

        $widthKey = $settings['width'] ?? 'auto';
        $width = $widthMap[$widthKey] ?? 'auto';

        if ($widthKey === 'custom' && !empty($settings['custom_width'])) {
            $width = $this->escape($settings['custom_width']);
        }

        $height = $heightMap[$settings['height'] ?? 'auto'] ?? 'auto';
        $align = $jcMap[$settings['align'] ?? 'left'] ?? 'flex-start';

        $imgStyles = [
            'display:block',
            'max-width:100%',
            'width:' . $width,
            'height:' . $height,
        ];

        if ($height !== 'auto') {
            $imgStyles[] = 'object-fit:contain';
        }

        $imgAttrs = [
            'src' => $url,
            'alt' => $settings['alt'] ?? '',
            'style' => implode(';', $imgStyles),
        ];

        $imgHtml = '<img' . $this->attr($imgAttrs) . '>';

        $link = $settings['link'] ?? '';

        if ($link !== '') {
            $linkAttrs = [
                'href' => $link,
                'style' => 'display:inline-block;text-decoration:none;',
            ];

            $target = $settings['target'] ?? '_self';
            if ($target === '_blank') {
                $linkAttrs['target'] = '_blank';
                $linkAttrs['rel'] = 'noopener';
            }

            $imgHtml = '<a' . $this->attr($linkAttrs) . '>' . $imgHtml . '</a>';
        }

        $wrapperStyles = [
            'display:flex',
            'justify-content:' . $align,
        ];

        $padding = $this->dimensionCss($settings, 'padding_dim');
        if ($padding !== '') {
            $wrapperStyles[] = 'padding:' . $padding;
        }

        $margin = $this->dimensionCss($settings, 'margin_dim');
        if ($margin !== '') {
            $wrapperStyles[] = 'margin:' . $margin;
        }

        $bg = $this->backgroundCss($settings);
        if ($bg !== '') {
            $wrapperStyles[] = rtrim($bg, ';');
        }

        $wrapperAttrs = [
            'style' => implode(';', $wrapperStyles),
        ];

        $id = $this->cssId($settings);
        if ($id !== '') {
            $wrapperAttrs['id'] = $id;
        }

        $class = $this->cssClass($settings);
        if ($class !== '') {
            $wrapperAttrs['class'] = $class;
        }

        $html = '<div' . $this->attr($wrapperAttrs) . '>' . $imgHtml . '</div>';

        return $this->applyAnimation($html, $settings);
    }
}