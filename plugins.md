
# Plugin System Documentation

## Introduction

Plugins extend Laravel applications with additional functionality without modifying core code. They can add features, modify behavior, or integrate with external services.

## Plugin Structure

### Basic Structure

```text
my-plugin/
├── plugin.json         # Plugin manifest
├── composer.json       # Composer dependencies
├── README.md           # Plugin documentation
├── src/
│   ├── Plugin.php      # Main plugin class
│   ├── Providers/
│   │   └── PluginServiceProvider.php
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   └── Hooks/
├── routes/
│   ├── web.php
│   └── api.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   ├── lang/
│   └── assets/
├── config/
│   └── config.php
└── tests/
```


### Plugin Manifest (plugin.json)
```json
{
    "name": "my-plugin",
    "title": "My Plugin",
    "namespace": "Vendor\\MyPlugin",
    "version": "1.0.0",
    "description": "A brief description of the plugin",
    "author": "Your Name",
    "license": "MIT",
    "require": {
        "laravel/framework": "^9.0|^10.0",
        "php": "^8.1"
    },
    "autoload": {
        "psr-4": {
            "Vendor\\MyPlugin\\": "src/"
        }
    },
    "providers": [
        "Vendor\\MyPlugin\\Providers\\PluginServiceProvider"
    ],
    "hooks": {
        "admin_menu": "Vendor\\MyPlugin\\Hooks\\AdminMenu@handle",
        "user_registered": "Vendor\\MyPlugin\\Hooks\\UserRegistered@handle"
    },
    "routes": {
        "web": "routes/web.php",
        "api": "routes/api.php"
    },
    "settings": {
        "has_settings_page": true,
        "menu_position": 25,
        "capability": "manage_options"
    }
}

## Plugin Development

### 1. Creating a Plugin
```bash
php artisan make:plugin MyPlugin
```

This creates:
```text
packages/vendor/my-plugin/
├── plugin.json
├── src/
│   ├── Plugin.php
│   └── Providers/
│       └── PluginServiceProvider.php
└── README.md
```

2. Plugin Service Provider
php

<?php

namespace Vendor\MyPlugin\Providers;

use Illuminate\Support\ServiceProvider;
use Vendor\MyPlugin\Plugin;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/config.php', 'myplugin'
        );
        
        $this->app->singleton('myplugin', function($app) {
            return new Plugin();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'myplugin');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        
        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'myplugin');
        
        // Publish assets
        $this->publishes([
            __DIR__.'/../../resources/assets' => public_path('vendor/myplugin'),
        ], 'myplugin-assets');
        
        // Register hooks
        $this->registerHooks();
    }
    
    /**
     * Register plugin hooks.
     */
    protected function registerHooks(): void
    {
        $hooks = config('myplugin.hooks', []);
        
        foreach ($hooks as $hook => $handler) {
            HookSystem::addAction($hook, $handler);
        }
    }
}

### 3. Main Plugin Class
```php
<?php

namespace Vendor\MyPlugin;

class Plugin
{
    /**
     * Plugin activation hook.
     */
    public function activate(): void
    {
        // Create database tables
        // Set default options
        // Schedule cron jobs
    }
    
    /**
     * Plugin deactivation hook.
     */
    public function deactivate(): void
    {
        // Remove cron jobs
        // Clean up temporary data
    }
    
    /**
     * Plugin uninstall hook.
     */
    public function uninstall(): void
    {
        // Remove database tables
        // Remove options
        // Remove uploaded files
    }
}
```

## Hooks System

### Available Hooks

#### Admin Hooks
```php
// Add menu items
HookSystem::addAction('admin_menu', function($menu) {
    $menu->add('My Plugin', 'plugin.dashboard')
         ->icon('plugin')
         ->order(25);
});

// Add dashboard widgets
HookSystem::addAction('dashboard_widgets', function($widgets) {
    $widgets->add(new MyPluginWidget());
});

// Add admin notices
HookSystem::addAction('admin_notices', function() {
    if ($someCondition) {
        echo '<div class="notice notice-warning">Warning message</div>';
    }
});
```

#### Content Hooks
```php
// Filter post content
HookSystem::addFilter('the_content', function($content) {
    return $this->processContent($content);
});

// Modify post title
HookSystem::addFilter('the_title', function($title, $postId) {
    return strtoupper($title);
}, 10, 2);

// Add meta tags
HookSystem::addAction('wp_head', function() {
    echo '<meta name="myplugin" content="value">';
});
```

#### User Hooks
```php
// User registration
HookSystem::addAction('user_registered', function($userId) {
    $this->sendWelcomeEmail($userId);
});

