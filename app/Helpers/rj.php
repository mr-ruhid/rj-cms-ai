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

if (!function_exists('rj_social_providers')) {
    function rj_social_providers(): array
    {
        return [
            'google' => [
                'label' => 'Google', 'region' => 'global', 'color' => '#4285F4',
                'docs' => 'https://console.cloud.google.com/apis/credentials',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'facebook' => [
                'label' => 'Facebook', 'region' => 'global', 'color' => '#1877F2',
                'docs' => 'https://developers.facebook.com/apps',
                'fields' => [
                    'client_id' => ['label' => 'App ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'App Secret', 'type' => 'password'],
                ],
            ],
            'apple' => [
                'label' => 'Apple', 'region' => 'global', 'color' => '#000000',
                'docs' => 'https://developer.apple.com/account/resources/identifiers',
                'fields' => [
                    'client_id' => ['label' => 'Service ID', 'type' => 'text'],
                    'team_id' => ['label' => 'Team ID', 'type' => 'text'],
                    'key_id' => ['label' => 'Key ID', 'type' => 'text'],
                    'private_key' => ['label' => 'Private Key (.p8)', 'type' => 'textarea'],
                ],
            ],
            'microsoft' => [
                'label' => 'Microsoft', 'region' => 'global', 'color' => '#00A4EF',
                'docs' => 'https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps',
                'fields' => [
                    'client_id' => ['label' => 'Application ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                    'tenant' => ['label' => 'Tenant (common)', 'type' => 'text'],
                ],
            ],
            'github' => [
                'label' => 'GitHub', 'region' => 'global', 'color' => '#181717',
                'docs' => 'https://github.com/settings/developers',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'linkedin' => [
                'label' => 'LinkedIn', 'region' => 'global', 'color' => '#0A66C2',
                'docs' => 'https://www.linkedin.com/developers/apps',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'x' => [
                'label' => 'X (Twitter)', 'region' => 'global', 'color' => '#000000',
                'docs' => 'https://developer.x.com/en/portal/dashboard',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'discord' => [
                'label' => 'Discord', 'region' => 'global', 'color' => '#5865F2',
                'docs' => 'https://discord.com/developers/applications',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'yandex' => [
                'label' => 'Yandex ID', 'region' => 'russia', 'color' => '#FC3F1D',
                'docs' => 'https://oauth.yandex.com/client/new',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'vk' => [
                'label' => 'VK ID', 'region' => 'russia', 'color' => '#0077FF',
                'docs' => 'https://id.vk.com/about/business/go',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'wechat' => [
                'label' => 'WeChat', 'region' => 'china', 'color' => '#07C160',
                'docs' => 'https://open.weixin.qq.com',
                'fields' => [
                    'client_id' => ['label' => 'App ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'App Secret', 'type' => 'password'],
                ],
            ],
            'huawei' => [
                'label' => 'Huawei ID', 'region' => 'china', 'color' => '#CF0A2C',
                'docs' => 'https://developer.huawei.com/consumer/en/console',
                'fields' => [
                    'client_id' => ['label' => 'App ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'App Secret', 'type' => 'password'],
                ],
            ],
            'alipay' => [
                'label' => 'Alipay', 'region' => 'china', 'color' => '#1677FF',
                'docs' => 'https://open.alipay.com',
                'fields' => [
                    'client_id' => ['label' => 'App ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Private Key', 'type' => 'textarea'],
                ],
            ],
            'kakao' => [
                'label' => 'Kakao', 'region' => 'korea', 'color' => '#FEE500',
                'docs' => 'https://developers.kakao.com/console/app',
                'fields' => [
                    'client_id' => ['label' => 'REST API Key', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'naver' => [
                'label' => 'Naver', 'region' => 'korea', 'color' => '#03C75A',
                'docs' => 'https://developers.naver.com/apps',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'line' => [
                'label' => 'LINE', 'region' => 'japan', 'color' => '#00C300',
                'docs' => 'https://developers.line.biz/console',
                'fields' => [
                    'client_id' => ['label' => 'Channel ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Channel Secret', 'type' => 'password'],
                ],
            ],
            'yahoo_japan' => [
                'label' => 'Yahoo! Japan', 'region' => 'japan', 'color' => '#FF0033',
                'docs' => 'https://developer.yahoo.co.jp',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'shad' => [
                'label' => 'Shad', 'region' => 'iran', 'color' => '#00B5A5',
                'docs' => 'https://shad.ir',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
            'donbaler' => [
                'label' => 'Donbaler', 'region' => 'iran', 'color' => '#6E3FFF',
                'docs' => 'https://donbaler.ir',
                'fields' => [
                    'client_id' => ['label' => 'Client ID', 'type' => 'text'],
                    'client_secret' => ['label' => 'Client Secret', 'type' => 'password'],
                ],
            ],
        ];
    }
}

if (!function_exists('rj_social_regions')) {
    function rj_social_regions(): array
    {
        return [
            'global' => ['label' => 'Global', 'flag' => '🌍'],
            'russia' => ['label' => 'Russia & CIS', 'flag' => '🇷🇺'],
            'china' => ['label' => 'China', 'flag' => '🇨🇳'],
            'korea' => ['label' => 'South Korea', 'flag' => '🇰🇷'],
            'japan' => ['label' => 'Japan', 'flag' => '🇯🇵'],
            'iran' => ['label' => 'Iran', 'flag' => '🇮🇷'],
        ];
    }
}

if (!function_exists('rj_menu_options')) {
    function rj_menu_options(): array
    {
        if (!class_exists(\App\Models\Menu::class)) {
            return [];
        }

        return \App\Models\Menu::orderBy('name')
            ->get()
            ->mapWithKeys(function ($menu) {
                $loc = $menu->location ? ' — ' . $menu->locationLabel() : '';
                return [$menu->id => $menu->name . $loc];
            })
            ->toArray();
    }
}

if (!function_exists('rj_menu_tree')) {
    function rj_menu_tree(): array
    {
        if (!class_exists(\App\Models\Menu::class)) {
            return [];
        }

        $menus = \App\Models\Menu::with('items.children')->get();
        $result = [];

        foreach ($menus as $menu) {
            $roots = $menu->items->whereNull('parent_id')->sortBy('sort_order')->values();

            $result[$menu->id] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'location' => $menu->location,
                'items' => $roots->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'label' => $item->label,
                        'url' => $item->resolvedUrl(),
                        'target' => $item->target,
                        'children' => $item->children->sortBy('sort_order')->values()->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'label' => $child->label,
                                'url' => $child->resolvedUrl(),
                                'target' => $child->target,
                                'children' => [],
                            ];
                        })->toArray(),
                    ];
                })->toArray(),
            ];
        }

        return $result;
    }
}
if (!function_exists('rj_global_colors')) {
    function rj_global_colors(): array
    {
        $raw = rj_get_option('global_colors', '[]');

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }
}

if (!function_exists('rj_global_fonts')) {
    function rj_global_fonts(): array
    {
        $raw = rj_get_option('global_fonts', '[]');

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }
}

if (!function_exists('rj_global_spacing')) {
    function rj_global_spacing(): array
    {
        $raw = rj_get_option('global_spacing', '[]');

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }
}

if (!function_exists('rj_global_color_css')) {
    function rj_global_color_css(): string
    {
        $vars = [];

        foreach (rj_global_colors() as $c) {
            if (empty($c['id']) || empty($c['color'])) {
                continue;
            }
            $id = preg_replace('/[^a-z0-9_-]/i', '', $c['id']);
            $color = preg_replace('/[^#\w(),.%\s-]/', '', $c['color']);
            $vars[] = '--rj-gc-' . $id . ':' . $color . ';';
        }

        return $vars ? ':root{' . implode('', $vars) . '}' : '';
    }
}
if (!function_exists('rj_breakpoints')) {
    function rj_breakpoints(): array
    {
        $raw = rj_get_option('rj_breakpoints', '[]');

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }
}

if (!function_exists('rj_breakpoint_css')) {
    function rj_breakpoint_css(): string
    {
        $bp = rj_breakpoints();

        $mobile = (int) ($bp['mobile']['max'] ?? 560);
        $tablet = (int) ($bp['tablet']['max'] ?? 860);

        return ":root{--rj-bp-mobile:{$mobile};--rj-bp-tablet:{$tablet};}";
    }
}