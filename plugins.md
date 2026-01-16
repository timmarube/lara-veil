
# Plugin System Documentation

## Introduction

Plugins extend Laravel applications with additional functionality without modifying core code. They can add features, modify behavior, or integrate with external services.

## Plugin Structure

### Basic Structure

```text
packages/
└── vendor/
    └── my-plugin/
        ├── plugin.json         # Plugin manifest
        ├── src/
        │   └── PluginServiceProvider.php
        ├── routes/
        │   └── web.php
        ├── database/
        ├── resources/
        │   └── views/
        └── config/
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

### 2. Plugin Service Provider
```php
<?php

namespace Vendor\MyPlugin;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'myplugin');
        
        // Register hooks with global helpers
        add_action('admin_menu', function() {
            // Logic
        });
    }
}
```

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
add_action('admin_menu', function($menu) {
    // Logic to add menu
});

// Add dashboard widgets
add_action('dashboard_widgets', function($widgets) {
    // Logic to add widgets
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
#### Content Hooks
```php
// Filter post content
add_filter('the_content', function($content) {
    return $content;
});

// Modify post title
add_filter('the_title', function($title) {
    return strtoupper($title);
});
```

#### User Hooks
```php
// User registration
add_action('user_registered', function($userId) {
    // Logic
});
```

#### System Hooks
```php
// Plugin activation
add_action('plugin_activated', function($plugin) {
    // Logic
});
```

### Creating Custom Hooks
```php
// Define a hook in your plugin
class OrderProcessor
{
    public function process($order)
    {
        // Allow other plugins to modify order
        $order = apply_filters('order_before_process', $order);
        
        // Notify other plugins
        do_action('order_processed', $order);
        
        return $order;
    }
}
```

## Database Operations

### Migrations
```php
Schema::create('myplugin_orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->timestamps();
});
```

## Asset Management

### Registering Assets
```php
// In service provider
public function boot()
{
    add_action('wp_enqueue_scripts', function() {
        // Enqueue assets
    });
}
```

## Security Best Practices

1. **Input Validation**
```php
$validated = $request->validate([
    'name' => 'required|string|max:255',
]);
```

2. **Capability Checks**
```php
if (!current_user_can('manage_options')) {
    abort(403);
}
```

## Best Practices Summary
- **Organization**: Follow PSR standards and separate concerns.
- **Performance**: Use caching and minimize database queries.
- **Security**: Validate all inputs and escape all outputs.
- **Maintenance**: Document code and follow semantic versioning.
