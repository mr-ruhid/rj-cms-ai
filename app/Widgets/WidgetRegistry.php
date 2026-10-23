<?php

namespace App\Widgets;

use App\Models\UploadedWidget;
use App\Widgets\Builtin\ContainerWidget;
use App\Widgets\Builtin\HeadingWidget;
use App\Widgets\Builtin\ImageWidget;
use App\Widgets\Builtin\LogoWidget;
use App\Widgets\Builtin\MenuWidget;
use App\Widgets\Builtin\PostsWidget;
use App\Widgets\Builtin\TextWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class WidgetRegistry
{
    protected static ?array $cache = null;

    public static function discover(): array
    {
        if (static::$cache !== null) {
            return static::$cache;
        }

        $cacheKey = 'rj_widget_registry';

        if (Cache::has($cacheKey)) {
            static::$cache = Cache::get($cacheKey);
            return static::$cache;
        }

        $builtin = [
            new HeadingWidget(),
            new TextWidget(),
            new ImageWidget(),
            new ContainerWidget(),
            new LogoWidget(),
            new MenuWidget(),
            new PostsWidget(),
        ];

        $plugin = static::discoverFromPlugins();
        $uploaded = static::discoverUploaded();

        $all = array_merge($builtin, $plugin, $uploaded);
        $unique = [];

        foreach ($all as $widget) {
            if (!$widget instanceof BaseWidget) {
                continue;
            }

            $unique[$widget->id()] = $widget;
        }

        static::$cache = array_values($unique);

        Cache::put($cacheKey, static::$cache, 3600);

        return static::$cache;
    }

    protected static function discoverFromPlugins(): array
    {
        try {
            $widgets = rj_apply_filters('builder.widgets', []);

            return is_array($widgets) ? array_filter($widgets, fn($w) => $w instanceof BaseWidget) : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected static function discoverUploaded(): array
    {
        if (!class_exists(UploadedWidget::class)) {
            return [];
        }

        try {
            static::loadFiles();

            $widgets = [];

            foreach (UploadedWidget::active()->get() as $record) {
                $class = $record->class;

                if (!class_exists($class)) {
                    continue;
                }

                try {
                    $instance = new $class();

                    if (!$instance instanceof BaseWidget) {
                        continue;
                    }

                    $widgets[] = $instance;
                } catch (\Throwable $e) {
                    $record->markAsError($e->getMessage());
                }
            }

            return $widgets;
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected static function loadFiles(): void
    {
        $basePath = base_path('rj-content/widgets');

        if (!File::isDirectory($basePath)) {
            return;
        }

        foreach (UploadedWidget::active()->get() as $record) {
            $file = $record->mainFilePath();

            if (!File::exists($file)) {
                continue;
            }

            try {
                require_once $file;
            } catch (\Throwable $e) {
                $record->markAsError($e->getMessage());
            }
        }
    }

    public static function all(): array
    {
        return static::discover();
    }

    public static function get(string $id): ?BaseWidget
    {
        foreach (static::discover() as $widget) {
            if ($widget->id() === $id) {
                return $widget;
            }
        }

        return null;
    }

    public static function has(string $id): bool
    {
        return static::get($id) !== null;
    }

    public static function grouped(): array
    {
        $groups = [];

        foreach (static::discover() as $widget) {
            $groups[$widget->category()][] = $widget;
        }

        return $groups;
    }

    public static function categories(): array
    {
        $categories = [
            'header' => [
                'label' => __('Header'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>'
            ],
            'basic' => [
                'label' => __('Basic'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'
            ],
            'media' => [
                'label' => __('Media'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
            ],
            'layout' => [
                'label' => __('Layout'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>'
            ],
            'content' => [
                'label' => __('Content'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
            ],
            'ecommerce' => [
                'label' => __('E-commerce'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
            ],
            'forms' => [
                'label' => __('Forms'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'
            ],
            'social' => [
                'label' => __('Social'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
            ],
            'advanced' => [
                'label' => __('Advanced'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
            ],
            'custom' => [
                'label' => __('Custom'),
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>'
            ],
        ];

        $activeCategories = [];
        foreach (static::discover() as $widget) {
            $activeCategories[] = $widget->category();
        }
        $activeCategories = array_unique($activeCategories);

        foreach ($activeCategories as $cat) {
            if (!isset($categories[$cat])) {
                $label = ucwords(str_replace(['-', '_'], ' ', $cat));
                $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>';

                $categories[$cat] = [
                    'label' => __($label),
                    'icon' => $icon
                ];
            }
        }

        return rj_apply_filters('builder.widget_categories', $categories);
    }

    public static function register(BaseWidget $widget): void
    {
        static::reset();
    }

    public static function reset(): void
    {
        static::$cache = null;
        Cache::forget('rj_widget_registry');
    }

    public static function toArray(): array
    {
        $out = [];

        foreach (static::discover() as $widget) {
            $out[] = [
                'id' => $widget->id(),
                'name' => $widget->name(),
                'category' => $widget->category(),
                'icon' => $widget->icon(),
                'description' => $widget->description(),
                'schema' => $widget->fullSchema(),
                'defaults' => $widget->fullDefaults(),
                'source' => static::widgetSource($widget),
            ];
        }

        return $out;
    }

    protected static function widgetSource(BaseWidget $widget): string
    {
        $class = get_class($widget);

        if (str_starts_with($class, 'RjPlugins\\') || str_starts_with($class, 'RjCommerce\\')) {
            return 'plugin';
        }

        if (str_starts_with($class, 'RjWidgets\\')) {
            return 'uploaded';
        }

        if (in_array($class, static::builtinClasses(), true)) {
            return 'builtin';
        }

        return 'custom';
    }

    protected static function builtinClasses(): array
    {
        return [
            HeadingWidget::class,
            TextWidget::class,
            ImageWidget::class,
            ContainerWidget::class,
            LogoWidget::class,
            MenuWidget::class,
            PostsWidget::class,
        ];
    }
}
