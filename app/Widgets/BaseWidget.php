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