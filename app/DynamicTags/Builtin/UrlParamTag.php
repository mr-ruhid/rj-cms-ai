<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class UrlParamTag extends BaseTag
{
    public function id(): string
    {
        return 'url_param';
    }

    public function name(): string
    {
        return __('URL Parameter');
    }

    public function category(): string
    {
        return 'site';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>';
    }

    public function description(): string
    {
        return __('Read a value from the current URL query string.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'key',
                'type' => 'text',
                'label' => __('Parameter Name'),
                'default' => '',
                'placeholder' => 'e.g. utm_source',
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $key = $this->option($options, 'key', '');

        if ($key === '') {
            return '';
        }

        $value = request()->query($key, '');

        return $this->escape((string) $value);
    }
}