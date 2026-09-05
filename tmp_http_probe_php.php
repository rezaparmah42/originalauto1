<?php
$urls = [
    'http://127.0.0.1:8000/',
    'http://127.0.0.1:8000/articles',
    'http://127.0.0.1:8000/services',
    'http://127.0.0.1:8000/products',
    'http://127.0.0.1:8000/login',
    'http://127.0.0.1:8000/account/dashboard',
    'http://127.0.0.1:8000/admin/login',
    'http://127.0.0.1:8000/notifications',
];
foreach ($urls as $url) {
    $opts = [
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 10,
        ],
    ];
    $ctx = stream_context_create($opts);
    $body = @file_get_contents($url, false, $ctx);
    if (!$body && empty($http_response_header)) {
        echo "$url => ERROR\n";
        continue;
    }
    $code = 0;
    foreach ($http_response_header as $line) {
        if (preg_match('/^HTTP\\/\\d+\\.\\d+\\s+(\\d+)/', $line, $m)) {
            $code = (int) $m[1];
            break;
        }
    }
    echo "$url => $code\n";
}
