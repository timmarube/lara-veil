<?php

namespace Vendor\HelloWorld;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        add_action('admin_menu', function() {
            echo "<!-- Hello World Menu Added -->";
        });

        add_filter('the_title', function($title) {
            return $title . " - modified by plugin";
        });
    }
}
