<?php
$root = realpath(__DIR__ . '/..');
$outFile = $root . '/phase2_php_lint.txt';
$php = 'C:\\xampp\\php\\php.exe';
$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
        $files[] = $file->getPathname();
    }
}
sort($files, SORT_STRING);
$lines = [];
$lines[] = 'PHASE2_PHP_LINT - ' . date('c');
$lines[] = 'PHP_FILES_TOTAL=' . count($files);
$failCount = 0;
foreach ($files as $file) {
    $cmd = escapeshellarg($php) . ' -l ' . escapeshellarg($file);
    $output = [];
    exec($cmd, $output, $code);
    $text = implode(PHP_EOL, $output);
    if ($code !== 0 || strpos($text, 'No syntax errors detected') === false) {
        $failCount++;
        $lines[] = '';
        $lines[] = 'LINT_FAIL: ' . $file;
        $lines[] = $text;
    }
}
$lines[] = '';
$lines[] = 'PHP_LINT_ERRORS=' . $failCount;
file_put_contents($outFile, implode(PHP_EOL, $lines));
echo "WROTE:$outFile\n";
