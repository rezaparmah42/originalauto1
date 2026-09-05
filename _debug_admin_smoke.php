<?php
// Temporary diagnostic: admin smoke test
// Scans app/routes.php for admin routes, bootstraps app, sets in-memory admin session,
// invokes each route via Router->run(), captures output, PHP errors, and Apache error.log deltas.
// Artifacts are written to project root and removed after verification by the operator.

require_once __DIR__ . '/config/config.php';

// Minimal bootstrap (no router run by default)
if (file_exists(__DIR__ . '/app/Core/Database.php')) {
    require_once __DIR__ . '/app/Core/Database.php';
    if (!class_exists('Database') && class_exists('App\\Core\\Database')) {
        class_alias('App\\Core\\Database', 'Database');
    }
}

define('PROJECT_ACCESS', true);
require_once __DIR__ . '/includes/functions.php';

// Start session like the app
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_name('originalshargh_session');
if (session_status() === PHP_SESSION_NONE) session_start();

$originalSession = $_SESSION;

// Set ephemeral admin session in-memory only
$_SESSION['admin'] = [
    'id' => 0,
    'name' => 'SmokeTester',
    'email' => 'smoke@localhost',
    'role' => 'admin',
];

// Read routes.php and extract admin routes
$routesFile = __DIR__ . '/app/routes.php';
$routesSrc = @file_get_contents($routesFile);
if ($routesSrc === false) {
    echo json_encode(['error' => 'Failed to read routes.php']);
    exit(1);
}

$paths = [];
// match get and post routes
if (preg_match_all('/\$router->(get|post)\(\s*\'([^\']+)\'/i', $routesSrc, $m)) {
    foreach ($m[1] as $i => $method) {
        $path = $m[2][$i];
        if (strpos($path, '/admin') === 0) {
            $paths[] = ['method' => strtoupper($method), 'path' => $path];
        }
    }
}

// normalize placeholders
function fill_placeholders($path) {
    $replacements = [
        '{id}' => '1',
        '{slug}' => 'test-slug',
        '{vehicle}' => '1',
        '{brand}' => '1',
        '{name}' => 'test',
    ];
    return preg_replace_callback('/\{[^}]+\}/', function($m) use ($replacements){
        $key = $m[0];
        return $replacements[$key] ?? '1';
    }, $path);
}

$unique = [];
foreach ($paths as $p) {
    $p['path'] = rtrim(fill_placeholders($p['path']), '/');
    if ($p['path'] === '') $p['path'] = '/';
    $key = $p['method'] . ' ' . $p['path'];
    $unique[$key] = $p;
}
$tests = array_values($unique);

// Prepare Router and include routes into it
$router = new App\Core\Router();
// routes.php expects $router to exist
require __DIR__ . '/app/routes.php';

$errLog = 'C:/xampp/apache/logs/error.log';
$beforeAll = '';
if (file_exists($errLog)) {
    $lines = @file($errLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines !== false) {
        $beforeAll = implode("\n", array_slice($lines, -200));
    }
}
file_put_contents(__DIR__ . '/smoke_before_error.log', $beforeAll);

$results = [];

foreach ($tests as $t) {
    $method = $t['method'];
    $path = $t['path'];

    // Set server vars expected by Router
    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $path;
    $_SERVER['SCRIPT_NAME'] = '/' . basename(__FILE__);

    // capture error log before
    $before = '';
    if (file_exists($errLog)) {
        $lines = @file($errLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines !== false) {
            $before = implode("\n", array_slice($lines, -200));
        }
    }

    $errors = [];
    set_error_handler(function($errno, $errstr, $errfile, $errline) use (&$errors){
        $errors[] = ['type' => 'php_error', 'errno' => $errno, 'message' => $errstr, 'file' => $errfile, 'line' => $errline];
        return true;
    });

    ob_start();
    $status = 200;
    try {
        $router->run();
        // Router may set http_response_code
        $status = http_response_code() ?: 200;
    } catch (Throwable $e) {
        $errors[] = ['type' => 'exception', 'class' => get_class($e), 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTraceAsString()];
        $status = 500;
    }
    $out = ob_get_clean();
    restore_error_handler();

    // capture after error log
    $after = '';
    if (file_exists($errLog)) {
        $lines = @file($errLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines !== false) {
            $after = implode("\n", array_slice($lines, -200));
        }
    }

    // compute new log lines
    $newLog = '';
    if ($after !== '') {
        if ($before === '') {
            $newLog = $after;
        } else {
            // show lines in $after that are not in $before suffix-wise
            $beforeLines = explode("\n", $before);
            $afterLines = explode("\n", $after);
            $new = array_slice($afterLines, max(0, count($afterLines) - count($afterLines)));
            // naive: subtract by comparing from end
            $newLog = implode("\n", $afterLines);
        }
    }

    // detect common SQL/unknown column snippets in errors and output
    $sqlErrors = [];
    foreach ($errors as $er) {
        if (stripos($er['message'] ?? '', 'SQLSTATE') !== false || stripos($er['message'] ?? '', 'PDOException') !== false || stripos($er['message'] ?? '', 'Unknown column') !== false) {
            $sqlErrors[] = $er;
        }
    }

    // determine if admin view rendered: simple heuristic
    $renderedAdminView = false;
    if (stripos($out, 'داشبورد مدیریت') !== false || stripos($out, 'admin-dashboard') !== false || stripos($out, 'مدیریت') !== false) {
        $renderedAdminView = true;
    }

    $safePathName = preg_replace('/[^a-z0-9_\-]/i', '_', trim($path, '/')) ?: 'root';
    $baseName = 'smoke_' . $method . '_' . $safePathName;
    file_put_contents(__DIR__ . '/' . $baseName . '.html', $out);
    file_put_contents(__DIR__ . '/' . $baseName . '.errors.json', json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    file_put_contents(__DIR__ . '/' . $baseName . '.log_after.txt', $after);

    $results[] = [
        'method' => $method,
        'path' => $path,
        'status' => $status,
        'rendered' => $renderedAdminView,
        'php_errors' => $errors,
        'sql_errors' => $sqlErrors,
        'log_after' => $after,
        'artifact_html' => $baseName . '.html',
        'artifact_errors' => $baseName . '.errors.json',
        'artifact_log' => $baseName . '.log_after.txt',
    ];

    // small delay
    usleep(50000);
}

// restore session
$_SESSION = $originalSession;

file_put_contents(__DIR__ . '/smoke_results.json', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "SMOKE_DONE\n";
echo "Results: smoke_results.json\n";
?>