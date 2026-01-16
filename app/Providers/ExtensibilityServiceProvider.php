<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Core\PluginManager;
use App\Core\ThemeManager;
use App\Core\HookSystem;

class ExtensibilityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Ensure helpers are loaded
        require_once __DIR__.'/../Core/helpers.php';

        $this->app->singleton(PluginManager::class, function ($app) {
            return new PluginManager();
        });

        $this->app->singleton(ThemeManager::class, function ($app) {
            return new ThemeManager();
        });

        $this->app->singleton(\App\Core\AssetManager::class, function ($app) {
            return new \App\Core\AssetManager();
        });

        // Register the singleton for facade-like access if needed
        $this->app->singleton('hook.system', function ($app) {
            return new HookSystem();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 1. Load Plugins
        $pluginManager = $this->app->make(PluginManager::class);
        $pluginManager->loadPlugins();

        // 2. Load Active Theme
        $themeManager = $this->app->make(ThemeManager::class);
        $themeManager->loadTheme();

        // Register Blade Directives
        \Illuminate\Support\Facades\Blade::directive('themeStyles', function () {
            return "<?php app(\App\Core\AssetManager::class)->renderStyles(); ?>";
        });

        \Illuminate\Support\Facades\Blade::directive('themeScripts', function ($expression) {
            $footer = $expression ?: 'false';
            return "<?php app(\App\Core\AssetManager::class)->renderScripts($footer); ?>";
        });

        // Trigger Enqueue Hook (only in web requests)
        if (!app()->runningInConsole()) {
            HookSystem::doAction('theme_enqueue_scripts');
        }

        // 3. Trigger System Init
        HookSystem::doAction('system.init');
    }
}
