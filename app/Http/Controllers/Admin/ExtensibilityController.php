<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plugin;
use App\Models\Theme;
use App\Core\PluginManager;
use App\Core\ThemeManager;
use Illuminate\Http\Request;

class ExtensibilityController extends Controller
{
    protected $pluginManager;
    protected $themeManager;

    public function __construct(PluginManager $pluginManager, ThemeManager $themeManager)
    {
        $this->pluginManager = $pluginManager;
        $this->themeManager = $themeManager;
    }

    /**
     * Display a listing of plugins.
     */
    public function plugins()
    {
        $this->pluginManager->syncPlugins();
        $plugins = Plugin::all();
        return view('admin.extensibility.plugins', compact('plugins'));
    }

    /**
     * Toggle plugin status.
     */
    public function togglePlugin(Plugin $plugin)
    {
        if ($plugin->status === 'active') {
            $this->pluginManager->deactivate($plugin->name);
            $message = "Plugin {$plugin->name} deactivated.";
        } else {
            $this->pluginManager->activate($plugin->name);
            $message = "Plugin {$plugin->name} activated.";
        }

        return back()->with('success', $message);
    }

    /**
     * Display a listing of themes.
     */
    public function themes()
    {
        $this->themeManager->syncThemes();
        $themes = Theme::all();
        return view('admin.extensibility.themes', compact('themes'));
    }

    /**
     * Activate a theme.
     */
    public function activateTheme(Theme $theme)
    {
        Theme::where('is_active', true)->update(['is_active' => false]);
        $theme->is_active = true;
        $theme->save();

        return back()->with('success', "Theme {$theme->name} activated.");
    }
}
