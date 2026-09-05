<?php
if (!defined('PROJECT_ACCESS')) {
    define('PROJECT_ACCESS', true);
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    ini_set('session.cookie_secure', '1');
}

if (session_status() === PHP_SESSION_NONE) {
    session_name('originalshargh_session');
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
header('Permissions-Policy: interest-cohort=()');

spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = dirname(__DIR__) . '/app/' . $class . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

$products = [];
$filteredProducts = [];
$brands = [];
$models = [];
$years = [];
$engines = [];
$filter = [
    'brand' => '',
    'model' => '',
    'year' => '',
    'engine' => '',
];

require_once dirname(__DIR__) . '/app/Views/shop/index.php';
