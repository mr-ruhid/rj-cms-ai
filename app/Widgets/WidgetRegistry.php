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

    protected static function icon(string $path): string
    {
        return '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">'
            . '<path stroke-linecap="round" stroke-linejoin="round" d="' . $path . '"/></svg>';
    }

    public static function categories(): array
    {
        return [
            'header' => [
                'label' => __('Header'),
                'icon' => static::icon('M4 6h16M4 12h16M4 18h16'),
            ],
            'basic' => [
                'label' => __('Basic'),
                'icon' => static::icon('M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'),
            ],
            'general' => [
                'label' => __('General'),
                'icon' => static::icon('M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'),
            ],
            'layout' => [
                'label' => __('Layout'),
                'icon' => static::icon('M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z'),
            ],
            'navigation' => [
                'label' => __('Navigation'),
                'icon' => static::icon('M4 6h16M4 12h8m-8 6h16'),
            ],
            'media' => [
                'label' => __('Media'),
                'icon' => static::icon('M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'),
            ],
            'gallery' => [
                'label' => __('Gallery & Slider'),
                'icon' => static::icon('M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'),
            ],
            'video' => [
                'label' => __('Video & Audio'),
                'icon' => static::icon('M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'),
            ],
            'content' => [
                'label' => __('Content'),
                'icon' => static::icon('M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'),
            ],
            'blog' => [
                'label' => __('Blog & Posts'),
                'icon' => static::icon('M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'),
            ],
            'marketing' => [
                'label' => __('Marketing'),
                'icon' => static::icon('M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'),
            ],
            'testimonials' => [
                'label' => __('Testimonials'),
                'icon' => static::icon('M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'),
            ],
            'pricing' => [
                'label' => __('Pricing'),
                'icon' => static::icon('M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'),
            ],
            'team' => [
                'label' => __('Team'),
                'icon' => static::icon('M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'),
            ],
            'maps' => [
                'label' => __('Maps & Location'),
                'icon' => static::icon('M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'),
            ],
            'ecommerce' => [
                'label' => __('E-commerce'),
                'icon' => static::icon('M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'),
            ],
            'forms' => [
                'label' => __('Forms'),
                'icon' => static::icon('M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'),
            ],
            'social' => [
                'label' => __('Social'),
                'icon' => static::icon('M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'),
            ],
            'interactive' => [
                'label' => __('Interactive'),
                'icon' => static::icon('M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122'),
            ],
            'theme' => [
                'label' => __('Theme'),
                'icon' => static::icon('M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'),
            ],
            'advanced' => [
                'label' => __('Advanced'),
                'icon' => static::icon('M13 10V3L4 14h7v7l9-11h-7z'),
            ],
            'custom' => [
                'label' => __('Custom'),
                'icon' => static::icon('M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z'),
            ],
        ];
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
