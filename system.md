# Lara-Veil: The Two Shall Become One

## Overview
A WordPress-like extensibility system for Laravel applications that allows dynamic loading of plugins and themes without modifying core code.

## System Architecture

### core components

```text
app/Core/
├── PluginManager.php # Manages plugin lifecycle
├── ThemeManager.php  # Manages theme lifecycle
├── HookSystem.php    # Action/Filter system
├── EventSystem.php   # Extended Laravel events
└── ServiceManager.php # Service provider management
```


### directory structure

```text
laravel-app/
├── app/
│   ├── Core/           # System core files (HookSystem, Managers)
│   └── Providers/
│       └── ExtensibilityServiceProvider.php
├── packages/           # User plugins directory (vendor-nested)
├── themes/             # User themes directory
├── bootstrap/
│   └── providers.php   # Service provider list
└── config/
```


## Installation & Setup

### 1. Register Service Provider
Add the provider to `bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ExtensibilityServiceProvider::class,
];
```

### 2. Configure Plugin/Theme Directories
Ensure `packages/` and `themes/` directories exist in your root.

## System Flow

### Application Bootstrap Sequence

```mermaid
graph TD
    A[Laravel Bootstrapping] --> B[Load Core System]
    B --> C[Load Active Plugins]
    C --> D[Register Plugin Service Providers]
    D --> E[Load Active Theme]
    E --> F[Apply Theme Configuration]
    F --> G[Register Theme Service Providers]
    G --> H[Execute 'init' Hooks]
    H --> I[Application Ready]
```

### Request Lifecycle

```mermaid
graph TD
    A[Request Received] --> B['request.received' Action]
    B --> C[Laravel Middleware Stack]
    C --> D[Router Dispatch]
    D --> E[Controller/Action Execution]
    E --> F[View Rendering]
    F --> G['theme.rendering' Filter]
    G --> H['response.sending' Action]
    H --> I[Response Sent]
```

## Hook System

### Available Hooks

#### System Hooks
- `system.init`: After system initialization
- `system.booted`: After application boot
- `system.shutdown`: Before application shutdown

#### Request Hooks
- `request.received`: When request is received
- `request.routed`: After route is matched
- `request.validated`: After request validation

#### Database Hooks
- `db.query.executing`: Before query execution
- `db.query.executed`: After query execution
- `db.model.saving`: Before model save
- `db.model.saved`: After model save

#### View Hooks
- `view.composing`: Before view is composed
- `view.rendering`: Before view is rendered
- `view.rendered`: After view is rendered

#### Hook Usage Examples
```php
// Adding an action using global helper
add_action('admin_menu', function($menu) {
    // Logic
});

// Applying filters using global helper
$content = apply_filters('the_content', $post->content);
```

Service Management
Plugin Service Registration
php

// In plugin service provider
public function register()
{
    $this->app->singleton('myplugin.service', function($app) {
        return new MyPluginService();
    });
}

Theme Service Registration
php

// In theme service provider
public function register()
{
    $this->app->bind('theme.view.finder', function($app) {
        return new ThemeViewFinder($app['files'], $app['config']['view.paths']);
    });
}

Configuration Management
System Configuration
php

// config/plugins.php
return [
    'paths' => [
        'plugins' => base_path('packages'),
        'themes' => base_path('themes'),
    ],
    
    'cache' => [
        'enabled' => env('PLUGIN_CACHE', true),
        'key' => 'plugin_system',
        'ttl' => 3600,
    ],
    
    'auto_discover' => true,
];

Environment Variables
env

PLUGIN_AUTO_LOAD=true
PLUGIN_CACHE=true
THEME_DEFAULT=my-theme
THEME_FALLBACK=default
PLUGIN_UPDATE_URL=https://plugins.example.com

Security Model
### Permission Levels
1. **Core**: Full system access
2. **Plugin**: Plugin-specific resources
3. **Theme**: View/assets only
4. **Guest**: Read-only access

Security Policies
php

class PluginSecurityPolicy
{
    public function before(User $user, $ability)
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
    }
    
    public function install(User $user): bool
    {
        return $user->can('install plugins');
    }
    
    public function activate(User $user, Plugin $plugin): bool
    {
        return $user->can('activate plugins') && 
               !$plugin->requiresPremium() ||
               $user->subscribed();
    }
}

Update System
### Update Flow

