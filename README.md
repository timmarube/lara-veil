# Larapack: WordPress-like Extensibility for Laravel

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Larapack is a powerful framework extension that brings the flexibility of a **Plugin and Theme system** to Laravel. Inspired by WordPress, it allows you to build modular applications where third-party developers can extend functionality and swap designs without touching the core codebase.

---

## 🚀 Key Features

- **🔌 Plugin System**: Self-contained packages with their own routes, controllers, and service providers.
- **🎨 Theme Engine**: Robust template hierarchy with Blade support and view overriding.
- **🪝 Hook API**: Familiar `add_action` and `add_filter` global helpers for decoupled communication.
- **🛠️ Developer-First**: Zero-config discovery for plugins and themes.
- **💎 Premium UX**: Built-in documentation dashboard with a sleek dark-mode design.

---

## 📦 Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/larapack.git
   cd larapack
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Initialize Database:**
   ```bash
   touch database/database.sqlite
   php artisan migrate --seed
   ```

5. **Start Development Server:**
   ```bash
   php artisan serve
   ```

---

## 📖 Essential Documentation

The project includes deep-dive documentation for every layer of the system:

| Document | Purpose |
| :--- | :--- |
| [**Architecture Overview**](larapack_core.md) | High-level system design and integration map. |
| [**Core System**](system.md) | Deep-dive into the Hook system, Managers, and Lifecycle. |
| [**Plugin Development**](plugins.md) | Guide to building modular functionality in `/packages`. |
| [**Theme Development**](themes.md) | Guide to building visual skins in `/themes`. |

---

## 🔌 Quick Start: Plugins

To create a new plugin, simply create a folder in `packages/[vendor]/[name]`:

```text
packages/vendor/my-plugin/
├── plugin.json         # The unique manifest
└── src/
    └── PluginServiceProvider.php
```

**plugin.json:**
```json
{
    "name": "my-plugin",
    "namespace": "Vendor\\MyPlugin\\",
    "providers": [
        "Vendor\\MyPlugin\\PluginServiceProvider"
    ]
}
```

---

## 🎨 Quick Start: Themes

Themes reside in the `themes/` directory. Create a new folder with a `theme.json`:

```text
themes/my-theme/
├── theme.json
└── views/
    └── index.blade.php
```

Larapack will automatically prioritize views in your theme over the core application views.

---

## 🪝 Global Helpers

Larapack provides WordPress-familiar functions out of the box:

```php
// Register a logic hook
add_action('theme_loaded', function() {
    // Custom logic
});

// Modify data
$title = apply_filters('site_title', 'Larapack');
```

---

## 📜 License

The Larapack framework is open-sourced software licensed under the [MIT license](LICENSE).
