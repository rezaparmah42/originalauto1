<?php
$log = __DIR__ . '/_db_probe_result.txt';
$lines = [];
$lines[] = 'START';

$mysqlExe = 'C:\\xampp\\mysql\\bin\\mysql.exe';
$cmd = '"' . $mysqlExe . '" -uroot -e "SHOW DATABASES LIKE \'original_east\'; SELECT \'mysql_client_ok\' AS status;"';
$lines[] = 'MYSQL_CMD=' . $cmd;
$mysqlOut = [];
exec($cmd . ' 2>&1', $mysqlOut, $mysqlCode);
$lines[] = 'MYSQL_EXIT=' . $mysqlCode;
$lines[] = 'MYSQL_OUTPUT=' . implode("\n", $mysqlOut);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=original_east;charset=utf8mb4', 'root', '');
    $dbname = $pdo->query('SELECT DATABASE() AS dbname')->fetchColumn();
    $lines[] = 'PDO_OK';
    $lines[] = 'DB=' . $dbname;
} catch (Throwable $e) {
    $lines[] = 'PDO_FAIL';
    $lines[] = $e->getMessage();
    file_put_contents($log, implode("\n", $lines) . "\n");
    exit(1);
}

$lines[] = 'END';
file_put_contents($log, implode("\n", $lines) . "\n");

echo implode("\n", $lines) . "\n";
