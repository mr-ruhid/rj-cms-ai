<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class SiteTitleTag extends BaseTag
{
    public function id(): string
    {
        return 'site_title';
    }

    public function name(): string
    {
        return __('Site Title');
    }

    public function category(): string
    {
        return 'site';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h12a1 1 0 001-1V10"/></svg>';
    }

    public function resolve(array $options = []): string
    {
        return $this->escape(rj_get_option('site_name', config('app.name')));
    }
}