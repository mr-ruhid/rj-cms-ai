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

    public function advancedSchema(): array
    {
        return [
            [
                'name' => '_animation',
                'type' => 'select',
                'label' => __('Animation'),
                'default' => 'none',
                'options' => [
                    'none' => __('None'),
                    'fade-in' => __('Fade In'),
                    'slide-up' => __('Slide Up'),
                    'slide-down' => __('Slide Down'),
                    'slide-left' => __('Slide Left'),
                    'slide-right' => __('Slide Right'),
                    'zoom-in' => __('Zoom In'),
                    'zoom-out' => __('Zoom Out'),
                ],
            ],
            [
                'name' => '_animation_delay',
                'type' => 'select',
                'label' => __('Animation Delay'),
                'default' => '0',
                'options' => [
                    '0' => '0s',
                    '100ms' => '0.1s',
                    '200ms' => '0.2s',
                    '300ms' => '0.3s',
                    '500ms' => '0.5s',
                    '700ms' => '0.7s',
                    '1000ms' => '1s',
                ],
            ],
            [
                'name' => '_animation_duration',
                'type' => 'select',
                'label' => __('Animation Duration'),
                'default' => 'md',
                'options' => [
                    'sm' => __('Fast') . ' — 0.3s',
                    'md' => __('Normal') . ' — 0.6s',
                    'lg' => __('Slow') . ' — 1s',
                    'xl' => __('Very Slow') . ' — 1.5s',
                ],
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
        return array_merge($this->schema(), $this->advancedSchema());
    }

    public function fullDefaults(): array
    {
        return array_merge($this->defaults(), $this->advancedDefaults());
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