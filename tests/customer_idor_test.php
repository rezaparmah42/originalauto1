<?php
// Focused IDOR/ownership verifier for vehicles -> AIRepairController
// Usage: php tests/customer_idor_test.php

require_once __DIR__ . '/../config/config.php';

// Override redirect to throw so we can detect deny without exiting
if (!function_exists('redirect')) {
    function redirect($url)
    {
        throw new \RuntimeException('REDIRECT->' . $url);
    }
}

require_once __DIR__ . '/../includes/functions.php';

// load models and controller
require_once __DIR__ . '/../app/Controllers/AIRepairController.php';
require_once __DIR__ . '/../app/Models/Vehicle.php';

use App\Controllers\AIRepairController;
use App\Models\Vehicle;

$vehicleModel = new Vehicle();
$conn = $vehicleModel->db;

$stmt = $conn->prepare('SELECT id, user_id FROM vehicles ORDER BY id ASC LIMIT 10');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$found = [];
foreach ($rows as $r) {
    $found[(int)$r['user_id']][] = (int)$r['id'];
}

$keys = array_keys($found);
if (count($keys) < 2) {
    echo "NEED_AT_LEAST_TWO_DISTINCT_USERS\n";
    exit(2);
}

$userA = $keys[0];
$userB = $keys[1];
$vehicleA = $found[$userA][0];
$vehicleB = $found[$userB][0];

$results = [];

// Scenario: CUSTOMER_A accesses own vehicle -> should ALLOW (no redirect)
@session_start();
$_SESSION = ['customer_id' => $userA];
try {
    $c = new AIRepairController();
    $c->report($vehicleA);
    $results[] = "CUSTOMER_A_OWN_RESOURCE=ALLOW";
} catch (\RuntimeException $e) {
    $results[] = "CUSTOMER_A_OWN_RESOURCE=DENY (" . $e->getMessage() . ")";
} catch (\Throwable $e) {
    $results[] = "CUSTOMER_A_OWN_RESOURCE=ERROR (" . $e->getMessage() . ")";
}

// Scenario: CUSTOMER_A accesses CUSTOMER_B vehicle -> should DENY (redirect)
@session_start();
$_SESSION = ['customer_id' => $userA];
try {
    $c = new AIRepairController();
    $c->report($vehicleB);
    $results[] = "CUSTOMER_A_OTHER_RESOURCE=ALLOW";
} catch (\RuntimeException $e) {
    $results[] = "CUSTOMER_A_OTHER_RESOURCE=DENY (" . $e->getMessage() . ")";
} catch (\Throwable $e) {
    $results[] = "CUSTOMER_A_OTHER_RESOURCE=ERROR (" . $e->getMessage() . ")";
}

file_put_contents(__DIR__ . '/customer_idor_results.txt', implode("\n", $results));
echo "WROTE\n";
