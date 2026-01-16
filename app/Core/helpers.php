<?php

use App\Core\HookSystem;

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10) {
        HookSystem::addAction($hook, $callback, $priority);
    }
}

if (!function_exists('do_action')) {
    function do_action($hook, ...$args) {
        HookSystem::doAction($hook, ...$args);
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10) {
        HookSystem::addFilter($hook, $callback, $priority);
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters($hook, $value, ...$args) {
        return HookSystem::applyFilters($hook, $value, ...$args);
    }
}

if (!function_exists('theme_asset')) {
    function theme_asset($path) {
        $activeTheme = app(\App\Core\ThemeManager::class)->getActiveTheme();
        $slug = $activeTheme['slug'] ?? 'default';
        return asset("themes/{$slug}/" . ltrim($path, '/'));
    }
}
