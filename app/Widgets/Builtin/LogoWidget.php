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
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
    }

    public function category(): string
    {
        return 'header';
    }

    public function description(): string
    {
        return __('Add your site logo with link, size and alignment.');
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'url',
                'type' => 'media',
                'label' => __('Logo Image'),
                'default' => '',
            ],
            [
                'name' => 'alt',
                'type' => 'text',
                'label' => __('Alt Text'),
                'default' => '',
            ],
            [
                'name' => 'link',
                'type' => 'text',
                'label' => __('Link URL'),
                'default' => '/',
            ],
            [
                'name' => 'target',
                'type' => 'select',
                'label' => __('Link Target'),
                'default' => '_self',
                'options' => [
                    '_self' => __('Same window'),
                    '_blank' => __('New window'),
                ],
            ],
            [
                'name' => 'width',
                'type' => 'select',
                'label' => __('Width'),
                'default' => 'auto',
                'options' => [
                    'auto' => __('Auto (natural size)'),
                    'xs' => __('Extra Small — 80px'),
                    'sm' => __('Small — 120px'),
                    'md' => __('Medium — 160px'),
                    'lg' => __('Large — 200px'),
                    'xl' => __('Extra Large — 280px'),
                    'custom' => __('Custom'),
                ],
            ],
            [
                'name' => 'custom_width',
                'type' => 'text',
                'label' => __('Custom Width'),
                'default' => '',
            ],
            [
                'name' => 'height',
                'type' => 'select',
                'label' => __('Height'),
                'default' => 'auto',
                'options' => [
                    'auto' => __('Auto'),
                    'xs' => __('Extra Small — 32px'),
                    'sm' => __('Small — 48px'),
                    'md' => __('Medium — 64px'),
                    'lg' => __('Large — 80px'),
                    'xl' => __('Extra Large — 120px'),
                ],
            ],
            [
                'name' => 'align',
                'type' => 'select',
                'label' => __('Alignment'),
                'default' => 'left',
                'options' => [
                    'left' => __('Left'),
                    'center' => __('Center'),
                    'right' => __('Right'),
                ],
            ],
        ];
    }

    public function render(array $settings): string
    {
        $url = $this->setting($settings, 'url', '');
        $alt = $this->setting($settings, 'alt', '');
        $link = $this->setting($settings, 'link', '/');
        $target = $this->setting($settings, 'target', '_self');
        $width = $this->setting($settings, 'width', 'auto');
        $customWidth = $this->setting($settings, 'custom_width', '');
        $height = $this->setting($settings, 'height', 'auto');
        $align = $this->setting($settings, 'align', 'left');

        if (empty($url)) {
            return '';
        }

        $alignMap = [
            'left' => '0 auto 0 0',
            'center' => '0 auto',
            'right' => '0 0 0 auto',
        ];

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

        $widthValue = $width === 'custom' && $customWidth !== ''
            ? $customWidth
            : ($widthMap[$width] ?? 'auto');

        $heightValue = $heightMap[$height] ?? 'auto';

        $imgStyles = [
            'display:block',
            'max-width:100%',
            'height:auto',
            'width:' . $widthValue,
            'height:' . $heightValue,
        ];

        if ($height !== 'auto') {
            $imgStyles[] = 'object-fit:contain';
        }

        $img = sprintf(
            '<img src="%s" alt="%s" style="%s">',
            $this->escape($url),
            $this->escape($alt),
            implode('; ', $imgStyles)
        );

        $wrapperStyle = 'display:inline-block; margin:' . ($alignMap[$align] ?? '0 auto 0 0') . ';';

        if (!empty($link)) {
            $img = sprintf(
                '<a href="%s" target="%s" style="display:inline-block; text-decoration:none;">%s</a>',
                $this->escape($link),
                $this->escape($target),
                $img
            );
        }

        return '<div style="' . $wrapperStyle . '">' . $img . '</div>';
    }
}