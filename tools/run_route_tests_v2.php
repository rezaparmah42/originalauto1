<?php
require_once __DIR__ . '/../config/config.php';

$baseUrl = getenv('APP_URL') ?: 'http://localhost/originalshargh';
$tests = [
    ['path' => '/', 'method' => 'GET'],
    ['path' => '/login', 'method' => 'GET'],
    ['path' => '/register', 'method' => 'GET'],
    ['path' => '/account/dashboard', 'method' => 'GET'],
    ['path' => '/admin/dashboard', 'method' => 'GET'],
    ['path' => '/vehicles', 'method' => 'GET'],
    ['path' => '/cart', 'method' => 'GET'],
    ['path' => '/checkout', 'method' => 'GET'],
    ['path' => '/orders', 'method' => 'GET'],
    ['path' => '/payment/start', 'method' => 'GET'],
    ['path' => '/invoice/show/1', 'method' => 'GET'],
    ['path' => '/diagnostic', 'method' => 'GET'],
    ['path' => '/api/auth/login', 'method' => 'POST', 'body' => ['username' => 'test', 'password' => 'test']],
    ['path' => '/api/vehicles', 'method' => 'GET'],
    ['path' => '/api/diagnostics', 'method' => 'GET'],
];

foreach ($tests as $test) {
    $path = $test['path'];
    $method = $test['method'];
    $url = rtrim($baseUrl, '/') . $path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($test['body']));
    }
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    $httpCode = $info['http_code'];
    echo "$method $path -> $httpCode\n";
    curl_close($ch);
}
