@extends('theme::layouts.app')

@section('title', 'Developer Documentation - Lara-Veil')

@section('content')
<div class="docs-layout">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
        <div class="sidebar-inner">
            <div class="logo">
                <span class="logo-icon">📦</span>
                <span class="logo-text">Lara-Veil</span>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <h4>Getting Started</h4>
                    <a href="#welcome">Introduction</a>
                    <a href="#core-concepts">Core Concepts</a>
                    <a href="#directory-structure">Folders</a>
                </div>
                <div class="nav-section">
                    <h4>Plugin Dev</h4>
                    <a href="#plugin-basics">Plugin Basics</a>
                    <a href="#plugin-manifest">The Manifest</a>
                    <a href="#plugin-namespace">Namespacing</a>
                    <a href="#plugin-routes">Adding Routes</a>
                </div>
                <div class="nav-section">
                    <h4>Theme Dev</h4>
                    <a href="#theme-basics">Theme Basics</a>
                    <a href="#theme-templates">Templates</a>
                    <a href="#theme-assets">Static Assets</a>
                </div>
                <div class="nav-section">
                    <h4>Extensibility API</h4>
                    <a href="#hooks-actions">Actions</a>
                    <a href="#hooks-filters">Filters</a>
                </div>
            </nav>
        </div>
    </aside>

    <!-- Content Area -->
    <main class="content">
        <div class="content-inner">
            <section id="welcome">
                <h1 class="page-title">Documentation</h1>
                <p class="subtitle">Complete guide to building and extending the Lara-Veil ecosystem. "The Two Shall Become One"</p>
                
                <div class="status-banner">
                    <span class="status-indicator"></span>
                    <p>Core System Active: <strong>v1.0.0</strong></p>
                    <a href="/hello-plugin" class="status-link">Run Integration Test →</a>
                </div>
            </section>

            <section id="core-concepts">
                <h2>Introduction</h2>
                <p>Lara-Veil is a WordPress-inspired extensibility architecture for Laravel. It allows developers to create self-contained modules called <strong>Plugins</strong> and visual skins called <strong>Themes</strong>.</p>
                
                <div class="feature-grid">
                    <div class="feature">
                        <h5>No Core Edits</h5>
                        <p>Extend logic without touching <code>app/</code>.</p>
                    </div>
                    <div class="feature">
                        <h5>Blade-First</h5>
                        <p>Flexible template overriding hierarchy.</p>
                    </div>
                    <div class="feature">
                        <h5>Hook Events</h5>
                        <p>Global Action and Filter API.</p>
                    </div>
                </div>
            </section>

            <hr>

            <section id="plugin-basics">
                <h2>🔌 Plugin Development</h2>
                <p>Plugins are stored in <code>packages/[vendor]/[name]</code>. They can register their own service providers, routes, migrations, and assets.</p>

                <div id="plugin-manifest">
                    <h3>The Manifest (plugin.json)</h3>
                    <p>The <code>plugin.json</code> defines the core identity of your module.</p>
                    <div class="code-editor">
                        <div class="code-header"><span>plugin.json</span></div>
                        <pre><code>{
    "name": "my-plugin",
    "title": "Analytics Engine",
    "namespace": "Vendor\\Analytics\\",
    "autoload": {
        "psr-4": { "Vendor\\Analytics\\": "src/" }
    },
    "providers": [ "Vendor\\Analytics\\PluginServiceProvider" ]
}</code></pre>
                    </div>
                </div>

                <div id="plugin-namespace">
                    <h3>Proper Namespacing</h3>
                    <p>Always use unique namespaces for your plugins. The system core uses the <code>namespace</code> field in <code>plugin.json</code> to register PSR-4 autoloading dynamically.</p>
                </div>
            </section>

            <hr>

            <section id="theme-basics">
                <h2>🎨 Theme Development</h2>
                <p>Themes control the visual presentation layer. They reside in the <code>themes/</code> directory and are automatically discovered by the <code>ThemeManager</code>.</p>
                
                <div id="theme-templates">
                    <h3>Template Hierarchy</h3>
                    <p>Themes use a waterfall logic for finding views. Views in your theme override core views of the same name.</p>
                    <div class="card-list">
                        <div class="card-item">
                            <strong>views/layouts</strong>
                            <span>Base application structures.</span>
                        </div>
                        <div class="card-item">
                            <strong>views/partials</strong>
                            <span>Reusable UI fragments (header, footer).</span>
                        </div>
                        <div class="card-item">
                            <strong>views/index.blade.php</strong>
                            <span>The primary entry point.</span>
                        </div>
                    </div>
                </div>
            </section>

            <hr>

            <section id="hooks-api">
                <h2>🪝 Extensibility API (Hooks)</h2>
                <p>Hooks are the primary way components communicate. They allow you to "hook" into the execution of other components.</p>
                
                <div class="api-split">
                    <div class="api-item">
                        <h4>Actions</h4>
                        <p>Execute logic at a point in time.</p>
                        <div class="code-editor small">
                            <pre><code>add_action('theme_loaded', function() {
    // Logic here
});</code></pre>
                        </div>
                    </div>
                    <div class="api-item">
                        <h4>Filters</h4>
                        <p>Transform data before use.</p>
                        <div class="code-editor small">
                            <pre><code>add_filter('site_title', function($t) {
    return $t . ' | Dev';
});</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="footer">
                <p>© 2026 Lara-Veil Framework. "The Two Shall Become One"</p>
            </footer>
        </div>
    </main>
