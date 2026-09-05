<?php
$urls = [
    'http://localhost/vehicles',
    'http://localhost/vehicles/japanese',
    'http://localhost/shop',
    'http://localhost/booking',
    'http://localhost/services',
    'http://localhost/services/diagnostic',
    'http://localhost/admin',
];
$out = [];
foreach ($urls as $u) {
    $headers = @get_headers($u);
    $status = $headers ? $headers[0] : 'ERROR';
    $out[] = "$u -> $status";
}
$out[] = "\nApache error.log tail (last 120 lines):";
$log = @file('C:\\xampp\\apache\\logs\\error.log');
if ($log) {
    $tail = array_slice($log, -120);
    foreach ($tail as $line) { $out[] = rtrim($line); }
} else {
    $out[] = 'Apache error.log not found';
}
file_put_contents(__DIR__ . '/tmp_http_results.txt', implode(PHP_EOL, $out));
echo "WROTE_RESULTS\n";
