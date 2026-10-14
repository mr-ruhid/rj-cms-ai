<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class SiteTaglineTag extends BaseTag
{
    public function id(): string
    {
        return 'site_tagline';
    }

    public function name(): string
    {
        return __('Site Tagline');
    }

    public function category(): string
    {
        return 'site';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h10M7 16h6"/></svg>';
    }

    public function description(): string
    {
        return __('Short description of your site.');
    }

    public function resolve(array $options = []): string
    {
        return $this->escape((string) rj_get_option('site_tagline', ''));
    }
}