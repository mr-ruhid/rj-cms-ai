<?php

namespace App\Hooks;

class HookRegistry
{
    protected static array $actions = [];

    protected static array $filters = [];

    protected static array $currentFilter = [];

    public static function addAction(string $hook, $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        static::$actions[$hook][$priority][] = [
            'callback' => $callback,
            'args' => $acceptedArgs,
        ];

        ksort(static::$actions[$hook]);
    }

    public static function addFilter(string $hook, $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        static::$filters[$hook][$priority][] = [
            'callback' => $callback,
            'args' => $acceptedArgs,
        ];

        ksort(static::$filters[$hook]);
    }

    public static function doAction(string $hook, ...$args): void
    {
        if (empty(static::$actions[$hook])) {
            return;
        }

        foreach (static::$actions[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $entry) {
                $accepted = $entry['args'];

                $slice = array_slice($args, 0, $accepted > 0 ? $accepted : count($args));

                try {
                    call_user_func_array($entry['callback'], $slice);
                } catch (\Throwable $e) {
                    logger()->error("Hook action [{$hook}] failed: " . $e->getMessage(), [
                        'hook' => $hook,
                        'priority' => $priority,
                        'exception' => $e,
                    ]);
                }
            }
        }
    }

    public static function applyFilters(string $hook, $value, ...$args)
    {
        if (empty(static::$filters[$hook])) {
            return $value;
        }

        static::$currentFilter[] = $hook;

        foreach (static::$filters[$hook] as $priority => $callbacks) {
            foreach ($callbacks as $entry) {
                $accepted = $entry['args'];

                $slice = array_slice($args, 0, max(0, $accepted - 1));

                try {
                    $value = call_user_func_array($entry['callback'], array_merge([$value], $slice));
                } catch (\Throwable $e) {
                    logger()->error("Hook filter [{$hook}] failed: " . $e->getMessage(), [
                        'hook' => $hook,
                        'priority' => $priority,
                        'exception' => $e,
                    ]);
                }
            }
        }

        array_pop(static::$currentFilter);

        return $value;
    }

    public static function removeAction(string $hook, $callback, int $priority = 10): bool
    {
        if (empty(static::$actions[$hook][$priority])) {
            return false;
        }

        $removed = false;

        static::$actions[$hook][$priority] = array_filter(
            static::$actions[$hook][$priority],
            function ($entry) use ($callback, &$removed) {
                if (static::callbacksMatch($entry['callback'], $callback)) {
                    $removed = true;
                    return false;
                }
                return true;
            }
        );

        if (empty(static::$actions[$hook][$priority])) {
            unset(static::$actions[$hook][$priority]);
        }

        return $removed;
    }

    public static function removeFilter(string $hook, $callback, int $priority = 10): bool
    {
        if (empty(static::$filters[$hook][$priority])) {
            return false;
        }

        $removed = false;

        static::$filters[$hook][$priority] = array_filter(
            static::$filters[$hook][$priority],
            function ($entry) use ($callback, &$removed) {
                if (static::callbacksMatch($entry['callback'], $callback)) {
                    $removed = true;
                    return false;
                }
                return true;
            }
        );

        if (empty(static::$filters[$hook][$priority])) {
            unset(static::$filters[$hook][$priority]);
        }

        return $removed;
    }

    public static function hasAction(string $hook, $callback = null): bool
    {
        if (empty(static::$actions[$hook])) {
            return false;
        }

        if ($callback === null) {
            return true;
        }

        foreach (static::$actions[$hook] as $callbacks) {
            foreach ($callbacks as $entry) {
                if (static::callbacksMatch($entry['callback'], $callback)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function hasFilter(string $hook, $callback = null): bool
    {
        if (empty(static::$filters[$hook])) {
            return false;
        }

        if ($callback === null) {
            return true;
        }

        foreach (static::$filters[$hook] as $callbacks) {
            foreach ($callbacks as $entry) {
                if (static::callbacksMatch($entry['callback'], $callback)) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function doingFilter(?string $hook = null): bool|string
    {
        if ($hook === null) {
            return !empty(static::$currentFilter);
        }

        return in_array($hook, static::$currentFilter, true);
    }

    public static function didAction(string $hook): int
    {
        return static::$actionCounts[$hook] ?? 0;
    }

    protected static array $actionCounts = [];

    public static function actions(): array
    {
        return static::$actions;
    }

    public static function filters(): array
    {
        return static::$filters;
    }

    public static function clear(): void
    {
        static::$actions = [];
        static::$filters = [];
        static::$currentFilter = [];
        static::$actionCounts = [];
    }

    protected static function callbacksMatch($a, $b): bool
    {
        if ($a === $b) {
            return true;
        }

        if (is_string($a) && is_string($b)) {
            return strcasecmp($a, $b) === 0;
        }

        if (is_array($a) && is_array($b)) {
            return count($a) === 2 && count($b) === 2
                && $a[0] === $b[0]
                && $a[1] === $b[1];
        }

        return false;
    }
}