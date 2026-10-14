<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PostExcerptTag extends BaseTag
{
    public function id(): string
    {
        return 'post_excerpt';
    }

    public function name(): string
    {
        return __('Post Excerpt');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M9 8h6"/><rect x="3" y="4" width="18" height="16" rx="2"/></svg>';
    }

    public function description(): string
    {
        return __('Short summary of the current post.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'length',
                'type' => 'number',
                'label' => __('Word Limit'),
                'default' => 30,
                'min' => 5,
                'max' => 200,
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $post = view()->shared('post');

        if (!is_object($post)) {
            return '';
        }

        $length = (int) ($options['length'] ?? 30);

        $text = '';

        if (!empty($post->excerpt)) {
            $text = (string) $post->excerpt;
        } elseif (!empty($post->content)) {
            $text = strip_tags((string) $post->content);
        }

        if ($text === '') {
            return '';
        }

        return $this->escape(\Illuminate\Support\Str::words($text, $length, '...'));
    }
}