<?php
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';
use App\Models\Admin as AdminModel;
try {
    $m = new AdminModel();
    $stats = $m->getDashboardStats();
    echo "OK\n";
    print_r($stats);
} catch (Throwable $t) {
    echo "EXCEPTION: " . get_class($t) . " - " . $t->getMessage() . "\n";
    echo $t->getTraceAsString();
}
