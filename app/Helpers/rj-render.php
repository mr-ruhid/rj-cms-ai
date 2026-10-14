<?php

use App\Widgets\WidgetRegistry;

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