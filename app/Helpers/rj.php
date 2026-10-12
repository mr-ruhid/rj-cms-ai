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

if (!function_exists('rj_color_presets')) {
    function rj_color_presets(): array
    {
        return [
            'purple' => [
                'label' => 'Purple',
                'purple' => '124 58 237',
                'purple-light' => '168 85 247',
                'pink' => '236 72 153',
                'dark-1' => '26 16 48',
                'dark-2' => '35 18 67',
                'dark-3' => '45 23 84',
            ],
            'blue' => [
                'label' => 'Blue',
                'purple' => '37 99 235',
                'purple-light' => '96 165 250',
                'pink' => '14 165 233',
                'dark-1' => '15 23 42',
                'dark-2' => '30 41 59',
                'dark-3' => '51 65 85',
            ],
            'green' => [
                'label' => 'Green',
                'purple' => '16 185 129',
                'purple-light' => '52 211 153',
                'pink' => '132 204 22',
                'dark-1' => '6 24 18',
                'dark-2' => '15 42 32',
                'dark-3' => '22 61 46',
            ],
            'rose' => [
                'label' => 'Rose',
                'purple' => '225 29 72',
                'purple-light' => '251 113 133',
                'pink' => '236 72 153',
                'dark-1' => '41 15 22',
                'dark-2' => '61 22 33',
                'dark-3' => '82 30 44',
            ],
            'amber' => [
                'label' => 'Amber',
                'purple' => '217 119 6',
                'purple-light' => '245 158 11',
                'pink' => '239 68 68',
                'dark-1' => '41 30 12',
                'dark-2' => '60 43 17',
                'dark-3' => '82 58 23',
            ],
            'slate' => [
                'label' => 'Slate',
                'purple' => '71 85 105',
                'purple-light' => '100 116 139',
                'pink' => '148 163 184',
                'dark-1' => '15 23 42',
                'dark-2' => '30 41 59',
                'dark-3' => '51 65 85',
            ],
            'midnight' => [
                'label' => 'Midnight',
                'purple' => '99 102 241',
                'purple-light' => '129 140 248',
                'pink' => '168 85 247',
                'dark-1' => '10 10 30',
                'dark-2' => '20 18 50',
                'dark-3' => '30 27 75',
            ],
            'ocean' => [
                'label' => 'Ocean',
                'purple' => '8 145 178',
                'purple-light' => '34 211 238',
                'pink' => '59 130 246',
                'dark-1' => '7 30 41',
                'dark-2' => '12 45 60',
                'dark-3' => '17 62 82',
            ],
        ];
    }
}

if (!function_exists('rj_active_preset')) {
    function rj_active_preset(): array
    {
        $presets = rj_color_presets();
        $key = rj_get_option('admin_color_preset', 'purple');

        return $presets[$key] ?? $presets['purple'];
    }
}

if (!function_exists('rj_active_preset_key')) {
    function rj_active_preset_key(): string
    {
        $presets = rj_color_presets();
        $key = rj_get_option('admin_color_preset', 'purple');

        return isset($presets[$key]) ? $key : 'purple';
    }
}

if (!function_exists('rj_css_variables')) {
    function rj_css_variables(): string
    {
        $preset = rj_active_preset();

        $vars = [];
        $vars[] = '--rj-purple: ' . $preset['purple'] . ';';
        $vars[] = '--rj-purple-light: ' . $preset['purple-light'] . ';';
        $vars[] = '--rj-pink: ' . $preset['pink'] . ';';
        $vars[] = '--rj-dark-1: ' . $preset['dark-1'] . ';';
        $vars[] = '--rj-dark-2: ' . $preset['dark-2'] . ';';
        $vars[] = '--rj-dark-3: ' . $preset['dark-3'] . ';';

        return implode(' ', $vars);
    }
}

if (!function_exists('rj_logo_mask_url')) {
    function rj_logo_mask_url(): ?string
    {
        foreach (['svg', 'png', 'webp'] as $ext) {
            $path = public_path("images/logo-white.{$ext}");

            if (file_exists($path)) {
                return asset("images/logo-white.{$ext}");
            }
        }

        return null;
    }
}
