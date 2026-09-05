<?php
$paths = [
    '/',
    '/services',
    '/services/diagnostic',
    '/articles',
    '/articles/test-slug',
    '/shop',
    '/products',
    '/products/test-slug',
    '/login',
    '/logout',
    '/account/dashboard',
    '/account/profile',
    '/notifications',
    '/repair/chat/1',
    '/technician/dashboard',
    '/technician/tasks',
    '/admin/login',
    '/admin/dashboard',
    '/admin/articles',
    '/admin/products',
    '/admin/services',
    '/admin/bookings',
    '/admin/repairs',
    '/admin/invoices',
];

$base = 'http://127.0.0.1:8000';

foreach ($paths as $path) {
    $url = $base . $path;
    $opts = [
        'http' => [
            'method' => 'GET',
            'ignore_errors' => true,
            'timeout' => 10,
            'header' => "User-Agent: OriginalSharghProbe/1.0\r\n",
        ],
    ];
    $ctx = stream_context_create($opts);
    $body = @file_get_contents($url, false, $ctx);
    $status = 0;
    if (!empty($http_response_header)) {
        foreach ($http_response_header as $header) {
            if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/', $header, $m)) {
                $status = (int)$m[1];
                break;
            }
        }
    }
    echo sprintf("%s %s => %s\n", date('H:i:s'), $path, $status);
    if ($status !== 200 && $status !== 301 && $status !== 302) {
        echo "  ERROR RESPONSE HEADERS:\n";
        foreach ($http_response_header as $header) {
            echo "    $header\n";
        }
        echo "  BODY SNIPPET: " . substr($body ?: '', 0, 200) . "\n";
    }
}
