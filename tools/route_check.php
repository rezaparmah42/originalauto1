<?php
require_once __DIR__ . '/../config/config.php';
$baseUrl = getenv('APP_URL') ?: 'http://localhost/originalshargh';
$requests = [
    ['GET', '/'],
    ['GET', '/login'],
    ['GET', '/register'],
    ['GET', '/account/dashboard'],
    ['GET', '/admin/dashboard'],
    ['GET', '/vehicles'],
    ['GET', '/bookings'],
    ['GET', '/cart'],
    ['GET', '/checkout'],
    ['GET', '/orders'],
    ['GET', '/payment/start'],
    ['GET', '/invoice/show/1'],
    ['GET', '/diagnostic'],
    ['POST', '/api/auth/login'],
    ['GET', '/api/vehicles'],
    ['GET', '/api/diagnostics'],
];

foreach ($requests as [$method, $path]) {
    $url = rtrim($baseUrl, '/') . $path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($method === 'POST') {
        $body = json_encode(['login' => 'test', 'password' => 'test']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $data = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $effective = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $redirectCount = curl_getinfo($ch, CURLINFO_REDIRECT_COUNT);
    echo sprintf("%-6s %-18s %-4s %s %s\n", $method, $path, $status, '->', $effective);
    if ($redirectCount > 0) {
        echo "  redirects: $redirectCount\n";
    }
    curl_close($ch);
}
