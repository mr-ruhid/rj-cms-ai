<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PostFeaturedImageTag extends BaseTag
{
    public function id(): string
    {
        return 'post_featured_image';
    }

    public function name(): string
    {
        return __('Featured Image');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>';
    }

    public function description(): string
    {
        return __('Featured image of the current post.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'size',
                'type' => 'select',
                'label' => __('Size'),
                'default' => 'full',
                'options' => [
                    'thumb' => __('Thumbnail'),
                    'medium' => __('Medium'),
                    'large' => __('Large'),
                    'full' => __('Full'),
                ],
            ],
            [
                'name' => 'output',
                'type' => 'select',
                'label' => __('Output'),
                'default' => 'url',
                'options' => [
                    'url' => __('URL only'),
                    'img' => __('<img> tag'),
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

        $image = $post->featured_image ?? '';

        if ($image === '') {
            return '';
        }

        $output = $this->option($options, 'output', 'url');

        if ($output === 'img') {
            $alt = $this->escape((string) ($post->title ?? ''));
            return '<img src="' . $this->escape($image) . '" alt="' . $alt . '" loading="lazy">';
        }

        return $this->escape($image);
    }
}