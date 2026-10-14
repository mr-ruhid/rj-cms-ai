<?php

namespace App\DynamicTags\Builtin;

use App\DynamicTags\BaseTag;

class ShortcodeTag extends BaseTag
{
    public function id(): string
    {
        return 'shortcode';
    }

    public function name(): string
    {
        return __('Shortcode');
    }

    public function category(): string
    {
        return 'site';
    }

    public function icon(): string
    {
        return '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6l-6 6 6 6M16 6l6 6-6 6"/></svg>';
    }

    public function description(): string
    {
        return __('Run a shortcode and output its result.');
    }

    public function options(): array
    {
        return [
            [
                'name' => 'code',
                'type' => 'text',
                'label' => __('Shortcode'),
                'default' => '',
                'placeholder' => 'e.g. [contact_form]',
            ],
        ];
    }

    public function resolve(array $options = []): string
    {
        $code = (string) $this->option($options, 'code', '');

        if ($code === '') {
            return '';
        }

        if (!function_exists('rj_do_shortcode')) {
            return '';
        }

        return rj_do_shortcode($code);
    }
}