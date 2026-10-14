<?php

use App\Hooks\HookRegistry;

if (!function_exists('rj_add_action')) {
    function rj_add_action(string $hook, $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        HookRegistry::addAction($hook, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('rj_do_action')) {
    function rj_do_action(string $hook, ...$args): void
    {
        HookRegistry::doAction($hook, ...$args);
    }
}

if (!function_exists('rj_remove_action')) {
    function rj_remove_action(string $hook, $callback, int $priority = 10): bool
    {
        return HookRegistry::removeAction($hook, $callback, $priority);
    }
}

if (!function_exists('rj_has_action')) {
    function rj_has_action(string $hook, $callback = null): bool
    {
        return HookRegistry::hasAction($hook, $callback);
    }
}

if (!function_exists('rj_add_filter')) {
    function rj_add_filter(string $hook, $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        HookRegistry::addFilter($hook, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('rj_apply_filters')) {
    function rj_apply_filters(string $hook, $value, ...$args)
    {
        return HookRegistry::applyFilters($hook, $value, ...$args);
    }
}

if (!function_exists('rj_remove_filter')) {
    function rj_remove_filter(string $hook, $callback, int $priority = 10): bool
    {
        return HookRegistry::removeFilter($hook, $callback, $priority);
    }
}

if (!function_exists('rj_has_filter')) {
    function rj_has_filter(string $hook, $callback = null): bool
    {
        return HookRegistry::hasFilter($hook, $callback);
    }
}

if (!function_exists('rj_doing_filter')) {
    function rj_doing_filter(?string $hook = null)
    {
        return HookRegistry::doingFilter($hook);
    }
}