<?php

namespace App\Widgets;

use App\Widgets\Builtin\ContainerWidget;
use App\Widgets\Builtin\HeadingWidget;
use App\Widgets\Builtin\ImageWidget;
use App\Widgets\Builtin\LogoWidget;
use App\Widgets\Builtin\MenuWidget;
use App\Widgets\Builtin\TextWidget;

class WidgetRegistry
{
    protected static ?array $cache = null;

    public static function discover(): array
    {
        if (static::$cache !== null) {
            return static::$cache;
        }

        $builtin = [
            new HeadingWidget(),
            new TextWidget(),
            new ImageWidget(),
            new ContainerWidget(),
            new LogoWidget(),
            new MenuWidget(),
        ];

        $uploaded = static::discoverUploaded();

        static::$cache = array_merge($builtin, $uploaded);

        return static::$cache;
    }

    protected static function discoverUploaded(): array
    {
        return [];
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
        static::$cache = null;
    }

    public static function reset(): void
    {
        static::$cache = null;
    }
}