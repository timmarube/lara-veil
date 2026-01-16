<?php

namespace App\Core;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class ThemeManager
{
    protected $path;
    protected $activeTheme;

    public function __construct()
    {
        $this->path = base_path('themes');
    }

    /**
     * Load the active theme.
     *
     * @param string|null $themeName
     */
    public function loadTheme($themeName = null)
    {
        if (!$themeName) {
            if (\Illuminate\Support\Facades\Schema::hasTable('themes')) {
                $activeThemeModel = \App\Models\Theme::where('is_active', true)->first();
                $themeName = $activeThemeModel ? $activeThemeModel->slug : env('THEME_DEFAULT', 'default');
            } else {
                $themeName = env('THEME_DEFAULT', 'default');
            }
        }

        $themePath = $this->path . DIRECTORY_SEPARATOR . $themeName;

        if (!File::isDirectory($themePath)) {
            Log::warning("Theme not found: {$themeName}");
            // Fallback to default if not found
            if ($themeName !== 'default') {
                $this->loadTheme('default');
            }
            return;
        }

        $manifestPath = $themePath . DIRECTORY_SEPARATOR . 'theme.json';
        if (File::exists($manifestPath)) {
            $manifest = json_decode(File::get($manifestPath), true);
            $this->activeTheme = $manifest;
        } else {
            $this->activeTheme = ['name' => ucfirst($themeName), 'slug' => $themeName];
        }

        // Add theme views to the view finder
        $viewPath = $themePath . DIRECTORY_SEPARATOR . 'views';
        if (File::isDirectory($viewPath)) {
            // Prepend the theme view path so it overrides core views
            View::prependLocation($viewPath);
            
            // Also register with a namespace
            View::addNamespace('theme', $viewPath);
        }

        // Load theme functions if they exist
        $functionsPath = $themePath . DIRECTORY_SEPARATOR . 'functions.php';
        if (File::exists($functionsPath)) {
            require_once $functionsPath;
        }

        HookSystem::doAction('theme_loaded', $themeName);
    }

    /**
     * Sync discovered themes with the database.
     */
    public function syncThemes()
    {
        if (!File::exists($this->path)) return [];

        $discovered = [];
        $themes = File::directories($this->path);

        foreach ($themes as $themeDir) {
            $slug = basename($themeDir);
            $manifestPath = $themeDir . DIRECTORY_SEPARATOR . 'theme.json';
            
            $name = ucfirst($slug);
            if (File::exists($manifestPath)) {
                $manifest = json_decode(File::get($manifestPath), true);
                if ($manifest) {
                    $name = $manifest['name'] ?? $name;
                }
            }

            $discovered[] = $slug;

            \App\Models\Theme::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
        return $discovered;
    }

    /**
     * Get active theme data.
     *
     * @return array|null
     */
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }
}
