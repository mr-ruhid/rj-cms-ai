<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class SiteUrlTag extends BaseTag
{
    public function id(): string
    {
        return 'site_url';
    }

    public function name(): string
    {
        return __('Site URL');
    }

    public function category(): string
    {
        return 'site';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>';
    }

    public function resolve(array $options = []): string
    {
        return $this->escape(url('/'));
    }
}