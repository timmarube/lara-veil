<?php

namespace App\Core;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PluginManager
{
    protected $path;
    protected $plugins = [];

    public function __construct()
    {
        $this->path = base_path('packages');
    }

    /**
     * Load all active plugins.
     */
    public function loadPlugins()
    {
        if (!File::exists($this->path)) {
            return;
        }

        if (!\Illuminate\Support\Facades\Schema::hasTable('plugins')) {
            return;
        }

        $activePlugins = \App\Models\Plugin::where('status', 'active')->pluck('name')->toArray();

        $vendors = File::directories($this->path);

        foreach ($vendors as $vendorDir) {
            $plugins = File::directories($vendorDir);
            foreach ($plugins as $pluginDir) {
                $manifestPath = $pluginDir . DIRECTORY_SEPARATOR . 'plugin.json';
                if (!File::exists($manifestPath)) continue;
                
                $manifest = json_decode(File::get($manifestPath), true);
                $name = $manifest['name'] ?? basename($pluginDir);

                if (in_array($name, $activePlugins)) {
                    $this->loadPluginFromDirectory($pluginDir, $manifest);
                }
            }
        }
    }

    /**
     * Sync discovered plugins with the database.
     */
    public function syncPlugins()
    {
        if (!File::exists($this->path)) return [];

        $discovered = [];
        $vendors = File::directories($this->path);

        foreach ($vendors as $vendorDir) {
            $plugins = File::directories($vendorDir);
            foreach ($plugins as $pluginDir) {
                $manifestPath = $pluginDir . DIRECTORY_SEPARATOR . 'plugin.json';
                if (!File::exists($manifestPath)) continue;

                $manifest = json_decode(File::get($manifestPath), true);
                if (!$manifest) continue;

                $name = $manifest['name'] ?? basename($pluginDir);
                $discovered[] = $name;

                \App\Models\Plugin::updateOrCreate(
                    ['name' => $name],
                    [
                        'namespace' => $manifest['namespace'] ?? '',
                        'version' => $manifest['version'] ?? '1.0.0',
                    ]
                );
            }
        }
        return $discovered;
    }

    /**
     * Load a plugin from a directory.
     *
     * @param string $directory
     * @param array|null $manifest
     */
    protected function loadPluginFromDirectory($directory, $manifest = null)
    {
        if (!$manifest) {
            $manifestPath = $directory . DIRECTORY_SEPARATOR . 'plugin.json';
            if (!File::exists($manifestPath)) return;
            $manifest = json_decode(File::get($manifestPath), true);
        }

        if (!$manifest) {
            Log::error("Invalid manifest for plugin in directory: {$directory}");
            return;
        }

        $id = $manifest['name'] ?? basename($directory);
        $this->plugins[$id] = $manifest;
        $this->plugins[$id]['path'] = $directory;

        // Autoloading
        if (isset($manifest['autoload']['psr-4'])) {
            foreach ($manifest['autoload']['psr-4'] as $namespace => $path) {
                $fullPath = $directory . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
                $this->registerAutoloader($namespace, $fullPath);
            }
        }

        // Register Service Providers
        if (isset($manifest['providers'])) {
            foreach ($manifest['providers'] as $provider) {
                app()->register($provider);
            }
        }
    }

    /**
     * Register a simple PSR-4 autoloader for the plugin.
     *
     * @param string $namespace
     * @param string $path
     */
    protected function registerAutoloader($namespace, $path)
    {
        spl_autoload_register(function ($class) use ($namespace, $path) {
            if (strpos($class, $namespace) === 0) {
                $relativeClass = substr($class, strlen($namespace));
                $file = $path . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
                if (File::exists($file)) {
                    require $file;
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Activate a plugin.
     *
     * @param string $name
     * @return bool
     */
    public function activate($name)
    {
        $plugin = \App\Models\Plugin::where('name', $name)->first();
        if (!$plugin) return false;

        $plugin->status = 'active';
        $plugin->save();

        HookSystem::doAction('plugin_activated', $name);
        return true;
    }

    /**
     * Deactivate a plugin.
     *
     * @param string $name
     * @return bool
     */
    public function deactivate($name)
    {
        $plugin = \App\Models\Plugin::where('name', $name)->first();
        if (!$plugin) return false;

        $plugin->status = 'inactive';
        $plugin->save();

        HookSystem::doAction('plugin_deactivated', $name);
        return true;
    }

    /**
     * Get all loaded plugins.
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}