```mermaid
graph TD
    A[Check for Updates] --> B[Download Updates]
    B --> C[Validate Signatures]
    C --> D[Backup Current Version]
    D --> E[Apply Updates]
    E --> F[Run Migration Scripts]
    F --> G[Clear Cache]
    G --> H[Verify Installation]
    H --> I{Success?}
    I -->|Yes| J[Done]
    I -->|No| K[Restore Backup]
```

Update Configuration
php

// config/updates.php
return [
    'channel' => env('UPDATE_CHANNEL', 'stable'),
    'check_interval' => 86400, // 24 hours
    'auto_update' => false,
    'backup_before_update' => true,
];

Error Handling
### Exception Hierarchy

```text
ExtensibilityException
├── PluginException
│   ├── PluginNotFoundException
│   ├── PluginActivationException
│   └── PluginDependencyException
├── ThemeException
│   ├── ThemeNotFoundException
│   └── ThemeActivationException
└── HookException
    ├── HookNotFoundException
    └── HookCallbackException
```

Error Recovery
php

try {
    $pluginManager->activate($plugin);
} catch (PluginDependencyException $e) {
    // Show dependency error
    return redirect()->back()->withErrors([
        'dependencies' => $e->getMissingDependencies()
    ]);
} catch (PluginException $e) {
    // Log and show generic error
    Log::error('Plugin activation failed', [
        'plugin' => $plugin->name,
        'error' => $e->getMessage()
    ]);
}

Performance Optimization
Caching Strategy
php

class PluginCache
{
    protected function getCacheKey(): string
    {
        return 'plugins.active.' . md5(implode('', $this->getActivePluginNames()));
    }
    
    protected function cacheActivePlugins(): void
    {
        Cache::remember($this->getCacheKey(), 3600, function() {
            return $this->loadActivePlugins();
        });
    }
}

Lazy Loading
php

class LazyPluginLoader
{
    protected $loaded = [];
    
    public function loadWhen($condition, $plugin)
    {
        if ($condition() && !isset($this->loaded[$plugin])) {
            $this->loadPlugin($plugin);
            $this->loaded[$plugin] = true;
        }
    }
}

### Database Schema

```sql
-- Plugins table
CREATE TABLE `plugins` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(191) UNIQUE NOT NULL,
    `namespace` VARCHAR(191) UNIQUE NOT NULL,
    `version` VARCHAR(20) NOT NULL,
    `status` ENUM('active', 'inactive', 'broken') DEFAULT 'inactive',
    `settings` JSON,
    `created_at` TIMESTAMP,
    `updated_at` TIMESTAMP
);

-- Themes table
CREATE TABLE `themes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(191) UNIQUE NOT NULL,
    `slug` VARCHAR(191) UNIQUE NOT NULL,
    `parent_id` INT NULL,
    `is_active` BOOLEAN DEFAULT FALSE,
    `settings` JSON,
    `created_at` TIMESTAMP,
    `updated_at` TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `themes`(`id`)
);
```

### API Endpoints

#### System API
```text
GET    /api/system/health          # System health check
GET    /api/system/info            # System information
GET    /api/system/hooks           # List registered hooks
POST   /api/system/cache/clear     # Clear system cache
```

#### Plugin API
```text
GET    /api/plugins                # List plugins
POST   /api/plugins                # Install plugin
PUT    /api/plugins/{id}           # Update plugin
DELETE /api/plugins/{id}           # Uninstall plugin
POST   /api/plugins/{id}/activate  # Activate plugin
POST   /api/plugins/{id}/deactivate # Deactivate plugin
```

#### Theme API
```text
GET    /api/themes                 # List themes
POST   /api/themes/{id}/activate   # Activate theme
GET    /api/themes/{id}/preview    # Preview theme
```

### Command Line Interface

#### System Commands
```bash
# System information
php artisan system:info
php artisan system:hooks

# Cache management
php artisan plugin:cache
php artisan plugin:clear-cache

# Diagnostics
php artisan plugin:diagnose
php artisan theme:diagnose
```

#### Update Commands
```bash
# Check for updates
php artisan plugin:check-updates
php artisan theme:check-updates

# Apply updates
php artisan plugin:update-all
php artisan theme:update {theme}

