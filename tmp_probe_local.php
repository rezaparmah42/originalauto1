<?php
$base = 'http://localhost/originalshargh';
$paths = [
    '/vehicles',
    '/vehicles/japanese',
    '/shop',
    '/booking',
    '/services',
    '/services/diagnostic',
    '/admin',
];
$out = [];
foreach ($paths as $p) {
    $url = $base . $p;
    $ctx = stream_context_create(['http' => ['method' => 'GET', 'ignore_errors' => true, 'timeout' => 10]]);
    $body = @file_get_contents($url, false, $ctx);
    $meta = $http_response_header ?? [];
    $code = 0;
    foreach ($meta as $line) {
        if (preg_match('/^HTTP\//', $line)) { preg_match('/\s(\d{3})\s/', $line, $m); $code = $m[1] ?? 0; break; }
    }
    $out[] = "$url -> " . ($code ?: 'ERROR');
}
$out[] = "\n--- Apache error.log tail (last 120 lines) ---";
$log = @file('C:\\xampp\\apache\\logs\\error.log');
if ($log) {
    $tail = array_slice($log, -120);
    foreach ($tail as $l) { $out[] = rtrim($l); }
} else {
    $out[] = 'Apache error.log not found';
}
file_put_contents(__DIR__ . '/tmp_probe_local_results.txt', implode(PHP_EOL, $out));
echo "WROTE tmp_probe_local_results.txt\n";
