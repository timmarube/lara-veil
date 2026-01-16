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

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src, $deps = [], $ver = false, $media = 'all') {
        app(\App\Core\AssetManager::class)->enqueueStyle($handle, $src, $deps, $ver, $media);
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src, $deps = [], $ver = false, $inFooter = false) {
        app(\App\Core\AssetManager::class)->enqueueScript($handle, $src, $deps, $ver, $inFooter);
    }
}

if (!function_exists('add_theme_support')) {
    function add_theme_support($feature, ...$args) {
        // Mock implementation for now
    }
}

if (!function_exists('register_nav_menus')) {
    function register_nav_menus($locations) {
        // Mock implementation for now
    }
}

if (!function_exists('theme_asset_url')) {
    function theme_asset_url($path) {
        return theme_asset($path);
    }
}

if (!function_exists('esc_html')) { function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); } }
if (!function_exists('esc_attr')) { function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); } }
if (!function_exists('esc_url')) { function esc_url($url) { return filter_var($url, FILTER_SANITIZE_URL); } }
if (!function_exists('__')) { 
    // Laravel already has __, but if we need a text domain version:
    function lara_veil_translate($text, $domain = 'default') {
        return __($text);
    }
}
