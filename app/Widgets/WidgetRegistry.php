<?php

namespace App\Widgets;

use App\Models\UploadedWidget;
use App\Widgets\Builtin\ContainerWidget;
use App\Widgets\Builtin\FormWidget;
use App\Widgets\Builtin\HeadingWidget;
use App\Widgets\Builtin\ImageWidget;
use App\Widgets\Builtin\LogoWidget;
use App\Widgets\Builtin\MenuWidget;
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
            new FormWidget(),
        ];

        $uploaded = static::discoverUploaded();

        static::$cache = array_merge($builtin, $uploaded);

        Cache::put($cacheKey, static::$cache, 3600);

        return static::$cache;
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
        return [
            'header' => ['label' => __('Header'), 'icon' => '🎯'],
            'basic' => ['label' => __('Basic'), 'icon' => '📦'],
            'media' => ['label' => __('Media'), 'icon' => '🖼️'],
            'layout' => ['label' => __('Layout'), 'icon' => '📐'],
            'content' => ['label' => __('Content'), 'icon' => '📝'],
            'custom' => ['label' => __('Custom'), 'icon' => '🔌'],
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
                'source' => static::isUploaded($widget) ? 'uploaded' : 'builtin',
            ];
        }

        return $out;
    }

    protected static function isUploaded(BaseWidget $widget): bool
    {
        return str_starts_with(get_class($widget), 'RjWidgets\\')
            || !in_array(get_class($widget), static::builtinClasses(), true);
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
            FormWidget::class,
        ];
    }
}