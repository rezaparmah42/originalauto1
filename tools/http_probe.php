<?php
// tools/http_probe.php
// Run HTTP probes for common routes and collect new Apache errors since start
$base = 'http://localhost/originalshargh';
$routes = [
    '/',
    '/admin/login',
    '/admin/dashboard',
    '/vehicles',
    '/brands',
    // '/models' removed from probe list: not used by UI and returns 404 in this install
    '/services',
    '/articles',
    '/shop',
    '/products',
    '/booking',
    '/cart',
    '/checkout',
    '/account',
    '/diagnostic',
];
$results = [];
$start = time();
$start_iso = date('c', $start);
foreach ($routes as $r) {
    $url = rtrim($base, '/') . $r;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $body = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $final = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $len = $body === false ? 0 : strlen($body);
    $bodyText = $body === false ? '' : (string) $body;
    $hasFatal = preg_match('/Fatal error|Uncaught Exception|Uncaught PDOException/i', $bodyText) === 1;
    $hasWarning = preg_match('/Warning: /i', $bodyText) === 1;
    $hasSqlState = preg_match('/SQLSTATE\[[A-Z0-9_]+\]/i', $bodyText) === 1;
    $hasUnknownColumn = preg_match('/Unknown column/i', $bodyText) === 1;
    $has404Text = preg_match('/\b404\b|Not Found|صفحه مورد نظر یافت نشد/i', $bodyText) === 1;
    curl_close($ch);
    $results[] = [
        'route' => $r,
        'url' => $url,
        'http_code' => $code,
        'final_url' => $final,
        'body_length' => $len,
        'curl_errno' => $errno,
        'curl_error' => $error,
        'has_fatal' => $hasFatal,
        'has_warning' => $hasWarning,
        'has_sqlstate' => $hasSqlState,
        'has_unknown_column' => $hasUnknownColumn,
        'has_404_text' => $has404Text,
    ];
}
// Read apache error tail file and return entries newer than $start
$apache_log = __DIR__ . '/apache_error_tail.txt';
$new_errors = [];
if (file_exists($apache_log)) {
    $lines = file($apache_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // match leading timestamp in brackets: [Mon Jul 27 10:36:45.535074 2026]
        if (preg_match('/^\[(\w{3} \w{3} \d{1,2} \d{2}:\d{2}:\d{2})(?:\.\d+)? (\d{4})\]/', $line, $m)) {
            $datestr = $m[1] . ' ' . $m[2];
            $ts = strtotime($datestr);
            if ($ts !== false && $ts >= $start) {
                $new_errors[] = $line;
            }
        }
    }
}
$out = [
    'start' => $start_iso,
    'base' => $base,
    'probes' => $results,
    'new_apache_errors' => $new_errors,
];
file_put_contents(__DIR__ . '/http_probe_results.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "Probe complete. Results written to tools/http_probe_results.json\n";
?>