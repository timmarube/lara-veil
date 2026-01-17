<?php

namespace LaraVeil\UserManager\Providers;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'user-manager');

        // Register Sidebar Menu
        add_action('admin_menu', function() {
            echo \Illuminate\Support\Facades\Blade::render('
                <flux:sidebar.item icon="users" :href="route(\'admin.users.index\')" :current="request()->routeIs(\'admin.users.index\')">Users</flux:sidebar.item>
            ');
        });
    }
}
