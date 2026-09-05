<?php
// PATCH_48 runner: runs the project's verification scripts and a PHP linter, writes patch_48_report.json
$cwd = __DIR__;
$report = ['generated_at' => date('c'), 'php_binary' => PHP_BINARY, 'results' => []];
$verifyScripts = [
    'verify_customer_smart_garage.php',
    'verify_customer_garage.php',
    'verify_workshop.php',
    'verify_milestone.php',
    'verify_workshop_vehicle_history.php'
];
foreach ($verifyScripts as $script) {
    $path = $cwd . DIRECTORY_SEPARATOR . $script;
    if (!file_exists($path)) {
        $report['results'][$script] = ['exists' => false];
        continue;
    }
    $cmd = escapeshellcmd((PHP_BINARY ?: 'php')) . ' -d display_errors=1 -d display_startup_errors=1 -d log_errors=0 -d error_reporting=E_ALL ' . escapeshellarg($path);
    $out = [];
    $rc = null;
    exec($cmd, $out, $rc);
    $text = implode("\n", $out);
    // Attempt to capture any well-known result file for the script
    $resultFileMap = [
        'verify_customer_smart_garage.php' => 'garage_verify_result.txt',
        'verify_customer_garage.php' => 'garage_verify_result.txt',
        'verify_workshop.php' => 'workshop_verify_result.txt',
        'verify_milestone.php' => 'milestone_verify_result.txt',
        'verify_workshop_vehicle_history.php' => 'workshop_vehicle_history_result.txt',
    ];
    $resultFile = $resultFileMap[$script] ?? null;
    $fileContents = null;
    if ($resultFile && file_exists($cwd . DIRECTORY_SEPARATOR . $resultFile)) {
        $fileContents = file_get_contents($cwd . DIRECTORY_SEPARATOR . $resultFile);
    }
    $report['results'][$script] = [
        'exists' => true,
        'command' => $cmd,
        'exit_code' => $rc,
        'stdout' => $text,
        'result_file' => $resultFile,
        'result_file_contents' => $fileContents,
    ];
}
// PHP lint across app/Controllers, app/Models, app/Core and root verify scripts
$filesToLint = [];
$dirs = [__DIR__ . '/app/Controllers', __DIR__ . '/app/Models', __DIR__ . '/app/Core'];
foreach ($dirs as $d) {
    if (!is_dir($d)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));
    foreach ($it as $f) {
        if (!$f->isFile()) continue;
        if (!preg_match('/\.php$/i', $f->getFilename())) continue;
        $filesToLint[] = $f->getPathname();
    }
}
// also include core verify files
foreach (glob(__DIR__ . '/*.php') as $p) {
    if (basename($p) === basename(__FILE__)) continue;
    if (preg_match('/verify_.*\.php$/i', basename($p))) $filesToLint[] = $p;
}
$lints = [];
foreach ($filesToLint as $f) {
    $cmd = escapeshellcmd((PHP_BINARY ?: 'php')) . ' -l ' . escapeshellarg($f);
    $out = [];
    $rc = null;
    exec($cmd, $out, $rc);
    $lints[] = ['file' => substr($f, strlen($cwd)+1), 'exit_code' => $rc, 'output' => $out];
}
$report['lint'] = $lints;
file_put_contents($cwd . '/patch_48_report.json', json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo "PATCH_48_RUNNER_DONE\n";
