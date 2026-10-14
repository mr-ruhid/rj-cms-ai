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

        if (!empty($block['global_id'])) {
            $resolved = rj_resolve_global_widget((int) $block['global_id'], $block);
            if ($resolved !== '') {
                return $resolved;
            }
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

if (!function_exists('rj_resolve_global_widget')) {
    function rj_resolve_global_widget(int $id, array $context = []): string
    {
        static $stack = [];

        if (in_array($id, $stack, true)) {
            return '';
        }

        $widget = \App\Models\GlobalWidget::active()->find($id);

        if (!$widget) {
            return '';
        }

        $stack[] = $id;

        try {
            $block = $widget->toBlock();

            if (!empty($context['settings']) && is_array($context['settings'])) {
                $block['settings'] = array_merge($block['settings'] ?? [], $context['settings']);
            }

            $html = rj_render_block($block);
        } finally {
            array_pop($stack);
        }

        return $html;
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