# Rollback updates
php artisan plugin:rollback {plugin}
```

Monitoring & Logging
Log Channels
php

// config/logging.php
'channels' => [
    'plugins' => [
        'driver' => 'daily',
        'path' => storage_path('logs/plugins.log'),
        'level' => 'debug',
    ],
    'themes' => [
        'driver' => 'daily',
        'path' => storage_path('logs/themes.log'),
        'level' => 'debug',
    ],
],

Monitoring Metrics
php

class SystemMonitor
{
    protected function collectMetrics(): array
    {
        return [
            'plugins' => [
                'total' => Plugin::count(),
                'active' => Plugin::where('status', 'active')->count(),
                'loaded' => count(app('plugin.manager')->getLoaded()),
            ],
            'hooks' => [
                'registered' => count(HookSystem::getRegistered()),
                'executed' => HookSystem::getExecutionCount(),
            ],
            'performance' => [
                'load_time' => microtime(true) - LARAVEL_START,
                'memory_usage' => memory_get_peak_usage(true),
            ],
        ];
    }
}

### Best Practices

#### Development Guidelines
- Always use namespaces to avoid conflicts
- Implement proper error handling
- Use the hook system instead of modifying core files
- Follow semantic versioning for plugins/themes
- Write comprehensive documentation

#### Security Guidelines
- Validate all user inputs
- Use prepared statements for database queries
- Implement proper authorization checks
- Sanitize output data
- Keep dependencies updated

#### Performance Guidelines
- Implement caching where appropriate
- Use lazy loading for non-essential features
- Optimize database queries
- Minimize hook usage in loops
- Use efficient algorithms

### Troubleshooting

#### Common Issues
**Plugin Not Loading**
- Check plugin status in database
- Verify `plugin.json` format
- Check for namespace conflicts
- Review error logs

**Hook Not Firing**
- Verify hook is registered
- Check hook priority
- Ensure plugin is active
- Check for early returns

**Theme Not Applying**
- Verify theme is active
- Check template hierarchy
- Clear view cache
- Check file permissions

#### Debug Commands
```bash
# Debug plugin loading
php artisan plugin:debug {plugin}

# Debug hooks
php artisan hook:debug {hook}

