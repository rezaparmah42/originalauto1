<?php

require_once __DIR__ . '/config/config.php';

// Ensure core Database class is available as early fallback for legacy helpers
// Some legacy files call `Database::connect()` without a namespace; load the
// namespaced class and provide a global alias to avoid fatal errors.
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

// Defensive: block direct HTTP access to sensitive diagnostic artifacts under /logs
// This prevents accidental exposure when .htaccess is not enabled in Apache.
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
if (preg_match('#(^|/)logs($|/)#i', $requestPath)) {
    // send 403 Forbidden and stop further processing
    http_response_code(403);
    echo "403 Forbidden";
    exit;
}

// Additionally block direct access to top-level diagnostic files (e.g. *.log)
// Some servers rewrite requests to the front controller; ensure .log files
// cannot be retrieved via PHP if they remain in the webroot.
if (preg_match('#\.(?:log)$#i', $requestPath) || preg_match('#(^|/)smoke_before_error\.log$#i', $requestPath)) {
    http_response_code(403);
    echo "403 Forbidden";
    exit;
}

// Prevent running in production with the default local APP_KEY which would
// make HMAC tokens (payment callbacks, etc.) trivially forgeable. Only
// enforce when the host is not a local development host to avoid blocking
// local test environments.
if (defined('APP_ENV') && APP_ENV === 'production') {
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
    $isLocalHost = preg_match('/^(localhost|127\.0\.0\.1|::1)(:\d+)?$/i', $host);
    if (!$isLocalHost) {
        $defaultKeys = ['originalshargh-local-dev-key-change-me', 'originalshargh-local-dev-key'];
        if (defined('APP_KEY') && in_array(APP_KEY, $defaultKeys, true)) {
            // Log and stop to avoid insecure production runs.
            error_log('Insecure APP_KEY in production detected; aborting startup.');
            http_response_code(500);
            echo "500 Server Error";
            exit;
        }
    }
}

error_reporting(E_ALL);
if (defined('DEBUG') && DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
header('Permissions-Policy: interest-cohort=()');
// Basic Content Security Policy to reduce XSS attack surface. Keep conservative and
// adjust per resource needs (images/fonts/CDN) as features roll out.
header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https:; connect-src 'self' https:; frame-ancestors 'none';");

spl_autoload_register(function($class){
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/app/' . $class . '.php';

    if(file_exists($file)){
        require_once $file;
    }
});

use App\Core\Router;

$router = new Router();

require_once __DIR__ . '/app/routes.php';
require_once __DIR__ . '/app/api_routes.php';

$router->run();
