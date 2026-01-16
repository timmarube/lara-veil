
# Theme System Documentation

## Introduction

Themes control the visual presentation of your Laravel application. They separate design from functionality, allowing you to change the look and feel without modifying application logic.

## Theme Structure

### Basic Theme Structure

```text
my-theme/
├── theme.json          # Theme manifest
├── screenshot.png      # Theme screenshot (1200x900)
├── style.css           # Main stylesheet
├── functions.php       # Theme functions
├── index.blade.php     # Main template
├── assets/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── fonts/
├── views/
│   ├── layouts/
│   ├── partials/
│   ├── components/
│   └── pages/
├── config/
│   └── theme.php
├── languages/
│   └── en.json
└── templates/          # Page templates
```


### Theme Manifest (theme.json)
```json
{
    "name": "My Theme",
    "slug": "my-theme",
    "version": "1.0.0",
    "description": "A responsive theme for Laravel applications",
    "author": "Theme Author",
    "author_url": "https://author.com",
    "license": "GPL-2.0-or-later",
    "text_domain": "my-theme",
    "requires": {
        "laravel": "^9.0|^10.0",
        "php": "^8.1",
        "plugins": {
            "my-plugin": "^1.0"
        }
    },
    "supports": {
        "menus": true,
        "widgets": true,
        "customizer": true,
        "post_thumbnails": true,
        "custom_logo": true,
        "custom_background": true,
        "html5": ["caption", "comment-form", "search-form"]
    },
    "templates": {
        "home": "Home",
        "page": "Page",
        "single": "Single Post",
        "archive": "Archive",
        "search": "Search Results",
        "404": "404 Error"
    },
    "styles": {
        "primary": "#0073aa",
        "secondary": "#23282d",
        "text": "#333333",
        "background": "#ffffff"
    },
    "screenshot": "screenshot.png"
}

## Theme Development

### 1. Creating a Theme
```bash
php artisan make:theme MyTheme
```

This creates:
```text
themes/my-theme/
├── theme.json
├── style.css
├── functions.php
├── screenshot.png
├── views/
│   └── layouts/
│       └── app.blade.php
└── assets/
```

### 2. Theme Functions File
```php
<?php
// functions.php

/**
 * Theme setup function.
 */
add_action('theme_setup', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    
    register_nav_menus([
        'primary' => __('Primary Menu', 'my-theme'),
        'footer' => __('Footer Menu', 'my-theme'),
    ]);
});

/**
 * Enqueue theme assets.
 */
add_action('theme_enqueue_scripts', function() {
    wp_enqueue_style('my-theme-style', theme_asset_url('css/style.css'));
    wp_enqueue_script('my-theme-script', theme_asset_url('js/script.js'), ['jquery'], null, true);
});
```

## Template Hierarchy

### Default Hierarchy
```text
index.blade.php                  # Fallback
├── front-page.blade.php         # Front page
├── home.blade.php               # Blog page
├── single.blade.php             # Single post
├── page.blade.php               # Single page
├── archive.blade.php            # Archive
├── search.blade.php             # Search results
└── 404.blade.php                # 404 page
```

### Custom Templates
```php
// Register custom template in theme.json
{
    "templates": {
        "portfolio": "Portfolio Template",
        "full-width": "Full Width Template"
    }
}
```

## Blade Templating

### Layout Template
```blade
{{-- views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', config('app.name'))</title>
    @themeStyles()
</head>
<body class="@yield('body-class')">
    @include('theme::partials.header')
    <main>@yield('content')</main>
    @include('theme::partials.footer')
    @themeScripts()
</body>
</html>
```

### Page Template
```blade
{{-- views/pages/home.blade.php --}}
@extends('theme::layouts.app')

@section('title', 'Home Page')
@section('body-class', 'home-page')

@section('content')
    @include('theme::partials.hero')
    <section class="featured">
        <h2>@lang('theme::messages.featured')</h2>
        @foreach($featuredPosts as $post)
            @include('theme::components.post-card', ['post' => $post])
        @endforeach
    </section>
@endsection
```

## Theme Components

### Header Component
```blade
{{-- views/partials/header.blade.php --}}
<header class="site-header">
    <div class="branding">
        <a href="{{ url('/') }}">{{ config('app.name') }}</a>
    </div>
    <nav>
        @if(has_nav_menu('primary'))
            {!! wp_nav_menu(['theme_location' => 'primary']) !!}
        @endif
    </nav>
</header>
```

### Footer Component
```blade
{{-- views/partials/footer.blade.php --}}
<footer class="site-footer">
    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
</footer>
```

## Theme Configuration

### Theme Options
```php
// config/theme.php
return [
    'colors' => [
        'primary' => '#0073aa',
        'secondary' => '#23282d',
    ],
    'typography' => [
        'font_family' => 'sans-serif',
        'font_size' => '16px',
    ],
];
```

### Customizer Integration
```php
// functions.php
add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('theme_colors', ['title' => __('Colors', 'my-theme')]);
    
    $wp_customize->add_setting('primary_color', ['default' => '#0073aa']);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', [
        'label' => __('Primary Color', 'my-theme'),
        'section' => 'theme_colors',
    ]));
});
```

## Asset Management

### Stylesheet
```css
/* style.css */
/*
Theme Name: My Theme
Version: 1.0.0
Description: A responsive Laravel theme
*/