# Debug theme
php artisan theme:debug {theme}
```

Support & Maintenance
Version Support
Version	Laravel	PHP	Support Until
1.0.x	9.x	8.1	2024-12-31
1.1.x	10.x	8.2	2025-06-30
2.0.x	10.x	8.3	2025-12-31
Update Policy

    Security patches: Within 48 hours

    Bug fixes: Within 7 days

    Feature updates: Monthly releases

    Major versions: Every 6 months

Migration Guide
From v1.0 to v2.0

    Backup your database

    Update composer dependencies

    Run migration scripts

    Update plugin manifests

    Clear all caches

    Test functionality

Breaking Changes

    Hook system API changes

    Database schema updates

    Configuration file structure

    Service provider registration 
 # #   M e d i a   S y s t e m  
  
 # # #   O v e r v i e w  
 T h e   M e d i a   S y s t e m   p r o v i d e s   a   r o b u s t   w a y   t o   h a n d l e   f i l e   u p l o a d s ,   i m a g e   p r o c e s s i n g ,   a n d   a s s o c i a t i n g   m e d i a   w i t h   m o d e l s   u s i n g   a   W o r d P r e s s - l i k e   a p p r o a c h .  
  
 # # #   D a t a b a s e   S c h e m a  
 T h e   ` m e d i a `   t a b l e   s t o r e s   m e t a d a t a   f o r   a l l   u p l o a d e d   f i l e s .  
  
 ` ` ` s q l  
 C R E A T E   T A B L E   ` m e d i a `   (  
         ` i d `   b i g i n t ( 2 0 )   u n s i g n e d   N O T   N U L L   A U T O _ I N C R E M E N T ,  
         ` n a m e `   v a r c h a r ( 2 5 5 )   N O T   N U L L ,   - -   F i l e n a m e   w i t h o u t   e x t e n s i o n  
         ` f i l e _ n a m e `   v a r c h a r ( 2 5 5 )   N O T   N U L L ,   - -   F u l l   f i l e n a m e   w i t h   e x t e n s i o n  
         ` m i m e _ t y p e `   v a r c h a r ( 2 5 5 )   D E F A U L T   N U L L ,  
         ` d i s k `   v a r c h a r ( 2 5 5 )   N O T   N U L L   D E F A U L T   ' p u b l i c ' ,  
         ` p a t h `   v a r c h a r ( 2 5 5 )   N O T   N U L L ,  
         ` s i z e `   b i g i n t ( 2 0 )   u n s i g n e d   N O T   N U L L ,  
         ` c o l l e c t i o n _ n a m e `   v a r c h a r ( 2 5 5 )   N O T   N U L L   D E F A U L T   ' d e f a u l t ' ,  
         ` m o d e l _ t y p e `   v a r c h a r ( 2 5 5 )   D E F A U L T   N U L L ,  
         ` m o d e l _ i d `   b i g i n t ( 2 0 )   u n s i g n e d   D E F A U L T   N U L L ,  
         ` m a n i p u l a t i o n s `   j s o n   D E F A U L T   N U L L ,  
         ` c u s t o m _ p r o p e r t i e s `   j s o n   D E F A U L T   N U L L ,  
         ` g e n e r a t e d _ c o n v e r s i o n s `   j s o n   D E F A U L T   N U L L ,  
         ` r e s p o n s i v e _ i m a g e s `   j s o n   D E F A U L T   N U L L ,  
         ` o r d e r _ c o l u m n `   i n t ( 1 0 )   u n s i g n e d   D E F A U L T   N U L L ,  
         ` c r e a t e d _ a t `   t i m e s t a m p   N U L L   D E F A U L T   N U L L ,  
         ` u p d a t e d _ a t `   t i m e s t a m p   N U L L   D E F A U L T   N U L L ,  
         P R I M A R Y   K E Y   ( ` i d ` )  
 ) ;  
 ` ` `  
  
 # # #   U s a g e  
  
 # # # #   1 .   S e t u p   M o d e l  
 A d d   t h e   ` H a s M e d i a `   t r a i t   t o   y o u r   m o d e l :  
  
 ` ` ` p h p  
 u s e   A p p \ T r a i t s \ H a s M e d i a ;  
  
 c l a s s   P o s t   e x t e n d s   M o d e l  
 {  
         u s e   H a s M e d i a ;  
 }  
 ` ` `  
  
 # # # #   2 .   U p l o a d i n g   M e d i a  
 U s e   t h e   ` a d d M e d i a `   m e t h o d   t o   u p l o a d   a n d   a s s o c i a t e   f i l e s .  
  
 ` ` ` p h p  
 / /   F r o m   a   r e q u e s t   u p l o a d  
 $ p o s t - > a d d M e d i a ( $ r e q u e s t - > f i l e ( ' i m a g e ' ) )  
           - > t o ( ' u p l o a d s / p o s t s ' )  
           - > i n C o l l e c t i o n ( ' f e a t u r e d _ i m a g e ' )  
           - > s a v e ( ) ;  
  
 / /   F r o m   a   U R L  
 $ p o s t - > a d d M e d i a ( ' h t t p s : / / e x a m p l e . c o m / i m a g e . j p g ' )  
           - > t o ( ' u p l o a d s / p o s t s ' )  
           - > s a v e ( ) ;  
 ` ` `  
  
 # # # #   3 .   R e t r i e v i n g   M e d i a  
  
 ` ` ` p h p  
 / /   G e t   a l l   m e d i a   i n   ' d e f a u l t '   c o l l e c t i o n  
 $ m e d i a I t e m s   =   $ p o s t - > g e t M e d i a ( ) ;  
  
 / /   G e t   s p e c i f i c   c o l l e c t i o n  
 $ f e a t u r e d I m a g e s   =   $ p o s t - > g e t M e d i a ( ' f e a t u r e d _ i m a g e ' ) ;  
  
 / /   G e t   f i r s t   m e d i a   U R L  
 $ u r l   =   $ p o s t - > g e t F i r s t M e d i a U r l ( ' f e a t u r e d _ i m a g e ' ) ;  
 ` ` `  
  
 # # # #   4 .   M e d i a F o r g e S e r v i c e   ( A d v a n c e d )  
 D i r e c t l y   u s e   t h e   s e r v i c e   f o r   a d v a n c e d   m a n i p u l a t i o n s   b e f o r e   s a v i n g .  
  
 ` ` ` p h p  
 u s e   A p p \ S e r v i c e s \ V r m \ M e d i a F o r g e S e r v i c e ;  
  
 $ s e r v i c e   =   n e w   M e d i a F o r g e S e r v i c e ( ) ;  
 $ m e d i a   =   $ s e r v i c e - > u p l o a d ( $ f i l e )  
         - > r e s i z e ( 8 0 0 ,   6 0 0 )  
         - > w a t e r m a r k ( ' p a t h / t o / w a t e r m a r k . p n g ' )  
         - > t o ( ' u p l o a d s ' )  
         - > f o r M o d e l ( $ u s e r )  
         - > i n C o l l e c t i o n ( ' a v a t a r ' )  
         - > s a v e ( ) ;  
 ` ` `  
 