<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class CustomFieldTag extends BaseTag
{
    public function id(): string
    {
        return 'custom_field';
    }

    public function name(): string
    {
        return __('Custom Field');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 9h10M7 13h6"/></svg>';
    }

    public function description(): string
    {
        return __('Value from post meta / custom fields.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'key',
                'type' => 'text',
                'label' => __('Field Key'),
                'default' => '',
                'placeholder' => 'e.g. subtitle, price, phone',
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $key = (string) $this->option($options, 'key', '');

        if ($key === '') {
            return '';
        }

        $post = view()->shared('post');

        if (!is_object($post)) {
            return '';
        }

        $value = null;

        if (method_exists($post, 'getMeta')) {
            $value = $post->getMeta($key);
        } elseif (isset($post->meta) && is_array($post->meta)) {
            $value = $post->meta[$key] ?? null;
        } elseif (isset($post->{$key})) {
            $value = $post->{$key};
        }

        if ($value === null || is_array($value) || is_object($value)) {
            return '';
        }

        return $this->escape((string) $value);
    }
}