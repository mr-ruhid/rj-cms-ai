<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class SiteLogoTag extends BaseTag
{
    public function id(): string
    {
        return 'site_logo';
    }

    public function name(): string
    {
        return __('Site Logo');
    }

    public function category(): string
    {
        return 'site';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>';
    }

    public function resolve(array $options = []): string
    {
        $logo = rj_get_option('site_logo', '');

        if ($logo === '') {
            return '';
        }

        $alt = $this->escape(rj_get_option('site_name', config('app.name')));
        $url = $this->escape($logo);

        return '<img src="' . $url . '" alt="' . $alt . '" loading="lazy">';
    }
}