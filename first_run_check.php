<?php
/**
 * First Run Check - Deployment Initialization Test
 * Verifies that the application is ready for use
 */

define('PROJECT_ACCESS', true);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$test_results = [];

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  FIRST RUN CHECK - DEPLOYMENT INITIALIZATION               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "Environment: " . APP_ENV . "\n";
echo "Debug Mode: " . (DEBUG ? 'ON' : 'OFF') . "\n";
echo "Site URL: " . SITE_URL . "\n";
echo "Test Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test 1: Database Connection
echo "━━━ 1. DATABASE CONNECTION ━━━\n";
try {
    require_once __DIR__ . '/app/Core/Database.php';
    $db = \App\Core\Database::connect();
    
    if ($db) {
        echo "✓ Database connection established\n";
        $test_results['db_connect'] = true;
        
        // Test query
        $stmt = $db->query("SELECT 1");
        if ($stmt) {
            echo "✓ Database query execution working\n";
            $test_results['db_query'] = true;
        }
    }
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    $test_results['db_connect'] = false;
}
echo "\n";

// Test 2: Check Upload Directory
echo "━━━ 2. UPLOAD DIRECTORIES ━━━\n";
$upload_dirs = [
    UPLOAD_PATH,
    UPLOAD_PATH . 'products/',
    UPLOAD_PATH . 'services/',
    UPLOAD_PATH . 'articles/',
    UPLOAD_PATH . 'avatars/',
    UPLOAD_PATH . 'temp/',
];

foreach ($upload_dirs as $dir) {
    if (is_dir($dir)) {
        echo "✓ Directory exists: " . basename($dir) . "/\n";
        $test_results['upload_' . basename($dir)] = true;
    } else {
        echo "⚠ Directory missing: $dir\n";
        $test_results['upload_' . basename($dir)] = false;
    }
}
echo "\n";

// Test 3: Check Configuration
echo "━━━ 3. CONFIGURATION ━━━\n";
$config_checks = [
    'SITE_NAME' => SITE_NAME,
    'SITE_URL' => SITE_URL,
    'DB_HOST' => DB_HOST,
    'DB_NAME' => DB_NAME,
    'APP_ENV' => APP_ENV,
];

foreach ($config_checks as $key => $value) {
    if (!empty($value)) {
        echo "✓ $key = " . substr($value, 0, 40) . (strlen($value) > 40 ? '...' : '') . "\n";
        $test_results['config_' . strtolower($key)] = true;
    }
}
echo "\n";

// Test 4: Check Views
echo "━━━ 4. CRITICAL VIEW FILES ━━━\n";
$views = [
    'app/Views/home/index.php' => 'Homepage',
    'app/Views/admin/login.php' => 'Admin Login',
    'app/Views/account/login.php' => 'User Login',
    'app/Views/booking/create.php' => 'Booking Form',
    'app/Views/layouts/header.php' => 'Header Layout',
];

foreach ($views as $path => $name) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "✓ View exists: $name\n";
        $test_results['view_' . str_slug($name)] = true;
    } else {
        echo "✗ View missing: $name ($path)\n";
        $test_results['view_' . str_slug($name)] = false;
    }
}
echo "\n";

// Test 5: Routes
echo "━━━ 5. ROUTING SYSTEM ━━━\n";
$routes_file = __DIR__ . '/app/routes.php';
if (file_exists($routes_file)) {
    $routes_content = file_get_contents($routes_file);
    $route_count = substr_count($routes_content, '$router->');
    echo "✓ Routes file exists\n";
    echo "✓ Routes defined: $route_count\n";
    $test_results['routes_file'] = true;
    $test_results['routes_count'] = $route_count >= 30;
} else {
    echo "✗ Routes file not found\n";
    $test_results['routes_file'] = false;
}
echo "\n";

// Test 6: Security Functions
echo "━━━ 6. SECURITY FUNCTIONS ━━━\n";
$security_functions = [
    'csrf_token' => 'CSRF Token Generator',
    'csrf_field' => 'CSRF Field Helper',
    'verify_csrf' => 'CSRF Verification',
    'e' => 'HTML Escape',
    'password_verify' => 'Password Verification',
];

