<?php
require_once __DIR__ . '/../config/config.php';

$baseUrl = getenv('APP_URL') ?: 'http://localhost/originalshargh';
$urls = [
    '/',
    '/login',
    '/register',
    '/account/dashboard',
    '/admin/dashboard',
    '/vehicles',
    '/bookings',
    '/cart',
    '/checkout',
    '/orders',
    '/payment/start',
    '/invoice/show/1',
    '/diagnostic',
    '/api/auth/login',
    '/api/vehicles',
    '/api/diagnostics'
];
$ch = curl_init();
$results = [];
foreach ($urls as $path) {
    $url = rtrim($baseUrl, '/') . $path;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $data = curl_exec($ch);
    if ($data === false) {
        $results[$path] = ['status' => 'error', 'error' => curl_error($ch)];
        continue;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $results[$path] = ['status' => $httpCode];
}
curl_close($ch);

foreach ($results as $path => $result) {
    echo $path . ': ' . (is_array($result) && isset($result['error']) ? $result['error'] : $result['status']) . "\n";
}