:root {
    --primary-color: #0073aa;
    --background-color: #ffffff;
}

body {
    font-family: sans-serif;
    background: var(--background-color);
}
```

### JavaScript
```javascript
// assets/js/theme.js
(function($) {
    'use strict';
    $(document).ready(function() {
        console.log('Theme initialized');
    });
})(jQuery);
```

## Theme Functions

### Helper Functions
```php
/**
 * Get theme option.
 */
function theme_option($key, $default = null)
{
    return data_get(get_option('theme_options'), $key, $default);
}

/**
 * Get pagination.
 */
function theme_pagination()
{
    echo paginate_links();
}
```

## Child Themes

### Creating a Child Theme
```text
my-child-theme/
├── style.css
├── functions.php
└── screenshot.png
```

### Child Theme style.css
```css
/*
Theme Name: My Child Theme
Template: my-theme
*/
@import url("../my-theme/style.css");
```

## Theme Widgets

### Creating Custom Widgets
```php
class RecentPostsWidget extends Widget
{
    public function widget($args, $instance)
    {
        echo $args['before_widget'] . '<h3>Recent Posts</h3>' . $args['after_widget'];
    }
}

// Register widget
add_action('widgets_init', function() {
    register_widget(RecentPostsWidget::class);
});
```

Theme Shortcodes
Creating Shortcodes
php

// functions.php
/**
 * Button shortcode.
 */
add_shortcode('button', function($atts, $content = null) {
    $atts = shortcode_atts([
        'url' => '#',
        'style' => 'primary',
        'size' => 'medium',
        'target' => '_self',
    ], $atts, 'button');
    
    $classes = ['btn', 'btn-' . $atts['style'], 'btn-' . $atts['size']];
    
    return sprintf(
        '<a href="%s" class="%s" target="%s">%s</a>',
        esc_url($atts['url']),
        esc_attr(implode(' ', $classes)),
        esc_attr($atts['target']),
        do_shortcode($content)
    );
});

/**
 * Columns shortcode.
 */
add_shortcode('columns', function($atts, $content = null) {
    $atts = shortcode_atts([
        'cols' => '2',
        'gap' => '20px',
    ], $atts, 'columns');
    
    $style = $atts['gap'] ? sprintf('style="gap: %s;"', $atts['gap']) : '';
    
    return sprintf(
        '<div class="columns columns-%s" %s>%s</div>',
        esc_attr($atts['cols']),
        $style,
        do_shortcode($content)
    );
});

add_shortcode('column', function($atts, $content = null) {
    return sprintf(
        '<div class="column">%s</div>',
        do_shortcode($content)
    );
});

Using Shortcodes
php

// In content
[button url="/contact" style="primary"]Contact Us[/button]

[columns cols="3" gap="30px"]
    [column]Column 1 content[/column]
    [column]Column 2 content[/column]
    [column]Column 3 content[/column]
[/columns]

Theme Localization
Translation Files
text

languages/
├── en.json
├── es.json
├── fr.json
└── my-theme.pot  # Template file

Generating POT File
bash

# Extract translatable strings
php artisan theme:make-pot my-theme

Loading Translations
php

// functions.php
add_action('after_setup_theme', function() {
    load_theme_textdomain('my-theme', get_template_directory() . '/languages');
});

Performance Optimization
Asset Optimization
php

// functions.php
add_action('wp_enqueue_scripts', function() {
    // Concatenate and minify in production
    if (wp_get_environment_type() === 'production') {
        wp_enqueue_style(
            'my-theme-styles',
            theme_asset_url('css/styles.min.css'),
            [],
            theme_version()
        );
        
        wp_enqueue_script(
            'my-theme-scripts',
            theme_asset_url('js/scripts.min.js'),
            [],
            theme_version(),
            true
        );
    } else {
        // Load separate files in development
        wp_enqueue_style('my-theme-main', theme_asset_url('css/main.css'));
        wp_enqueue_script('my-theme-app', theme_asset_url('js/app.js'));
    }
}, 100);

Lazy Loading
blade

{{-- In templates --}}
<img src="{{ $image['placeholder'] }}" 
     data-src="{{ $image['url'] }}"
     loading="lazy"
     alt="{{ $image['alt'] }}"
     class="lazy-load">

Caching Strategies
php

// functions.php
/**
 * Cache menu output.
 */
function cached_menu($location, $args = [])
{
    $cache_key = 'menu_' . $location . '_' . get_locale();
    $cache_time = HOUR_IN_SECONDS;
    
    return cache()->remember($cache_key, $cache_time, function() use ($location, $args) {
        return wp_nav_menu(array_merge([
            'theme_location' => $location,
            'echo' => false,
        ], $args));
    });
}

Security Best Practices
1. Escape Output
blade

{{-- Escape all output --}}
<h1>{{ esc_html($title) }}</h1>
<p>{{ esc_attr($description) }}</p>
<a href="{{ esc_url($link) }}">Link</a>

{{-- Use wp_kses for HTML --}}
{!! wp_kses($html, [
    'a' => ['href' => [], 'title' => []],
    'br' => [],
    'em' => [],
    'strong' => [],
]) !!}

2. Validate Input
php

// In theme functions
function theme_process_form($data)
{
    // Validate nonce
    if (!wp_verify_nonce($data['_wpnonce'], 'theme_form')) {
        wp_die(__('Security check failed', 'my-theme'));
    }
    
    // Sanitize input
    $name = sanitize_text_field($data['name']);
    $email = sanitize_email($data['email']);
    
    // Validate input
    if (!is_email($email)) {
        wp_die(__('Invalid email address', 'my-theme'));
    }
    
    return compact('name', 'email');
}

3. File Security
php

// functions.php
/**
 * Restrict file access.
 */
add_action('template_redirect', function() {
    // Prevent direct access to template files
    if (strpos($_SERVER['REQUEST_URI'], '/templates/') !== false) {
        wp_die(__('Direct access not allowed', 'my-theme'));
    }
});

Theme Testing
Browser Testing Checklist

    Chrome (latest)

    Firefox (latest)

    Safari (latest)

    Edge (latest)

    Mobile Safari

    Chrome Mobile

Functionality Testing
php

// tests/ThemeTest.php
class ThemeTest extends TestCase
{
    public function test_theme_setup()
    {
        // Test theme supports
        $this->assertTrue(current_theme_supports('post-thumbnails'));
        $this->assertTrue(current_theme_supports('custom-logo'));
        
        // Test menus
        $this->assertArrayHasKey('primary', get_registered_nav_menus());
        $this->assertArrayHasKey('footer', get_registered_nav_menus());
    }
    
    public function test_theme_functions()
    {
        $this->assertIsString(theme_version());
        $this->assertIsString(theme_asset_url('css/style.css'));
    }
}

Publishing a Theme
1. Prepare for Distribution
bash

# Create theme package
php artisan theme:package my-theme

# This creates:
# my-theme.zip
# my-theme.tar.gz

2. Theme Check
bash

# Validate theme structure
php artisan theme:check my-theme

# Check for common issues
php artisan theme:lint my-theme

3. Update Server
json

{
    "my-theme": {
        "version": "1.0.1",
        "package": "https://example.com/downloads/my-theme-1.0.1.zip",
        "requires": "5.6",
        "tested": "6.0",
        "requires_php": "7.4",
        "last_updated": "2024-01-15",
        "downloads": 1500,
        "sections": {
            "description": "Theme description",
            "installation": "Installation instructions",
            "changelog": "## 1.0.1\n- Fixed responsive issues\n- Improved performance"
        }
    }
}

Theme Marketplace
Theme Information File
php

// marketplace.json
{
    "name": "My Premium Theme",
    "slug": "my-premium-theme",
    "version": "1.0.0",
    "description": "Premium theme with advanced features",
    "author": "Theme Author",
    "author_url": "https://author.com",
    "price": "$59",
    "categories": ["Business", "Blog", "Portfolio"],
    "tags": ["responsive", "woocommerce", "elementor"],
    "features": [
        "Responsive Design",
        "WooCommerce Ready",
        "Page Builder Support",
        "SEO Optimized"
    ],
    "demo_url": "https://demo.example.com",
    "documentation_url": "https://docs.example.com"
}

Troubleshooting
Common Issues
Theme Not Activating

    Check theme.json format

    Verify required Laravel version

    Check for conflicting plugins

    Verify file permissions

Styles Not Loading

    Check asset paths

    Clear browser cache

    Verify enqueue functions

    Check for CSS conflicts

Templates Not Working

    Verify template hierarchy

    Check file names

    Clear template cache

    Check for overrides

Debug Mode
php

// Enable in wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Theme specific debug
define('THEME_DEBUG', true);

Best Practices
Development Practices

    Use semantic HTML5

    Follow accessibility guidelines (WCAG 2.1)

    Implement responsive design

    Optimize images and assets

Code Organization

    Separate PHP and HTML

    Use template parts

    Implement caching

    Document custom functions

### Best Practices

#### Performance
- Minimize HTTP requests
- Optimize images
- Use efficient CSS/JS
- Implement lazy loading

#### Security
- Escape all output
- Validate all input
- Use nonces for forms
- Keep dependencies updated