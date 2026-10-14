<?php

use App\DynamicTags\TagRegistry;
use App\Widgets\WidgetRegistry;

if (!function_exists('rj_resolve_dynamic')) {
    function rj_resolve_dynamic($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        if (($value['source'] ?? '') !== 'dynamic') {
            return $value;
        }

        $tag = $value['tag'] ?? '';

        if ($tag === '') {
            return '';
        }

        $options = $value['options'] ?? [];

        if (!is_array($options)) {
            $options = [];
        }

        return TagRegistry::resolve($tag, $options);
    }
}

if (!function_exists('rj_resolve_dynamics')) {
    function rj_resolve_dynamics(array $settings): array
    {
        foreach ($settings as $key => $value) {
            if (is_array($value) && ($value['source'] ?? '') === 'dynamic') {
                $settings[$key] = rj_resolve_dynamic($value);
            }
        }

        return $settings;
    }
}

if (!function_exists('rj_render_block')) {
    function rj_render_block(array $block): string
    {
        $type = $block['type'] ?? '';

        if ($type === '') {
            return '';
        }

        $widget = WidgetRegistry::get($type);

        if (!$widget) {
            return '';
        }

        $settings = $block['settings'] ?? [];
        $settings = rj_resolve_dynamics($settings);

        $children = $block['children'] ?? [];

        if ($type === 'container') {
            return $widget->render($settings, $children);
        }

        return $widget->render($settings);
    }
}

if (!function_exists('rj_render_blocks')) {
    function rj_render_blocks(array $blocks): string
    {
        $html = '';

        foreach ($blocks as $block) {
            if (!is_array($block)) {
                continue;
            }

            $html .= rj_render_block($block);
        }

        return $html;
    }
}

if (!function_exists('rj_render_template')) {
    function rj_render_template($template): string
    {
        if (is_numeric($template)) {
            $template = \App\Models\Template::find($template);
        }

        if (!$template || !is_object($template)) {
            return '';
        }

        $content = $template->content ?? [];

        if (is_string($content)) {
            $content = json_decode($content, true) ?: [];
        }

        $blocks = $content['blocks'] ?? [];

        if (empty($blocks)) {
            return '';
        }

        return rj_render_blocks($blocks);
    }
}

if (!function_exists('rj_render_template_id')) {
    function rj_render_template_id(?int $id): string
    {
        if (!$id) {
            return '';
        }

        return rj_render_template($id);
    }
}

if (!function_exists('rj_do_shortcode')) {
    function rj_do_shortcode(string $code): string
    {
        return '';
    }
}