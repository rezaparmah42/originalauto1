<?php
$files = [
    __DIR__ . '/app/Controllers/AccountController.php',
    __DIR__ . '/app/Views/account/garage.php',
    __DIR__ . '/app/routes.php',
];
$report = ['generated_at' => date('c'), 'files' => []];
foreach ($files as $f) {
    $cmd = escapeshellcmd((PHP_BINARY ?: 'php')) . ' -l ' . escapeshellarg($f);
    $out = [];
    $rc = null;
    exec($cmd, $out, $rc);
    $report['files'][] = ['file' => substr($f, strlen(__DIR__)+1), 'exit_code' => $rc, 'output' => $out];
}
file_put_contents(__DIR__ . '/patch_51_lint_results.json', json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo "DONE\n";
