<?php
require_once __DIR__ . '/../config/config.php';
$paths = ['/account/dashboard', '/admin/dashboard', '/checkout', '/diagnostic'];
$baseUrl = getenv('APP_URL') ?: 'http://localhost/originalshargh';
foreach ($paths as $path) {
    $url = rtrim($baseUrl, '/') . $path;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $data = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    echo "PATH: $path\n";
    echo "CODE: " . ($info['http_code'] ?? 'N/A') . "\n";
    echo "EFFECTIVE: " . ($info['url'] ?? 'N/A') . "\n";
    echo "HEADERSIZE: " . ($info['header_size'] ?? 'N/A') . "\n";
    echo "ERROR: " . curl_error($ch) . "\n";
    echo "BODY LEN: " . strlen($data) . "\n";
    echo str_repeat('-', 40) . "\n";
}
