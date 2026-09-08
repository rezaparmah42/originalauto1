<?php
// Read-only config check and DB counts
require __DIR__ . '/../config/config.php';

echo "APP_ENV=" . (defined('APP_ENV') ? APP_ENV : 'undef') . PHP_EOL;
echo "SITE_URL=" . (defined('SITE_URL') ? SITE_URL : 'undef') . PHP_EOL;
echo "DB_HOST=" . (defined('DB_HOST') ? DB_HOST : 'undef') . PHP_EOL;
echo "DB_NAME=" . (defined('DB_NAME') ? DB_NAME : 'undef') . PHP_EOL;
echo "DB_USER=" . (defined('DB_USER') ? DB_USER : 'undef') . PHP_EOL;
echo "DB_PASS=" . ((defined('DB_PASS') && DB_PASS !== '') ? 'set' : 'empty') . PHP_EOL;

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $tables = ['services','articles','products','users','vehicles','vehicle_brands'];
    foreach ($tables as $t) {
        $stmt = $pdo->query("SELECT COUNT(*) AS c FROM `" . $t . "`");
        $c = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
        echo $t . '=' . $c . PHP_EOL;
    }
} catch (Throwable $e) {
    echo 'CONNECT_ERR: ' . $e->getMessage() . PHP_EOL;
}
