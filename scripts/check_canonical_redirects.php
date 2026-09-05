<?php
/**
 * Scan views for explicit canonical assignments and redirects (header Location)
 * Run: php scripts/check_canonical_redirects.php
 */
require_once __DIR__ . '/../config/config.php';
$base = __DIR__ . '/../app/Views';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
$files = [];
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    if (preg_match('#\\.(php|phtml)$#i', $path)) {
        $files[] = $path;
    }
}

$results = ['canonical' => [], 'location' => []];
foreach ($files as $f) {
    $content = file_get_contents($f);
    if ($content === false) continue;
    if (preg_match_all('/\$canonical\s*=\s*(.+);/U', $content, $m)) {
        foreach ($m[1] as $expr) {
            $results['canonical'][] = ['file' => $f, 'expr' => trim($expr)];
        }
    }
    if (preg_match_all('/header\s*\(\s*["\']Location:\s*([^"\']+)["\']/i', $content, $m2)) {
        foreach ($m2[1] as $loc) {
            $results['location'][] = ['file' => $f, 'target' => trim($loc)];
        }
    }
}

echo "Canonical assignments found:\n";
foreach ($results['canonical'] as $c) {
    echo " - {$c['file']}: {$c['expr']}\n";
}

echo "\nLocation headers found:\n";
foreach ($results['location'] as $l) {
    echo " - {$l['file']}: {$l['target']}\n";
}

echo "\nReview files listed above for inconsistent canonical values or external redirects.\n";
