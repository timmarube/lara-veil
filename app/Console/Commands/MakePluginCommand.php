<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakePluginCommand extends Command
{
    protected $signature = 'make:plugin {name} {vendor=lara-veil}';
    protected $description = 'Create a new plugin';

    public function handle()
    {
        $name = $this->argument('name');
        $vendor = $this->argument('vendor');
        $slug = \Illuminate\Support\Str::slug($name);
        $namespace = ucfirst($vendor) . '\\' . \Illuminate\Support\Str::studly($name);
        
        $path = base_path("packages/{$vendor}/{$slug}");

        if (File::exists($path)) {
            $this->error("Plugin already exists at {$path}");
            return;
        }

        File::makeDirectory($path . '/src', 0755, true);
        File::makeDirectory($path . '/routes', 0755, true);
        File::makeDirectory($path . '/resources/views', 0755, true);

        // plugin.json
        $manifest = [
            'name' => $slug,
            'title' => $name,
            'namespace' => $namespace . '\\',
            'version' => '1.0.0',
            'autoload' => [
                'psr-4' => [
                    $namespace . '\\' => 'src/'
                ]
            ],
            'providers' => [
                $namespace . '\\Providers\\PluginServiceProvider'
            ]
        ];
        File::put($path . '/plugin.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // PluginServiceProvider
        $providerContent = "<?php\n\nnamespace {$namespace}\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\n\nclass PluginServiceProvider extends ServiceProvider\n{\n    public function boot(): void\n    {\n        \$this->loadRoutesFrom(__DIR__.'/../routes/web.php');\n        \$this->loadViewsFrom(__DIR__.'/../resources/views', '{$slug}');\n    }\n}\n";
        File::makeDirectory($path . '/src/Providers', 0755, true);
        File::put($path . "/src/Providers/PluginServiceProvider.php", $providerContent);

        // web.php
        File::put($path . '/routes/web.php', "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::get('{$slug}', function() {\n    return 'Hello from {$name} plugin!';\n});\n");

        $this->info("Plugin {$name} created successfully at {$path}");
    }
}
