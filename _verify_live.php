<?php
$log = __DIR__ . '/_verify_live.log';
$lines = ['START'];

$mysqlExe = 'C:\\xampp\\mysql\\bin\\mysql.exe';
$phpExe = 'C:\\xampp\\php\\php.exe';
$verify = __DIR__ . '\\verify_customer_smart_garage.php';

$mysqlCmd = '"' . $mysqlExe . '" -uroot -e "SHOW DATABASES LIKE \'original_east\'; SELECT \'mysql_client_ok\' AS status;"';
$lines[] = 'MYSQL_CMD=' . $mysqlCmd;
$mysqlOut = [];
$mysqlCode = 0;
@exec($mysqlCmd . ' 2>&1', $mysqlOut, $mysqlCode);
$lines[] = 'MYSQL_EXIT=' . $mysqlCode;
$lines[] = 'MYSQL_OUTPUT=' . implode("\n", $mysqlOut);

try {
    $pdo = new PDO('mysql:host=localhost;dbname=original_east;charset=utf8mb4', 'root', '');
    $dbname = $pdo->query('SELECT DATABASE() AS dbname')->fetchColumn();
    $lines[] = 'PDO_OK';
    $lines[] = 'DBNAME=' . $dbname;
} catch (Throwable $e) {
    $lines[] = 'PDO_FAIL';
    $lines[] = 'PDO_MESSAGE=' . $e->getMessage();
    $lines[] = 'END';
    file_put_contents($log, implode("\n", $lines) . "\n");
    exit(1);
}

$lines[] = 'VERIFY_CMD=' . $phpExe . ' -d display_errors=1 ' . $verify;
$verifyOut = [];
$verifyCode = 0;
@exec('"' . $phpExe . '" -d display_errors=1 "' . $verify . '" 2>&1', $verifyOut, $verifyCode);
$lines[] = 'VERIFY_EXIT=' . $verifyCode;
$lines[] = 'VERIFY_OUTPUT=' . implode("\n", $verifyOut);
$lines[] = 'END';
file_put_contents($log, implode("\n", $lines) . "\n");

echo 'LOG=' . $log . "\n";
exit($verifyCode === 0 ? 0 : 1);
