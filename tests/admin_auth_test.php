<?php
// Minimal admin auth tester
// Usage: php tests/admin_auth_test.php

define('SITE_URL', 'http://localhost/originalshargh');

// Provide a non-exiting redirect to observe access decisions
if (!function_exists('redirect')) {
    function redirect($url)
    {
        // Signal redirect via exception with URL
        throw new \RuntimeException('REDIRECT->' . $url);
    }
}

// Load helpers
require_once __DIR__ . '/../includes/functions.php';

// List of controllers and a representative admin method to call
$tests = [
    ['class' => \App\Controllers\AdminController::class, 'method' => 'dashboard'],
    ['class' => \App\Controllers\AdminOrderController::class, 'method' => 'index'],
    ['class' => \App\Controllers\InventoryController::class, 'method' => 'index'],
    ['class' => \App\Controllers\ProductController::class, 'method' => 'index'],
    ['class' => \App\Controllers\ArticleController::class, 'method' => 'index'],
    ['class' => \App\Controllers\ServiceController::class, 'method' => 'index'],
    ['class' => \App\Controllers\SupplierController::class, 'method' => 'index'],
    ['class' => \App\Controllers\BookingController::class, 'method' => 'index'],
];

// Session scenarios
$scenarios = [
    'NO_SESSION' => [],
    'CUSTOMER' => ['customer_id' => 42, 'customer_name' => 'Customer A'],
    'ADMIN' => ['admin' => ['id' => 1, 'role' => 'admin', 'name' => 'SysAdmin']],
    'MANAGER' => ['admin' => ['id' => 2, 'role' => 'manager', 'name' => 'Manager']],
];

$results = [];

foreach ($tests as $t) {
    foreach ($scenarios as $name => $sess) {
        // isolate session
        @session_start();
        $_SESSION = [];
        foreach ($sess as $k => $v) {
            $_SESSION[$k] = $v;
        }

        $class = $t['class'];
        $method = $t['method'];

        $key = $class . '::' . $method . ' -- ' . $name;
        try {
            $obj = new $class();
            // call method
            $obj->{$method}();
            $results[$key] = 'ALLOW';
        } catch (\RuntimeException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, 'REDIRECT') === 0) {
                $results[$key] = 'DENY';
            } else {
                $results[$key] = 'ERROR: ' . $msg;
            }
        } catch (\Throwable $e) {
            $results[$key] = 'ERROR: ' . $e->getMessage();
        }
    }
}

// Output simple machine-readable results to file
$outFile = __DIR__ . '/admin_auth_test_results.log';
$lines = [];
foreach ($results as $k => $v) {
    $lines[] = $k . ' => ' . $v;
}
file_put_contents($outFile, implode(PHP_EOL, $lines));
echo "WROTE:" . $outFile . PHP_EOL;
exit(0);
