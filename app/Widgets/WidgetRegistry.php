<?php

namespace App\Widgets;

use App\Widgets\Contracts\WidgetContract;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class WidgetRegistry
{
    protected static array $widgets = [];

    protected static bool $discovered = false;

    public static function register(string $widgetClass): void
    {
        if (!class_exists($widgetClass)) {
            Log::warning("Widget class does not exist: {$widgetClass}");
            return;
        }

        try {
            $instance = new $widgetClass();

            if (!$instance instanceof WidgetContract) {
                Log::warning("Widget does not implement WidgetContract: {$widgetClass}");
                return;
            }

            static::$widgets[$instance->id()] = $instance;
        } catch (\Throwable $e) {
            Log::error("Failed to register widget: {$widgetClass}", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function discover(): void
    {
        if (static::$discovered) {
            return;
        }

        static::discoverBuiltin();
        static::discoverUploaded();

        static::$discovered = true;
    }

    public static function all(): array
    {
        static::discover();

        return static::$widgets;
    }

    public static function get(string $id): ?WidgetContract
    {
        static::discover();

        return static::$widgets[$id] ?? null;
    }

    public static function has(string $id): bool
    {
        static::discover();

        return isset(static::$widgets[$id]);
    }

    public static function grouped(): array
    {
        $groups = [];

        foreach (static::all() as $widget) {
            $groups[$widget->category()][] = $widget;
        }

        ksort($groups);

        return $groups;
    }

    public static function categories(): array
    {
        return [
            'basic' => ['label' => __('Basic'), 'icon' => '📦'],
            'media' => ['label' => __('Media'), 'icon' => '🖼️'],
            'layout' => ['label' => __('Layout'), 'icon' => '📐'],
            'content' => ['label' => __('Content'), 'icon' => '📝'],
            'custom' => ['label' => __('Custom'), 'icon' => '🔌'],
        ];
    }

    public static function reset(): void
    {
        static::$widgets = [];
        static::$discovered = false;
    }

    protected static function discoverBuiltin(): void
    {
        $path = app_path('Widgets/Builtin');

        if (!is_dir($path)) {
            return;
        }

        $files = File::files($path);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $className = 'App\\Widgets\\Builtin\\' . $file->getFilenameWithoutExtension();

            static::register($className);
        }
    }

    protected static function discoverUploaded(): void
    {
        $path = storage_path('app/widgets');

        if (!is_dir($path)) {
            return;
        }

        $directories = File::directories($path);

        foreach ($directories as $dir) {
            $manifest = $dir . '/widget.json';

            if (!file_exists($manifest)) {
                continue;
            }

            try {
                $data = json_decode(file_get_contents($manifest), true);

                if (empty($data['class']) || empty($data['entry'])) {
                    continue;
                }

                $entryFile = $dir . '/' . $data['entry'];

                if (!file_exists($entryFile)) {
                    continue;
                }

                require_once $entryFile;

                if (class_exists($data['class'])) {
                    static::register($data['class']);
                }
            } catch (\Throwable $e) {
                Log::error("Failed to load uploaded widget: {$dir}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}