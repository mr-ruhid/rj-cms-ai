<?php

use App\Models\Option;

if (!function_exists('rj_get_option')) {
    function rj_get_option(string $name, $default = null)
    {
        return Option::get($name, $default);
    }
}

if (!function_exists('rj_set_option')) {
    function rj_set_option(string $name, $value, string $group = 'general', string $type = 'text', bool $autoload = true): void
    {
        Option::set($name, $value, $group, $type, $autoload);
    }
}

if (!function_exists('rj_delete_option')) {
    function rj_delete_option(string $name): void
    {
        Option::forget($name);
    }
}

if (!function_exists('rj_flush_options')) {
    function rj_flush_options(): void
    {
        Option::flush();
    }
}