// User login
HookSystem::addAction('user_login', function($userId, $user) {
    $this->logLogin($userId);
}, 10, 2);

// User profile update
HookSystem::addAction('profile_update', function($userId, $oldData) {
    $this->syncUserData($userId);
}, 10, 2);
```

#### System Hooks
```php
// Plugin activation
HookSystem::addAction('plugin_activated', function($plugin) {
    if ($plugin === 'my-plugin') {
        $this->setupPlugin();
    }
});

// Plugin deactivation
HookSystem::addAction('plugin_deactivated', function($plugin) {
    if ($plugin === 'my-plugin') {
        $this->cleanup();
    }
});
```

### Creating Custom Hooks
```php
// Define a hook in your plugin
class OrderProcessor
{
    public function process(Order $order)
    {
        // Allow other plugins to modify order before processing
        $order = HookSystem::applyFilters('myplugin_order_before_process', $order);
        
        // Process order
        $result = $this->doProcess($order);
        
        // Allow other plugins to modify result
        $result = HookSystem::applyFilters('myplugin_order_after_process', $result);
        
        // Notify other plugins about processed order
        HookSystem::doAction('myplugin_order_processed', $order, $result);
        
        return $result;
    }
}
```

## Database Operations

### Migrations
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('myplugin_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('myplugin_orders');
    }
};
```

### Models
```php
<?php

namespace Vendor\MyPlugin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    
    protected $table = 'myplugin_orders';
    
    protected $fillable = ['user_id', 'amount', 'status', 'metadata'];
    
    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];
}
```

## Configuration Management

### Plugin Configuration
```php
// config/config.php
return [
    'enabled' => env('MYPLUGIN_ENABLED', true),
    'api_key' => env('MYPLUGIN_API_KEY'),
    'settings' => [
        'option1' => 'default',
        'option2' => 'default',
    ],
];
```

### Settings Page
```php
// resources/views/settings.blade.php
@extends('myplugin::layouts.admin')

@section('content')
<div class="container">
    <h1>{{ __('My Plugin Settings') }}</h1>
    
    <form method="POST" action="{{ route('myplugin.settings.update') }}">
        @csrf
        @method('PUT')
        <input type="text" name="api_key" value="{{ $settings['api_key'] ?? '' }}">
        <button type="submit">Save Settings</button>
    </form>
</div>
@endsection
```

## Asset Management

### Registering Assets
```php
// In service provider
public function boot()
{
    HookSystem::addAction('admin_enqueue_scripts', function() {
        wp_enqueue_style('myplugin-admin', plugin_asset_url('css/admin.css'));
    });
}
```

### Asset Helper
```php
function plugin_asset_url($path)
{
    $base = plugins_url('my-plugin');
    return rtrim($base, '/') . '/resources/assets/' . ltrim($path, '/');
}
```

## Internationalization

### Translation Files
```text
resources/lang/
├── en/
│   ├── messages.php
├── es/
│   ├── messages.php
└── fr/
    ├── messages.php
```

### Using Translations
```php
// In PHP
echo __('myplugin::messages.welcome', ['name' => $user->name]);

// In Blade
{{ __('myplugin::messages.welcome', ['name' => $user->name]) }}
```

## Security Best Practices

1. **Input Validation**
```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email',
]);
```

2. **Capability Checks**
```php
if (!current_user_can('manage_options')) {
    abort(403);
}
```

3. **Nonce Verification**
```php
if (!verify_nonce($request->nonce, 'myplugin_action')) {
    abort(403);
}
```

## Testing

### Unit Tests
```php
public function test_order_creation()
{
    $order = $this->service->create([
        'user_id' => 1,
        'amount' => 100.00,
    ]);
    
    $this->assertInstanceOf(Order::class, $order);
}
```

### Integration Tests
```php
public function test_plugin_activates_successfully()
{
    $plugin = new MyPlugin();
    $result = $plugin->activate();
    $this->assertTrue($result);
}
```

## Publishing a Plugin

### 1. Register with Plugin Repository
```json
{
    "name": "vendor/my-plugin",
    "type": "laravel-plugin",
    "extra": {
        "laravel": {
            "providers": ["Vendor\\MyPlugin\\Providers\\PluginServiceProvider"]
        }
    }
}
```

## Best Practices Summary
- **Organization**: Follow PSR standards and separate concerns.
- **Performance**: Use caching and minimize database queries.
- **Security**: Validate all inputs and escape all outputs.
- **Maintenance**: Document code and follow semantic versioning.
