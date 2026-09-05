<?php
require_once __DIR__ . '/../config/config.php';

$baseUrl = getenv('APP_URL') ?: 'http://localhost/originalshargh';

function request($url, $method = 'GET', $headers = [], $body = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if ($method === 'POST' && $body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['status' => $info['http_code'], 'response' => $data];
}

// customer login sample
$loginUrl = rtrim($baseUrl, '/') . '/api/auth/login';
$loginData = json_encode(['login' => '09116438660', 'password' => 'password']);
$result = request($loginUrl, 'POST', ['Content-Type: application/json'], $loginData);
echo "API LOGIN -> " . $result['status'] . "\n";
if ($result['status'] === 200) {
    preg_match('/\{.*\}/s', $result['response'], $matches);
    $json = json_decode($matches[0] ?? '', true);
    if (!empty($json['data']['token'])) {
        $token = $json['data']['token'];
        echo "Token found: " . substr($token, 0, 16) . "...\n";
        $vehicles = request(rtrim($baseUrl, '/') . '/api/vehicles', 'GET', ["Authorization: Bearer $token"]);
        echo "/api/vehicles -> " . $vehicles['status'] . "\n";
        $diag = request(rtrim($baseUrl, '/') . '/api/diagnostics', 'GET', ["Authorization: Bearer $token"]);
        echo "/api/diagnostics -> " . $diag['status'] . "\n";
    } else {
        echo "No token returned.\n";
    }
} else {
    echo "Login response body: " . substr($result['response'], 0, 400) . "\n";
}
