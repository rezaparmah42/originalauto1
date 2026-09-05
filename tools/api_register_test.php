<?php
require_once __DIR__ . '/../config/config.php';

$baseUrl = getenv('APP_URL') ?: 'http://localhost/originalshargh';
function request($url, $method = 'POST', $headers = [], $body = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($method === 'POST' && $body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    $headerSize = $info['header_size'] ?? 0;
    $body = $headerSize ? substr($data, $headerSize) : $data;
    return ['status' => $info['http_code'], 'response' => $body];
}

$phone = '09' . str_pad((string) rand(0, 999999999), 9, '0', STR_PAD_LEFT);
$email = 'smoke' . time() . '@example.com';
$payload = json_encode([
    'name' => 'Smoke Tester',
    'phone' => $phone,
    'email' => $email,
    'password' => 'Test12345',
    'confirm_password' => 'Test12345',
]);
$url = rtrim($baseUrl, '/') . '/api/auth/register';
$result = request($url, 'POST', ['Content-Type: application/json'], $payload);
echo "REGISTER status: " . $result['status'] . "\n";
echo "Body: " . substr($result['response'], 0, 800) . "\n";
if ($result['status'] === 200) {
    $json = json_decode($result['response'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "JSON decode error: " . json_last_error_msg() . "\n";
    } elseif (!empty($json['token'])) {
        $token = $json['token'];
        echo "Token created: " . substr($token, 0, 16) . "...\n";
        $vehicles = request(rtrim($baseUrl, '/') . '/api/vehicles', 'GET', ["Authorization: Bearer $token"]);
        echo "/api/vehicles -> " . $vehicles['status'] . "\n";
        $diagnostics = request(rtrim($baseUrl, '/') . '/api/diagnostics', 'GET', ["Authorization: Bearer $token"]);
        echo "/api/diagnostics -> " . $diagnostics['status'] . "\n";
    } else {
        echo "No token returned in response.\n";
    }
}
