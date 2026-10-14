<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class CurrentDateTag extends BaseTag
{
    public function id(): string
    {
        return 'current_date';
    }

    public function name(): string
    {
        return __('Current Date');
    }

    public function category(): string
    {
        return 'site';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
    }

    public function options(): array
    {
        return [
            [
                'name' => 'format',
                'type' => 'select',
                'label' => __('Format'),
                'default' => 'default',
                'options' => [
                    'default' => __('Default'),
                    'short' => __('Short'),
                    'long' => __('Long'),
                    'iso' => __('ISO 8601'),
                ],
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $format = $this->option($options, 'format', 'default');

        $value = match ($format) {
            'short' => now()->format('d.m.Y'),
            'long' => now()->translatedFormat('j F Y'),
            'iso' => now()->toIso8601String(),
            default => now()->format('F j, Y'),
        };

        return $this->escape($value);
    }
}