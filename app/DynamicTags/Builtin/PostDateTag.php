<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PostDateTag extends BaseTag
{
    public function id(): string
    {
        return 'post_date';
    }

    public function name(): string
    {
        return __('Post Date');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
    }

    public function description(): string
    {
        return __('Published date of the current post.');
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
                    'short' => __('Short (d.m.Y)'),
                    'long' => __('Long (j F Y)'),
                    'relative' => __('Relative (2 days ago)'),
                ],
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $post = view()->shared('post');

        if (!is_object($post) || empty($post->created_at)) {
            return '';
        }

        $date = $post->created_at;
        $format = $this->option($options, 'format', 'default');

        $value = match ($format) {
            'short' => $date->format('d.m.Y'),
            'long' => $date->translatedFormat('j F Y'),
            'relative' => $date->diffForHumans(),
            default => $date->format('F j, Y'),
        };

        return $this->escape($value);
    }
}