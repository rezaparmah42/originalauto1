<?php
$src = __DIR__ . '/../app/routes.php';
if (!file_exists($src)) { echo "MISSING: $src\n"; exit(1); }
$txt = file_get_contents($src);
$lines = preg_split('/\r\n|\n|\r/', $txt);
$out = [];
$out[] = "method,uri,handler";
foreach ($lines as $line) {
    if (preg_match('/\\$router->(get|post)\\s*\\(\\s*([\'\"])(.*?)\\2\\s*,\\s*(.*)/', $line, $m)) {
        $method = $m[1];
        $uri = $m[3];
        $handler = preg_replace('/\\s*[,;]\\s*$/', '', $m[4]);
        $handler = trim($handler);
        // sanitize commas inside handler
        $handler = str_replace(',', '|', $handler);
        $out[] = $method . "," . $uri . "," . $handler;
    }
}
file_put_contents(__DIR__ . '/../phase1_routes.csv', implode("\n", $out));
echo "WROTE: ../phase1_routes.csv\n";
