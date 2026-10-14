<?php

namespace App\DynamicTags;

use App\DynamicTags\Contracts\TagContract;

abstract class BaseTag implements TagContract
{
    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10"/></svg>';
    }

    public function description(): string
    {
        return '';
    }

    public function options(): array
    {
        return [];
    }

    public function resolve(array $options = []): string
    {
        return '';
    }

    protected function option(array $options, string $key, $default = null)
    {
        return $options[$key] ?? $default;
    }

    protected function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}