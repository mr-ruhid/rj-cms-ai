<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class PageTitleTag extends BaseTag
{
    public function id(): string
    {
        return 'page_title';
    }

    public function name(): string
    {
        return __('Page Title');
    }

    public function category(): string
    {
        return 'archive';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 4v16M18 4v16M6 12h12"/></svg>';
    }

    public function description(): string
    {
        return __('Current page or template title.');
    }

    public function resolve(array $options = []): string
    {
        $title = '';

        if (view()->shared('title')) {
            $title = (string) view()->shared('title');
        } elseif (view()->shared('template')) {
            $template = view()->shared('template');
            if (is_object($template) && isset($template->name)) {
                $title = (string) $template->name;
            }
        }

        if ($title === '') {
            $title = rj_get_option('site_name', config('app.name'));
        }

        return $this->escape($title);
    }
}