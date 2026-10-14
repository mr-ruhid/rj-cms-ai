<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;

abstract class BaseWidget implements WidgetContract
{
    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>';
    }

    public function category(): string
    {
        return 'basic';
    }

    public function description(): string
    {
        return '';
    }

    public function defaults(): array
    {
        $defaults = [];

        foreach ($this->schema() as $field) {
            if (array_key_exists('default', $field)) {
                $defaults[$field['name']] = $field['default'];
            }
        }

        return $defaults;
    }

    public function render(array $settings): string
    {
        return '';
    }

    public function commonStyleSchema(): array
    {
        return [
            [
                'name' => 'bg_type',
                'type' => 'select',
                'label' => __('Background Type'),
                'default' => 'none',
                'tab' => 'style',
                'options' => [
                    'none' => __('None'),
                    'classic' => __('Classic'),
                    'gradient' => __('Gradient'),
                ],
            ],
            [
                'name' => 'bg_color',
                'type' => 'color',
                'label' => __('Background Color'),
                'default' => '',
                'tab' => 'style',
            ],
            [
                'name' => 'bg_image',
                'type' => 'media',
                'label' => __('Background Image'),
                'default' => '',
                'tab' => 'style',
            ],
            [
                'name' => 'bg_position',
                'type' => 'select',
                'label' => __('Background Position'),
                'default' => 'center',
                'tab' => 'style',
                'options' => [
                    'center' => __('Center'),
                    'top' => __('Top'),
                    'bottom' => __('Bottom'),
                    'left' => __('Left'),
                    'right' => __('Right'),
                ],
            ],
            [
                'name' => 'bg_size',
                'type' => 'select',
                'label' => __('Background Size'),
                'default' => 'cover',
                'tab' => 'style',
                'options' => [
                    'cover' => __('Cover'),
                    'contain' => __('Contain'),
                    'auto' => __('Auto'),
                ],
            ],
            [
                'name' => 'bg_repeat',
                'type' => 'select',
                'label' => __('Background Repeat'),
                'default' => 'no-repeat',
                'tab' => 'style',
                'options' => [
                    'no-repeat' => __('No Repeat'),
                    'repeat' => __('Repeat'),
                    'repeat-x' => __('Repeat X'),
                    'repeat-y' => __('Repeat Y'),
                ],
            ],
            [
                'name' => 'gradient_color_1',
                'type' => 'color',
                'label' => __('Gradient Color 1'),
                'default' => '#7c3aed',
                'tab' => 'style',
            ],
            [
                'name' => 'gradient_color_2',
                'type' => 'color',
                'label' => __('Gradient Color 2'),
                'default' => '#ec4899',
                'tab' => 'style',
            ],
            [
                'name' => 'gradient_angle',
                'type' => 'number',
                'label' => __('Gradient Angle'),
                'default' => 135,
                'min' => 0,
                'max' => 360,
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
                    'xl' => __('Extra Large'),
                ],
            ],
            [
                'name' => 'align_self',
                'type' => 'select',
                'label' => __('Align Self'),
                'default' => 'auto',
                'tab' => 'style',
                'options' => [
                    'auto' => __('Auto'),
                    'start' => __('Start'),
                    'center' => __('Center'),
                    'end' => __('End'),
                    'stretch' => __('Stretch'),
                ],
            ],
            [
                'name' => 'order',
                'type' => 'number',
                'label' => __('Order'),
                'default' => 0,
                'min' => -10,
                'max' => 10,
                'step' => 1,
                'tab' => 'style',
            ],
        ];
    }

    public function commonStyleDefaults(): array
    {
        $defaults = [];

        foreach ($this->commonStyleSchema() as $field) {
            if (array_key_exists('default', $field)) {
                $defaults[$field['name']] = $field['default'];
            }
        }

        return $defaults;
    }

    public function advancedSchema(): array
    {
        return [
            [
                'name' => '_animation',
                'type' => 'select',
                'label' => __('Entrance Animation'),
                'default' => 'none',
                'tab' => 'advanced',
                'options' => [
                    'none' => __('None'),
                    'fadeIn' => __('Fade In'),
                    'fadeInUp' => __('Fade In Up'),
                    'fadeInDown' => __('Fade In Down'),
                    'fadeInLeft' => __('Fade In Left'),
                    'fadeInRight' => __('Fade In Right'),
                    'zoomIn' => __('Zoom In'),
                    'bounceIn' => __('Bounce In'),
                    'flipInX' => __('Flip In X'),
                    'rotateIn' => __('Rotate In'),
                    'slideUp' => __('Slide Reveal'),
                    'blurIn' => __('Blur In'),
                    'pulse' => __('Pulse'),
                ],
            ],
            [
                'name' => '_animation_duration',
                'type' => 'select',
                'label' => __('Duration'),
                'default' => 'md',
                'tab' => 'advanced',
                'options' => [
                    'sm' => __('Fast') . ' (300ms)',
                    'md' => __('Normal') . ' (600ms)',
                    'lg' => __('Slow') . ' (1s)',
                    'xl' => __('Very Slow') . ' (1.5s)',
                ],
            ],
            [
                'name' => '_animation_delay',
                'type' => 'select',
                'label' => __('Delay'),
                'default' => '0',
                'tab' => 'advanced',
                'options' => [
                    '0' => __('None'),
                    '200ms' => '200ms',
                    '400ms' => '400ms',
                    '600ms' => '600ms',
                    '1s' => '1s',
                ],
            ],
            [
                'name' => '_hover',
                'type' => 'select',
                'label' => __('Hover Effect'),
                'default' => 'none',
                'tab' => 'advanced',
                'options' => [
                    'none' => __('None'),
                    'grow' => __('Grow'),
                    'lift' => __('Lift + Shadow'),
                    'glow' => __('Glow'),
                    'tilt' => __('3D Tilt'),
                    'float' => __('Float'),
                ],
            ],
            [
                'name' => '_hover_text_color',
                'type' => 'color',
                'label' => __('Hover Text Color'),
                'default' => '',
                'tab' => 'advanced',
            ],
            [
                'name' => '_hover_bg_color',
                'type' => 'color',
                'label' => __('Hover Background'),
                'default' => '',
                'tab' => 'advanced',
            ],
            [
                'name' => '_hover_border_color',
                'type' => 'color',
                'label' => __('Hover Border Color'),
                'default' => '',
                'tab' => 'advanced',
            ],
            [
                'name' => '_css_id',
                'type' => 'text',
                'label' => __('CSS ID'),
                'default' => '',
                'tab' => 'advanced',
                'placeholder' => 'my-element-id',
            ],
            [
                'name' => '_css_class',
                'type' => 'text',
                'label' => __('Custom CSS Class'),
                'default' => '',
                'tab' => 'advanced',
                'placeholder' => 'my-custom-class',
            ],
        ];
    }

    public function advancedDefaults(): array
    {
        $defaults = [];

        foreach ($this->advancedSchema() as $field) {
            if (array_key_exists('default', $field)) {
                $defaults[$field['name']] = $field['default'];
            }
        }

        return $defaults;
    }

    public function fullSchema(): array
    {
        return array_merge($this->schema(), $this->commonStyleSchema(), $this->advancedSchema());
    }

    public function fullDefaults(): array
    {
        return array_merge($this->defaults(), $this->commonStyleDefaults(), $this->advancedDefaults());
    }

    public function applyAnimation(string $html, array $settings): string
    {
        $animation = $settings['_animation'] ?? 'none';

        if ($animation === 'none' || $animation === '') {
            return $html;
        }

        $duration = $settings['_animation_duration'] ?? 'md';
        $delay = $settings['_animation_delay'] ?? '0';

        $durations = [
            'sm' => '300ms',
            'md' => '600ms',
            'lg' => '1000ms',
            'xl' => '1500ms',
        ];

        $style = 'animation-duration: ' . ($durations[$duration] ?? '600ms') . ';';

        if ($delay !== '0' && $delay !== '') {
            $style .= ' animation-delay: ' . $delay . ';';
        }

        return '<div class="rj-animate rj-animate-' . $this->escape($animation) . '" style="' . $style . '">' . $html . '</div>';
    }

    protected function dimension(array $settings, string $key): ?array
    {
        $value = $settings[$key] ?? null;

        if (!is_array($value)) {
            return null;
        }

        $unit = $value['unit'] ?? 'px';

        $allowed = ['px', '%', 'em', 'rem', 'vh', 'vw'];
        if (!in_array($unit, $allowed, true)) {
            $unit = 'px';
        }

        $norm = [
            'top' => $this->dimValue($value['top'] ?? ''),
            'right' => $this->dimValue($value['right'] ?? ''),
            'bottom' => $this->dimValue($value['bottom'] ?? ''),
            'left' => $this->dimValue($value['left'] ?? ''),
            'unit' => $unit,
        ];

        if ($norm['top'] === '' && $norm['right'] === '' && $norm['bottom'] === '' && $norm['left'] === '') {
            return null;
        }

        return $norm;
    }

    protected function dimensionCss(array $settings, string $key): string
    {
        $d = $this->dimension($settings, $key);

        if ($d === null) {
            return '';
        }

        $u = $d['unit'];

        $t = $d['top'] !== '' ? $d['top'] . $u : '0';
        $r = $d['right'] !== '' ? $d['right'] . $u : '0';
        $b = $d['bottom'] !== '' ? $d['bottom'] . $u : '0';
        $l = $d['left'] !== '' ? $d['left'] . $u : '0';

        return $t . ' ' . $r . ' ' . $b . ' ' . $l;
    }

    protected function spacingStyle(array $settings, array $map): string
    {
        $parts = [];

        foreach ($map as $cssProp => $key) {
            $css = $this->dimensionCss($settings, $key);
            if ($css !== '') {
                $parts[] = $cssProp . ':' . $css . ';';
            }
        }

        return implode('', $parts);
    }

    protected function dynamicValue(array $settings, string $key, $default = '')
    {
        $value = $settings[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        return $value;
    }

    protected function linkAttrs(array $settings, string $key): string
    {
        $raw = $settings[$key] ?? null;

        $url = '';
        $target = '_self';
        $nofollow = false;

        if (is_array($raw)) {
            $url = $raw['url'] ?? '';
            $target = $raw['target'] ?? '_self';
            $nofollow = !empty($raw['nofollow']);
        } else {
            $url = (string) $raw;
            $target = $settings[$key . '_target'] ?? '_self';
            $nofollow = !empty($settings[$key . '_nofollow']);
        }

        if ($url === '') {
            return '';
        }

        $attrs = ' href="' . $this->escape($url) . '"';

        if ($target === '_blank') {
            $attrs .= ' target="_blank"';
            $attrs .= ' rel="noopener' . ($nofollow ? ' nofollow' : '') . '"';
        } elseif ($nofollow) {
            $attrs .= ' rel="nofollow"';
        }

        return $attrs;
    }

    protected function iconSvg(?string $name, int $size = 20): string
    {
        if (!$name) {
            return '';
        }

        $path = $this->iconPaths()[$name] ?? '';

        if ($path === '') {
            return '';
        }

        return '<svg width="' . (int) $size . '" height="' . (int) $size . '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">' . $path . '</svg>';
    }

    protected function iconPaths(): array
    {
        return [
            'check' => '<path d="M5 13l4 4L19 7"/>',
            'arrow-right' => '<path d="M5 12h14M13 5l7 7-7 7"/>',
            'arrow-left' => '<path d="M19 12H5M11 19l-7-7 7-7"/>',
            'arrow-up' => '<path d="M12 19V5M5 12l7-7 7 7"/>',
            'arrow-down' => '<path d="M12 5v14M19 12l-7 7-7-7"/>',
            'star' => '<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>',
            'heart' => '<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>',
            'user' => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
            'mail' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/>',
            'phone' => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>',
            'search' => '<circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>',
            'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
            'close' => '<path d="M18 6L6 18M6 6l12 12"/>',
            'plus' => '<path d="M12 5v14M5 12h14"/>',
            'minus' => '<path d="M5 12h14"/>',
            'edit' => '<path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>',
            'trash' => '<path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>',
            'eye' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
            'lock' => '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/>',
            'download' => '<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
            'upload' => '<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>',
            'external' => '<path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><path d="M15 3h6v6M10 14L21 3"/>',
            'copy' => '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>',
            'info' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>',
            'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
            'clock' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
            'tag' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><path d="M7 7h.01"/>',
        ];
    }

    protected function repeaterItems(array $settings, string $key): array
    {
        $items = $settings[$key] ?? [];

        if (!is_array($items)) {
            return [];
        }

        return array_values(array_filter($items, fn($item) => is_array($item)));
    }

    protected function tagWrap(string $tag, string $content, array $attrs = []): string
    {
        $tag = preg_replace('/[^a-z0-9]/i', '', $tag) ?: 'div';

        return '<' . $tag . $this->attr($attrs) . '>' . $content . '</' . $tag . '>';
    }

    protected function cssId(array $settings): string
    {
        $id = $settings['_css_id'] ?? '';

        if ($id === '') {
            return '';
        }

        return preg_replace('/[^a-zA-Z0-9_-]/', '', $id);
    }

    protected function cssClass(array $settings): string
    {
        $class = $settings['_css_class'] ?? '';

        return $class !== '' ? $this->escape($class) : '';
    }

    protected function boxShadowCss(?string $shadow): string
    {
        $map = [
            'sm' => '0 1px 2px rgba(0,0,0,0.06)',
            'md' => '0 4px 12px rgba(0,0,0,0.1)',
            'lg' => '0 12px 32px rgba(0,0,0,0.16)',
            'xl' => '0 24px 60px rgba(0,0,0,0.22)',
        ];

        if (!$shadow || $shadow === 'none' || !isset($map[$shadow])) {
            return '';
        }

        return 'box-shadow:' . $map[$shadow] . ';';
    }

    protected function backgroundCss(array $settings, string $prefix = 'bg'): string
    {
        $type = $settings[$prefix . '_type'] ?? 'none';

        if ($type === 'gradient') {
            $c1 = $settings['gradient_color_1'] ?? '#7c3aed';
            $c2 = $settings['gradient_color_2'] ?? '#ec4899';
            $angle = (int) ($settings['gradient_angle'] ?? 135);

            return 'background:linear-gradient(' . $angle . 'deg,' . $this->escape($c1) . ',' . $this->escape($c2) . ');';
        }

        if ($type === 'classic' || $type === 'none') {
            $css = '';

            if (!empty($settings[$prefix . '_color'])) {
                $css .= 'background-color:' . $this->escape($settings[$prefix . '_color']) . ';';
            }

            if (!empty($settings[$prefix . '_image'])) {
                $css .= "background-image:url('" . $this->escape($settings[$prefix . '_image']) . "');";
                $css .= 'background-position:' . $this->escape($settings[$prefix . '_position'] ?? 'center') . ';';
                $css .= 'background-size:' . $this->escape($settings[$prefix . '_size'] ?? 'cover') . ';';
                $css .= 'background-repeat:' . $this->escape($settings[$prefix . '_repeat'] ?? 'no-repeat') . ';';
            }

            return $css;
        }

        return '';
    }

    protected function renderImageAdvanced(array $settings, string $key, array $options = []): string
    {
        $url = $settings[$key] ?? '';

        if ($url === '') {
            return '';
        }

        $alt = $options['alt'] ?? '';
        $class = $options['class'] ?? '';
        $loading = $options['loading'] ?? 'lazy';

        $attrs = [
            'src' => $url,
            'alt' => $alt,
            'loading' => $loading,
        ];

        if ($class !== '') {
            $attrs['class'] = $class;
        }

        if (isset($options['width'])) {
            $attrs['width'] = $options['width'];
        }

        if (isset($options['height'])) {
            $attrs['height'] = $options['height'];
        }

        if (isset($options['style']) && $options['style'] !== '') {
            $attrs['style'] = $options['style'];
        }

        return '<img' . $this->attr($attrs) . '>';
    }

    private function dimValue($raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }

        if (!is_numeric($raw)) {
            return '';
        }

        return (string) $raw;
    }

    protected function setting(array $settings, string $key, $default = null)
    {
        return $settings[$key] ?? $default;
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function attr(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $parts[] = $key . '="' . $this->escape((string) $value) . '"';
        }

        return $parts ? ' ' . implode(' ', $parts) : '';
    }

    protected function classNames(array $classes): string
    {
        $filtered = array_filter($classes, fn($c) => !empty($c));

        return $filtered ? implode(' ', $filtered) : '';
    }

    protected function alignClass(?string $align): string
    {
        return match ($align) {
            'left' => 'text-left',
            'center' => 'text-center',
            'right' => 'text-right',
            'justify' => 'text-justify',
            default => 'text-left',
        };
    }

    protected function paddingClass(?string $padding): string
    {
        return match ($padding) {
            'none' => 'p-0',
            'sm' => 'p-2',
            'md' => 'p-4',
            'lg' => 'p-6',
            'xl' => 'p-10',
            default => 'p-0',
        };
    }

    protected function marginClass(?string $margin): string
    {
        return match ($margin) {
            'none' => 'm-0',
            'sm' => 'my-2',
            'md' => 'my-4',
            'lg' => 'my-6',
            'xl' => 'my-10',
            default => 'my-0',
        };
    }
}