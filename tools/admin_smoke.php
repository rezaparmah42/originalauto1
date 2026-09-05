<?php
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

// Provide a non-exiting redirect stub for smoke tests so tests continue after redirects
if (!function_exists('redirect')) {
    function redirect($url)
    {
        // Print redirect target to output instead of exiting
        echo "[SMOKE REDIRECT] " . $url . "\n";
    }
}

// Now include normal helpers; our stub prevents exiting redirects during smoke testing
require_once __DIR__ . '/../includes/functions.php';

// Autoload (same as index.php)
spl_autoload_register(function($class){
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/../app/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Session same as index.php
session_name('originalshargh_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

use App\Core\Router;

$router = new Router();
require_once __DIR__ . '/../app/routes.php';

$routes = [
    '/originalshargh/admin',
    '/originalshargh/admin/dashboard',
    '/originalshargh/admin/users',
    '/originalshargh/admin/services',
    '/originalshargh/admin/products',
    '/originalshargh/admin/articles',
    '/originalshargh/admin/bookings',
    '/originalshargh/admin/settings',
];

// Ensure admin session present
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Smoke Tester',
    'email' => 'admin@originalshargh.com',
    'role' => 'admin',
];

$results = [];
foreach ($routes as $uri) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';

    ob_start();
    try {
        $router->run();
    } catch (Throwable $t) {
        echo "EXCEPTION: " . get_class($t) . " - " . $t->getMessage() . "\n";
        echo $t->getTraceAsString();
    }
    $out = ob_get_clean();

    $errors = [];
    if (stripos($out, 'Fatal error') !== false || stripos($out, 'Uncaught') !== false || stripos($out, 'Exception') !== false || stripos($out, 'PDOException') !== false) {
        $errors[] = trim(preg_replace('/\s+/', ' ', substr($out, 0, 1000)));
    }

    $results[$uri] = [
        'length' => strlen($out),
        'has_errors' => !empty($errors),
        'errors' => $errors,
        'output_snippet' => substr(strip_tags($out), 0, 500),
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
