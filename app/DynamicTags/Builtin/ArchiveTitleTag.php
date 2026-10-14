<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class ArchiveTitleTag extends BaseTag
{
    public function id(): string
    {
        return 'archive_title';
    }

    public function name(): string
    {
        return __('Archive Title');
    }

    public function category(): string
    {
        return 'archive';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 12h14M5 16h10"/><rect x="3" y="3" width="18" height="18" rx="2"/></svg>';
    }

    public function description(): string
    {
        return __('Title of the current archive page.');
    }

    public function resolve(array $options = []): string
    {
        $title = view()->shared('archive_title');

        if (is_string($title) && $title !== '') {
            return $this->escape($title);
        }

        return '';
    }
}