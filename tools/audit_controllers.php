<?php
$dir = __DIR__ . '/../app/Controllers';
$out = [];
$out[] = "controller,file,public_methods,requireLogin,requireAdmin,csrf_keywords,ownership_keywords";
$files = glob($dir . '/*.php');
foreach ($files as $f) {
    $txt = file_get_contents($f);
    preg_match('/namespace\s+([\\\w]+);/s', $txt, $mns);
    $ns = isset($mns[1]) ? $mns[1] . '\\' : '';
    preg_match('/class\s+(\w+)/', $txt, $mc);
    $class = isset($mc[1]) ? $mc[1] : basename($f);
    preg_match_all('/public\s+function\s+(\w+)\s*\(/', $txt, $mm);
    $methods = isset($mm[1]) ? implode('|', $mm[1]) : '';
    $requireLogin = (strpos($txt, 'requireLogin') !== false) ? 'yes' : 'no';
    $requireAdmin = (strpos($txt, 'requireAdmin') !== false) ? 'yes' : 'no';
    $csrf = (strpos($txt, 'csrf') !== false || strpos($txt, 'CSRF') !== false) ? 'yes' : 'no';
    $ownership = (strpos($txt, 'owner') !== false || strpos($txt, 'ownership') !== false || strpos($txt, 'requireOwner') !== false) ? 'yes' : 'no';
    $out[] = sprintf('%s,%s,%s,%s,%s,%s,%s', $ns . $class, basename($f), $methods, $requireLogin, $requireAdmin, $csrf, $ownership);
}
file_put_contents(__DIR__ . '/../phase1_controllers_audit.csv', implode("\n", $out));
echo "WROTE: ../phase1_controllers_audit.csv\n";
