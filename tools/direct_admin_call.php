<?php
// Minimal bootstrap to call AdminController->dashboard without full index bootstrap
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['admin'] = [
    'id' => 1,
    'name' => 'Dev Admin',
    'email' => 'admin@example.test',
    'role' => 'admin',
];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/originalshargh/admin';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';

use App\Controllers\AdminController;
try {
    $c = new AdminController();
    $c->dashboard();
    echo "OK\n";
} catch (Throwable $t) {
    echo "EXCEPTION: " . get_class($t) . " - " . $t->getMessage() . "\n";
    echo $t->getTraceAsString();
}
