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

        // 3. Trigger System Init
        HookSystem::doAction('system.init');
    }
}
