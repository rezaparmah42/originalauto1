<?php
/**
 * Temporary diagnostic harness for the admin dashboard render path.
 * It sets an in-memory admin session only, invokes the real controller method,
 * and captures the PHP output/errors without altering the persistent session state.
 */

require_once __DIR__ . '/config/config.php';

if (file_exists(__DIR__ . '/app/Core/Database.php')) {
    require_once __DIR__ . '/app/Core/Database.php';
    if (!class_exists('Database') && class_exists('App\\Core\\Database')) {
        class_alias('App\\Core\\Database', 'Database');
    }
}

define('PROJECT_ACCESS', true);
require_once __DIR__ . '/includes/functions.php';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', '1');
}

session_name('originalshargh_session');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$originalSession = $_SESSION;
$_SESSION['admin'] = [
    'id' => 999,
    'name' => 'DiagTester',
    'email' => 'diagtester@localhost',
    'role' => 'admin',
];

$errors = [];
set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$errors) {
    $errors[] = [
        'type' => 'php_error',
        'errno' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
    ];
    return true;
});

ob_start();
try {
    $controller = new App\Controllers\AdminController();
    $controller->dashboard();
    $output = ob_get_clean();
} catch (Throwable $e) {
    $output = ob_get_clean();
    $errors[] = [
        'type' => 'exception',
        'class' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];
}
restore_error_handler();

$_SESSION = $originalSession;

$logFile = __DIR__ . '/_debug_admin_render.log';
file_put_contents($logFile, json_encode([
    'rendered_html_length' => strlen($output ?? ''),
    'php_errors' => $errors,
    'output_snippet' => substr(strip_tags((string) ($output ?? '')), 0, 500),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if (strlen((string) $output) > 0) {
    file_put_contents(__DIR__ . '/_debug_admin_render_output.html', $output);
}

if (!empty($errors)) {
    file_put_contents(__DIR__ . '/_debug_admin_render_errors.json', json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

echo "ADMIN_RENDER_DIAGNOSTIC:OK\n";
