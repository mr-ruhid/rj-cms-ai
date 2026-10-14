<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PostTermsTag extends BaseTag
{
    public function id(): string
    {
        return 'post_terms';
    }

    public function name(): string
    {
        return __('Post Terms');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><path d="M7 7h.01"/></svg>';
    }

    public function description(): string
    {
        return __('Categories or tags of the current post.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'type',
                'type' => 'select',
                'label' => __('Type'),
                'default' => 'category',
                'options' => [
                    'category' => __('Categories'),
                    'tag' => __('Tags'),
                ],
            ],
            [
                'name' => 'separator',
                'type' => 'text',
                'label' => __('Separator'),
                'default' => ', ',
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $post = view()->shared('post');

        if (!is_object($post)) {
            return '';
        }

        $type = $this->option($options, 'type', 'category');
        $separator = (string) $this->option($options, 'separator', ', ');

        $relation = $type === 'tag' ? 'tags' : 'categories';

        if (!method_exists($post, $relation)) {
            return '';
        }

        $items = $post->{$relation};

        if (!$items || $items->isEmpty()) {
            return '';
        }

        $labels = [];

        foreach ($items as $item) {
            $labels[] = method_exists($item, 'getNameAttribute')
                ? $item->getNameAttribute()
                : ($item->name ?? '');
        }

        $labels = array_filter($labels);

        if (empty($labels)) {
            return '';
        }

        return $this->escape(implode($separator, $labels));
    }
}