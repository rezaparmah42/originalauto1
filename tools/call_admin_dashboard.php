<?php
// Setup minimal server context and session for admin access
chdir(__DIR__ . '/..');
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/originalshargh/admin';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Mock admin session to bypass requireLogin()
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Dev Admin',
    'email' => 'admin@example.test',
    'role' => 'admin',
];

require_once __DIR__ . '/../index.php';
use App\Controllers\AdminController;
try {
    $c = new AdminController();
    $c->dashboard();
    echo "OK\n";
} catch (Throwable $t) {
    echo "EXCEPTION: " . get_class($t) . " - " . $t->getMessage() . "\n";
    echo $t->getTraceAsString();
}
