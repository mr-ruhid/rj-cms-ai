<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PostTitleTag extends BaseTag
{
    public function id(): string
    {
        return 'post_title';
    }

    public function name(): string
    {
        return __('Post Title');
    }

    public function category(): string
    {
        return 'post';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z"/><path d="M8 8h8M8 12h8M8 16h5"/></svg>';
    }

    public function description(): string
    {
        return __('Title of the current post.');
    }

    public function resolve(array $options = []): string
    {
        $post = view()->shared('post');

        if (is_object($post) && isset($post->title)) {
            return $this->escape((string) $post->title);
        }

        return '';
    }
}