<?php
$base = __DIR__;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
$out = [];
foreach ($it as $f) {
    if (!$f->isFile()) continue;
    $path = $f->getPathname();
    if (!preg_match('/\\.php$/i', $path)) continue;
    // skip vendor/cache if any
    if (strpos($path, DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR) !== false) continue;
    $cmd = escapeshellcmd((PHP_BINARY ?: 'php') . ' -l ' . escapeshellarg($path));
    $res = null;
    @exec($cmd, $res, $rc);
    $out[] = ['file' => substr($path, strlen($base)+1), 'rc' => $rc, 'output' => $res];
}
file_put_contents($base . '/_php_lint_results.json', json_encode($out, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo "LINT_DONE\n";