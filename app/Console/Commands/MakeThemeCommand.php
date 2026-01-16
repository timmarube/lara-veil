<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeThemeCommand extends Command
{
    protected $signature = 'make:theme {name}';
    protected $description = 'Create a new theme';

    public function handle()
    {
        $name = $this->argument('name');
        $slug = \Illuminate\Support\Str::slug($name);
        
        $path = base_path("themes/{$slug}");

        if (File::exists($path)) {
            $this->error("Theme already exists at {$path}");
            return;
        }

        File::makeDirectory($path . '/views/layouts', 0755, true);
        File::makeDirectory($path . '/assets/css', 0755, true);

        // theme.json
        $manifest = [
            'name' => $name,
            'slug' => $slug,
            'version' => '1.0.0',
            'author' => 'Lara-Veil Developer'
        ];
        File::put($path . '/theme.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // functions.php
        File::put($path . '/functions.php', "<?php\n\nadd_action('theme_enqueue_scripts', function() {\n    // wp_enqueue_style('{$slug}-style', theme_asset('css/style.css'));\n});\n");

        // index.blade.php
        File::put($path . '/views/index.blade.php', "@extends('theme::layouts.app')\n\n@section('content')\n    <h1>Welcome to {$name} Theme</h1>\n@endsection\n");

        // layouts/app.blade.php
        $layoutContent = "<!DOCTYPE html>\n<html>\n<head>\n    <title>@yield('title', '{$name}')</title>\n    @themeStyles()\n</head>\n<body>\n    @yield('content')\n    @themeScripts()\n</body>\n</html>\n";
        File::put($path . '/views/layouts/app.blade.php', $layoutContent);

        $this->info("Theme {$name} created successfully at {$path}");
    }
}
