<?php

use App\Models\Plugin;
use App\Services\PluginManager;

if (!function_exists('rj_plugin_active')) {
    function rj_plugin_active(string $slug): bool
    {
        return Plugin::where('slug', $slug)->where('status', 'active')->exists();
    }
}

if (!function_exists('rj_plugin_path')) {
    function rj_plugin_path(string $slug, string $append = ''): ?string
    {
        $plugin = Plugin::where('slug', $slug)->first();

        if (!$plugin) {
            return null;
        }

        return $plugin->path($append);
    }
}

if (!function_exists('rj_plugin_url')) {
    function rj_plugin_url(string $slug, string $path = ''): ?string
    {
        return app(PluginManager::class)->assetUrl($slug, $path);
    }
}

if (!function_exists('rj_plugin_setting')) {
    function rj_plugin_setting(string $slug, string $key, $default = null)
    {
        $plugin = Plugin::where('slug', $slug)->first();

        return $plugin?->getSetting($key, $default) ?? $default;
    }
}