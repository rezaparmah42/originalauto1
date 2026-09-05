<?php
$log = __DIR__ . '/_live_mysql_check.log';
$lines = ['START'];
$lines[] = 'PHP=' . PHP_VERSION;

$mysqlOk = false;
$pdoOk = false;

$mysqlCmd = '"C:\\xampp\\mysql\\bin\\mysql.exe" -uroot -e "SHOW DATABASES LIKE \'original_east\'; SELECT \'mysql_client_ok\' AS status;"';
$lines[] = 'MYSQL_CMD=' . $mysqlCmd;
$mysqlOut = [];
$mysqlCode = 0;
@exec($mysqlCmd . ' 2>&1', $mysqlOut, $mysqlCode);
$lines[] = 'MYSQL_EXIT=' . $mysqlCode;
$lines[] = 'MYSQL_OUTPUT=' . implode("\n", $mysqlOut);
if ($mysqlCode === 0) { $mysqlOk = true; }

try {
    $pdo = new PDO('mysql:host=localhost;dbname=original_east;charset=utf8mb4', 'root', '');
    $dbname = $pdo->query('SELECT DATABASE() AS dbname')->fetchColumn();
    $pdoOk = true;
    $lines[] = 'PDO_OK';
    $lines[] = 'DBNAME=' . $dbname;
} catch (Throwable $e) {
    $lines[] = 'PDO_FAIL';
    $lines[] = 'PDO_MESSAGE=' . $e->getMessage();
}

$lines[] = 'MYSQL_OK=' . ($mysqlOk ? 'yes' : 'no');
$lines[] = 'PDO_OK=' . ($pdoOk ? 'yes' : 'no');
$lines[] = 'END';
file_put_contents($log, implode("\n", $lines) . "\n");

echo implode("\n", $lines) . "\n";
exit($mysqlOk && $pdoOk ? 0 : 1);
