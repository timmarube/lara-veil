<!DOCTYPE html>
<html>
<head>
    <title>User Manager Plugin Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .test { background: white; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #4CAF50; }
        .test.fail { border-left-color: #f44336; }
        .code { background: #f0f0f0; padding: 10px; border-radius: 4px; margin: 10px 0; font-family: monospace; }
        h1 { color: #333; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <h1>🔧 User Manager Plugin Diagnostic</h1>
    
    <?php
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    // Test 1: Plugin Active
    echo '<div class="test">';
    echo '<h3>✓ Test 1: Plugin Status</h3>';
    try {
        $plugin = \App\Models\Plugin::where('name', 'user-manager')->first();
        if ($plugin && $plugin->status === 'active') {
            echo '<p class="status" style="color: green;">PASS - Plugin is active</p>';
            echo '<div class="code">Name: ' . $plugin->name . '<br>Status: ' . $plugin->status . '</div>';
        } else {
            echo '<p class="status" style="color: red;">FAIL - Plugin not active</p>';
        }
    } catch (\Exception $e) {
        echo '<p class="status" style="color: red;">ERROR: ' . $e->getMessage() . '</p>';
    }
    echo '</div>';
    
    // Test 2: Routes Registered
    echo '<div class="test">';
    echo '<h3>✓ Test 2: Routes Registration</h3>';
    try {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();
        $userRoutes = [];
        foreach ($routes as $route) {
            if (str_contains($route->getName() ?? '', 'admin.users')) {
                $userRoutes[] = $route->getName() . ' => ' . $route->uri();
            }
        }
        if (count($userRoutes) > 0) {
            echo '<p class="status" style="color: green;">PASS - Found ' . count($userRoutes) . ' user management routes</p>';
            echo '<div class="code">' . implode('<br>', $userRoutes) . '</div>';
        } else {
            echo '<p class="status" style="color: red;">FAIL - No user management routes found</p>';
        }
    } catch (\Exception $e) {
        echo '<p class="status" style="color: red;">ERROR: ' . $e->getMessage() . '</p>';
    }
    echo '</div>';
    
    // Test 3: Controller Exists
    echo '<div class="test">';
    echo '<h3>✓ Test 3: Controller Class</h3>';
    if (class_exists('LaraVeil\UserManager\Controllers\UserController')) {
        echo '<p class="status" style="color: green;">PASS - UserController class exists</p>';
        echo '<div class="code">Class: LaraVeil\UserManager\Controllers\UserController</div>';
    } else {
        echo '<p class="status" style="color: red;">FAIL - UserController class not found</p>';
    }
    echo '</div>';
    
    // Test 4: Views Registered
    echo '<div class="test">';
    echo '<h3>✓ Test 4: Views Registration</h3>';
    try {
        $viewExists = view()->exists('user-manager::users.index');
        if ($viewExists) {
            echo '<p class="status" style="color: green;">PASS - User manager views are registered</p>';
            echo '<div class="code">View namespace: user-manager</div>';
        } else {
            echo '<p class="status" style="color: red;">FAIL - Views not found</p>';
        }
    } catch (\Exception $e) {
        echo '<p class="status" style="color: red;">ERROR: ' . $e->getMessage() . '</p>';
    }
    echo '</div>';
    
    // Test 5: Route URL
    echo '<div class="test">';
    echo '<h3>✓ Test 5: Route URL Generation</h3>';
    try {
        $url = route('admin.users.index');
        echo '<p class="status" style="color: green;">PASS - Route URL generated successfully</p>';
        echo '<div class="code">URL: ' . $url . '</div>';
        echo '<p><a href="' . $url . '" style="color: #2196F3; text-decoration: none;">→ Click here to access User Manager</a></p>';
    } catch (\Exception $e) {
        echo '<p class="status" style="color: red;">ERROR: ' . $e->getMessage() . '</p>';
    }
    echo '</div>';
    
    ?>
    
    <div style="margin-top: 30px; padding: 15px; background: #e3f2fd; border-radius: 8px;">
        <h3>📝 Summary</h3>
        <p>If all tests pass, the User Manager plugin is properly configured. Access it by:</p>
        <ol>
            <li>Logging into your application</li>
            <li>Looking for "Users" in the "Administration" section of the sidebar</li>
            <li>Or directly navigate to: <code>/admin/user-list</code></li>
        </ol>
    </div>
</body>
</html>
