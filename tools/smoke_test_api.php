<?php
// Simple smoke test script for API token flow
echo "SMOKE TEST START\n";
require_once __DIR__ . '/../config/config.php';
if (!defined('PROJECT_ACCESS')) {
    define('PROJECT_ACCESS', true);
}
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Models/Model.php';
require_once __DIR__ . '/../app/Models/ApiToken.php';
require_once __DIR__ . '/../app/Models/ApiDevice.php';
require_once __DIR__ . '/../app/Models/ApiLog.php';

use App\Core\Database;
use App\Models\ApiToken;

$db = Database::connect();
$stmt = $db->query('SELECT id FROM users LIMIT 1');
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (empty($user) || empty($user['id'])) {
    echo "No users found to test against\n";
    exit(1);
}

$api = new ApiToken();
$token = $api->createToken((int)$user['id'], 'smoke-test-token');
if (!$token) {
    echo "Failed to create token\n";
    exit(1);
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, SITE_URL . '/api/vehicles');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "First call HTTP: $http\n";
if ($http < 200 || $http >= 300) {
    echo "Protected endpoint did not return success. HTTP=$http\n";
    echo "curl_error: $err\n";
    echo "response length: " . strlen((string)$res) . "\n";
    echo "response: $res\n";
    exit(2);

}

// Revoke token
$api->revokeToken($token);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, SITE_URL . '/api/vehicles');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
$res2 = curl_exec($ch);
$http2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Second call HTTP: $http2\n";
if ($http2 === $http) {
    echo "Token revoke did not change response code.\n";
    exit(3);
}

echo "Smoke test passed.\n";
exit(0);
