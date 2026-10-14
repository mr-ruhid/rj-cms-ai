<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class ArchiveDescriptionTag extends BaseTag
{
    public function id(): string
    {
        return 'archive_description';
    }

    public function name(): string
    {
        return __('Archive Description');
    }

    public function category(): string
    {
        return 'archive';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h12M4 18h8"/></svg>';
    }

    public function description(): string
    {
        return __('Description of the current archive page.');
    }

    public function resolve(array $options = []): string
    {
        $description = view()->shared('archive_description');

        if (is_string($description) && $description !== '') {
            return $this->escape($description);
        }

        return '';
    }
}