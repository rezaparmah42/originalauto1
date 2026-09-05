<?php
$paths = ['/', '/services', '/services/diagnostic', '/articles', '/articles/test', '/shop', '/products', '/products/test', '/login', '/account/dashboard', '/admin/login', '/notifications'];
$base = 'http://127.0.0.1:8000';
foreach ($paths as $path) {
    $url = $base . $path;
    $ctx = stream_context_create(['http' => ['method' => 'GET', 'ignore_errors' => true, 'timeout' => 10]]);
    $body = @file_get_contents($url, false, $ctx);
    $meta = $http_response_header ?? [];
    $code = 0;
    if ($meta) {
        foreach ($meta as $line) {
            if (preg_match('/^HTTP\//', $line)) {
                $code = (int) trim(str_replace('HTTP/1.1 ', '', $line));
            }
        }
    }
    echo $path . ' => ' . $code . PHP_EOL;
}
