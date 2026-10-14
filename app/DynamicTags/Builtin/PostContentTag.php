<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PostContentTag extends BaseTag
{
    public function id(): string
    {
        return 'post_content';
    }

    public function name(): string
    {
        return __('Post Content');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h10"/></svg>';
    }

    public function description(): string
    {
        return __('Content of the current post.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'length',
                'type' => 'number',
                'label' => __('Character Limit'),
                'default' => 0,
                'min' => 0,
                'max' => 5000,
                'help' => __('0 = full content.'),
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $post = view()->shared('post');

        if (!is_object($post) || !isset($post->content)) {
            return '';
        }

        $content = (string) $post->content;

        if (empty($options['length']) || (int) $options['length'] <= 0) {
            return $content;
        }

        return \Illuminate\Support\Str::limit(strip_tags($content), (int) $options['length']);
    }
}