foreach ($security_functions as $func => $name) {
    if (function_exists($func)) {
        echo "✓ $name available\n";
        $test_results['security_' . $func] = true;
    } else {
        echo "✗ $name NOT available\n";
        $test_results['security_' . $func] = false;
    }
}
echo "\n";

// Test 7: Database Tables (if connected)
if ($test_results['db_connect'] ?? false) {
    echo "━━━ 7. DATABASE TABLES ━━━\n";
    
    try {
        $stmt = $db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "'");
        $tables = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        if (count($tables) > 0) {
            echo "✓ Database contains " . count($tables) . " tables\n";
            $test_results['db_tables'] = true;
            
            // Check for critical tables
            $table_names = array_column($tables, 'TABLE_NAME');
            $critical_tables = ['users', 'services', 'products', 'bookings'];
            $missing = array_diff($critical_tables, $table_names);
            
            if (empty($missing)) {
                echo "✓ All critical tables present\n";
                $test_results['critical_tables'] = true;
            } else {
                echo "⚠ Missing tables: " . implode(', ', $missing) . "\n";
                echo "   Run: mysql -u root -p original_east < database/production_schema.sql\n";
                $test_results['critical_tables'] = false;
            }
        } else {
            echo "⚠ Database is empty\n";
            echo "   Run: mysql -u root -p original_east < database/production_schema.sql\n";
            echo "   Then: mysql -u root -p original_east < database/seed.sql\n";
            $test_results['db_tables'] = false;
        }
    } catch (Exception $e) {
        echo "⚠ Could not query database: " . $e->getMessage() . "\n";
        $test_results['db_tables'] = false;
    }
    echo "\n";
}

// Summary
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  TEST SUMMARY                                              ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$passed = count(array_filter($test_results));
$total = count($test_results);
$pass_rate = $total > 0 ? round(($passed / $total) * 100) : 0;

echo "Tests Passed: $passed/$total (" . $pass_rate . "%)\n\n";

// Identify blockers
$blockers = [];

if (!($test_results['db_connect'] ?? false)) {
    $blockers[] = "Database connection failed - verify DB_HOST, DB_NAME, DB_USER, DB_PASS";
}

if (!($test_results['critical_tables'] ?? true)) {
    $blockers[] = "Database schema not imported - run production_schema.sql";
}

if (!($test_results['upload_uploads'] ?? true)) {
    $blockers[] = "Upload directory not created - run mkdir -p public/uploads/{products,services,articles,avatars,temp}";
}

if (!empty($blockers)) {
    echo "⚠ BLOCKERS DETECTED:\n";
    foreach ($blockers as $i => $blocker) {
        echo "  " . ($i + 1) . ". $blocker\n";
    }
    echo "\n";
}

// Status determination
if ($pass_rate >= 90 && empty($blockers)) {
    echo "Status: ✓ READY FOR DEPLOYMENT\n";
    echo "The application is ready for first run and user access.\n";
} elseif ($pass_rate >= 70) {
    echo "Status: ⚠ REQUIRES INITIALIZATION\n";
    echo "Complete the blockers above before accessing the application.\n";
} else {
    echo "Status: ✗ DEPLOYMENT BLOCKED\n";
    echo "Critical errors must be resolved before deployment.\n";
}

echo "\nNext Steps:\n";
echo "  1. Resolve any blockers listed above\n";
echo "  2. Import database schema: database/production_schema.sql\n";
echo "  3. Import seed data: database/seed.sql\n";
echo "  4. Access admin: http://" . str_replace(['http://', 'https://'], '', SITE_URL) . "/admin/login\n";
echo "  5. Login with: admin@example.com / admin123\n";
echo "  6. Change password immediately\n";

echo "\n";
echo "Report Generated: " . date('Y-m-d H:i:s') . "\n";

// Helper function
function str_slug($text) {
    return strtolower(preg_replace('/[^a-z0-9]+/', '_', preg_replace('/[^a-z0-9]/i', ' ', $text)));
}
