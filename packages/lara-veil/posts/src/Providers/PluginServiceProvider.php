<?php

namespace LaraVeil\Posts\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class PluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'posts');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Run migrations if they haven't been run
        if (!\Illuminate\Support\Facades\Schema::hasTable('posts')) {
             \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => 'packages/lara-veil/posts/database/migrations',
                '--force' => true,
             ]);
        }

        // Register Sidebar Menu
        add_action('admin_menu', function() {
            echo \Illuminate\Support\Facades\Blade::render('
                <flux:sidebar.group :heading="__(\'Content\')" class="grid">
                    <flux:sidebar.item icon="document-text" :href="route(\'admin.posts.index\')" :current="request()->routeIs(\'admin.posts.index\')">Posts</flux:sidebar.item>
                    <flux:sidebar.item icon="cog-6-tooth" :href="route(\'admin.posts.settings\')" :current="request()->routeIs(\'admin.posts.settings\')">Post Settings</flux:sidebar.item>
                </flux:sidebar.group>
            ');
        });
    }
}