</div>

<style>
    /* Docs Specific Styling */
    .docs-layout {
        display: flex;
        min-height: 100vh;
        background: #0b0f1a;
        color: #f8fafc;
    }

    /* Sidebar */
    .sidebar {
        width: 280px;
        background: #0f172a;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }

    .sidebar-inner {
        padding: 2.5rem 1.5rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2.5rem;
        padding-left: 0.5rem;
    }

    .logo-icon { font-size: 1.5rem; }
    .logo-text { font-weight: 700; font-size: 1.25rem; letter-spacing: -0.025em; }

    .nav-section { margin-bottom: 2rem; }
    .nav-section h4 {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6366f1;
        margin-bottom: 1rem;
        padding-left: 0.5rem;
    }

    .nav-section a {
        display: block;
        padding: 0.5rem;
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.9rem;
        border-radius: 0.375rem;
        transition: all 0.2s;
    }

    .nav-section a:hover {
        background: rgba(99, 102, 241, 0.1);
        color: #f8fafc;
    }

    /* Content Area */
    .content {
        margin-left: 280px;
        flex-grow: 1;
        padding: 4rem 2rem;
        display: flex;
        justify-content: center;
    }

    .content-inner {
        max-width: 800px;
        width: 100%;
        text-align: left;
    }

    .page-title {
        font-size: 3.5rem;
        font-weight: 800;
        letter-spacing: -0.05em;
        margin-bottom: 1rem;
        background: linear-gradient(to right, #fff, #6366f1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .subtitle {
        font-size: 1.25rem;
        color: #94a3b8;
        margin-bottom: 3rem;
    }

    .status-banner {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.9rem;
    }

    .status-indicator {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 10px #10b981;
    }

    .status-link {
        margin-left: auto;
        color: #10b981;
        text-decoration: none;
        font-weight: 600;
    }

    h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-top: 4rem;
        margin-bottom: 1.5rem;
        scroll-margin-top: 2rem;
    }

    section p {
        color: #94a3b8;
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    .feature-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .feature {
        background: rgba(255, 255, 255, 0.03);
        padding: 1.5rem;
        border-radius: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .feature h5 { margin-top: 0; color: #fff; margin-bottom: 0.5rem; }
    .feature p { font-size: 0.85rem; margin-bottom: 0; }

    /* Code Editor UI */
    .code-editor {
        background: #000;
        border-radius: 0.75rem;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin: 2rem 0;
    }

    .code-header {
        background: #1a1a1a;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        color: #666;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .code-editor pre { margin: 0; padding: 1.5rem; color: #10b981; font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; overflow-x: auto; }

    .card-list { display: flex; flex-direction: column; gap: 1rem; }
    .card-item {
        background: #1e293b;
        padding: 1.25rem;
        border-radius: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-item strong { color: #6366f1; }
    .card-item span { font-size: 0.85rem; color: #94a3b8; }

    .api-split { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .api-item h4 { margin-top: 0; margin-bottom: 1rem; }
    .code-editor.small pre { padding: 1rem; font-size: 0.75rem; }

    hr { margin: 4rem 0; border: 0; border-top: 1px solid rgba(255, 255, 255, 0.05); }

    .footer { margin-top: 6rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.05); text-align: center; color: #475569; font-size: 0.8rem; }

    @media (max-width: 1024px) {
        .sidebar { width: 60px; }
        .sidebar .logo-text, .sidebar h4, .sidebar a { display: none; }
        .content { margin-left: 60px; }
    }
</style>
@endsection
