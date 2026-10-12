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

if (!function_exists('rj_available_locales')) {
    function rj_available_locales(): array
    {
        $path = lang_path();
        $locales = [];

        if (is_dir($path)) {
            foreach (scandir($path) as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                if (is_dir($path . '/' . $item)) {
                    $locales[] = $item;
                } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'json') {
                    $locales[] = pathinfo($item, PATHINFO_FILENAME);
                }
            }
        }

        if (empty($locales)) {
            $locales = [config('app.locale', 'en')];
        }

        return array_values(array_unique($locales));
    }
}
if (!function_exists('rj_lang_name')) {
    function rj_lang_name(string $locale): string
    {
        $custom = rj_get_option('lang_name_' . $locale);

        if ($custom) {
            return $custom;
        }

        if (class_exists(\Locale::class)) {
            $display = \Locale::getDisplayLanguage($locale, 'en');

            if ($display && $display !== $locale) {
                return $display;
            }
        }

        return strtoupper($locale);
    }
}

if (!function_exists('rj_lang_flag_url')) {
    function rj_lang_flag_url(string $locale): ?string
    {
        $custom = rj_get_option('lang_flag_' . $locale);

        if ($custom && file_exists(public_path('images/flags/' . $custom))) {
            return asset('images/flags/' . $custom);
        }

        foreach (['svg', 'png', 'jpg', 'jpeg', 'webp'] as $ext) {
            if (file_exists(public_path("images/flags/{$locale}.{$ext}"))) {
                return asset("images/flags/{$locale}.{$ext}");
            }
        }

        return null;
    }
}
