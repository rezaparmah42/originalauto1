<?php
// Simple sitemap generator for services and vehicles from content banks
$root = dirname(__DIR__);
$site = '';
if (defined('SITE_URL')) {
    $site = rtrim(SITE_URL, '/');
} elseif (getenv('SITE_URL')) {
    $site = rtrim(getenv('SITE_URL'), '/');
} else {
    // fallback - please set SITE_URL in your environment or app config
    $site = 'https://example.com';
}
$bank1 = include $root . '/app/Data/content_bank1.php';
$bank2 = include $root . '/app/Data/content_bank2.php';
$urls = [];
$now = date('Y-m-d');
// Services from bank1
foreach ($bank1 as $serviceSlug => $svc) {
    $urls[] = ['loc' => '/services/' . rawurlencode($serviceSlug), 'lastmod' => $now];
    // include vehicle targets
    if (!empty($svc['vehicle_targets'])) {
        foreach ($svc['vehicle_targets'] as $vt) {
            $urls[] = ['loc' => '/services/' . rawurlencode($serviceSlug) . '/' . rawurlencode($vt['slug']), 'lastmod' => $now];
        }
    }
}
// Vehicles from bank2
foreach ($bank2 as $brandSlug => $brand) {
    foreach (($brand['models'] ?? []) as $modelSlug => $model) {
        $urls[] = ['loc' => '/vehicles/' . rawurlencode($brandSlug) . '/' . rawurlencode($modelSlug), 'lastmod' => $now];
        // include priority service links
        foreach (($model['priority_services'] ?? []) as $ps) {
            $urls[] = ['loc' => '/services/' . rawurlencode($ps['slug']) . '/' . rawurlencode($modelSlug), 'lastmod' => $now];
        }
    }
}
// Unique
$unique = [];
foreach ($urls as $u) $unique[$u['loc']] = $u['lastmod'];
$xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
foreach ($unique as $loc => $lm) {
    $url = $xml->addChild('url');
    $url->addChild('loc', htmlspecialchars($site . $loc));
    $url->addChild('lastmod', $lm);
}
$outPath = $root . '/public/sitemap-services-vehicles.xml';
file_put_contents($outPath, $xml->asXML());
echo "Wrote sitemap to: " . $outPath . PHP_EOL;
