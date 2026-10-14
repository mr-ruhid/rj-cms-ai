<?php

use App\Shortcodes\ShortcodeRegistry;

if (!function_exists('rj_add_shortcode')) {
    function rj_add_shortcode(string $tag, $callback): void
    {
        ShortcodeRegistry::add($tag, $callback);
    }
}

if (!function_exists('rj_remove_shortcode')) {
    function rj_remove_shortcode(string $tag): bool
    {
        return ShortcodeRegistry::remove($tag);
    }
}

if (!function_exists('rj_has_shortcode')) {
    function rj_has_shortcode(string $tag): bool
    {
        return ShortcodeRegistry::has($tag);
    }
}

if (!function_exists('rj_do_shortcode')) {
    function rj_do_shortcode(string $content): string
    {
        if (!is_string($content) || $content === '') {
            return (string) $content;
        }

        $content = ShortcodeRegistry::render($content);

        return rj_apply_filters('rj_shortcode_content', $content);
    }
}

if (!function_exists('rj_strip_shortcodes')) {
    function rj_strip_shortcodes(string $content): string
    {
        return ShortcodeRegistry::strip($content);
    }
}

if (!function_exists('rj_has_shortcodes')) {
    function rj_has_shortcodes(string $content): bool
    {
        return ShortcodeRegistry::exists($content);
    }
}