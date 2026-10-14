<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PostAuthorTag extends BaseTag
{
    public function id(): string
    {
        return 'post_author';
    }

    public function name(): string
    {
        return __('Post Author');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
    }

    public function description(): string
    {
        return __('Author of the current post.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'field',
                'type' => 'select',
                'label' => __('Field'),
                'default' => 'name',
                'options' => [
                    'name' => __('Name'),
                    'email' => __('Email'),
                ],
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $post = view()->shared('post');

        if (!is_object($post)) {
            return '';
        }

        $author = $post->author ?? null;

        if (!is_object($author)) {
            return '';
        }

        $field = $this->option($options, 'field', 'name');

        $value = match ($field) {
            'email' => $author->email ?? '',
            default => $author->name ?? '',
        };

        return $this->escape((string) $value);
    }
}