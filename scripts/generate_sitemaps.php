<?php
/**
 * Generate sitemaps for OriginalShargh
 * Writes sitemap-pages.xml and (when DB available) sitemap-services.xml, sitemap-articles.xml, sitemap-products.xml, sitemap-vehicles.xml
 * Also writes sitemap_index.xml referencing them.
 * Run: php scripts/generate_sitemaps.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/functions/functions.php';

$publicRoot = __DIR__ . '/..';
$sitemapDir = $publicRoot;

$now = date('c');

// Static pages
$pages = [
    ['loc' => rtrim(SITE_URL, '/') . '/', 'changefreq' => 'daily', 'priority' => '1.0'],
    ['loc' => rtrim(SITE_URL, '/') . '/services', 'changefreq' => 'weekly', 'priority' => '0.8'],
    ['loc' => rtrim(SITE_URL, '/') . '/articles', 'changefreq' => 'daily', 'priority' => '0.7'],
    ['loc' => rtrim(SITE_URL, '/') . '/shop', 'changefreq' => 'daily', 'priority' => '0.7'],
    ['loc' => rtrim(SITE_URL, '/') . '/booking', 'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => rtrim(SITE_URL, '/') . '/about', 'changefreq' => 'yearly', 'priority' => '0.4'],
    ['loc' => rtrim(SITE_URL, '/') . '/contact', 'changefreq' => 'yearly', 'priority' => '0.4'],
    ['loc' => rtrim(SITE_URL, '/') . '/vehicles', 'changefreq' => 'weekly', 'priority' => '0.6'],
];

// helper to write a sitemap file
function write_sitemap($filePath, $urls)
{
    $fp = fopen($filePath, 'w');
    if (!$fp) {
        echo "Cannot write $filePath\n";
        return false;
    }

    fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fwrite($fp, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n");
    foreach ($urls as $u) {
        fwrite($fp, "  <url>\n");
        fwrite($fp, "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n");
        if (!empty($u['lastmod'])) fwrite($fp, "    <lastmod>" . $u['lastmod'] . "</lastmod>\n");
        if (!empty($u['changefreq'])) fwrite($fp, "    <changefreq>" . $u['changefreq'] . "</changefreq>\n");
        if (!empty($u['priority'])) fwrite($fp, "    <priority>" . $u['priority'] . "</priority>\n");
        fwrite($fp, "  </url>\n");
    }
    fwrite($fp, "</urlset>\n");
    fclose($fp);
    echo "Wrote: $filePath\n";
    return true;
}

// Write static pages sitemap
$pagesPaths = array_map(function ($p) use ($now) { $p['lastmod'] = $now; return $p; }, $pages);
write_sitemap($sitemapDir . '/sitemap-pages.xml', $pagesPaths);

// Attempt to build dynamic sitemaps from DB (services, articles, products, vehicles)
$dynamicSitemaps = [];
try {
    if (class_exists('\App\Core\Database')) {
        require_once __DIR__ . '/../app/Core/Database.php';
        $db = \App\Core\Database::connect();

        // Articles
        try {
            $stmt = $db->query("SELECT slug, updated_at, image FROM articles WHERE status = 1 ORDER BY updated_at DESC LIMIT 50000");
            $urls = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $loc = rtrim(SITE_URL, '/') . '/articles/' . rawurlencode($row['slug']);
                $urls[] = ['loc' => $loc, 'lastmod' => !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : $now, 'changefreq' => 'weekly', 'priority' => '0.6'];
            }
            if (!empty($urls)) { write_sitemap($sitemapDir . '/sitemap-articles.xml', $urls); $dynamicSitemaps[] = 'sitemap-articles.xml'; }
        } catch (Exception $e) { echo "Articles sitemap generation skipped: " . $e->getMessage() . "\n"; }

        // Services
        try {
            $stmt = $db->query("SELECT slug, updated_at FROM services WHERE status = 1 ORDER BY updated_at DESC LIMIT 50000");
            $urls = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $loc = rtrim(SITE_URL, '/') . '/services/' . rawurlencode($row['slug']);
                $urls[] = ['loc' => $loc, 'lastmod' => !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : $now, 'changefreq' => 'monthly', 'priority' => '0.7'];
            }
            if (!empty($urls)) { write_sitemap($sitemapDir . '/sitemap-services.xml', $urls); $dynamicSitemaps[] = 'sitemap-services.xml'; }
        } catch (Exception $e) { echo "Services sitemap generation skipped: " . $e->getMessage() . "\n"; }

        // Products
        try {
            $stmt = $db->query("SELECT slug, updated_at FROM products WHERE status = 1 ORDER BY updated_at DESC LIMIT 50000");
            $urls = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $loc = rtrim(SITE_URL, '/') . '/products/' . rawurlencode($row['slug']);
                $urls[] = ['loc' => $loc, 'lastmod' => !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : $now, 'changefreq' => 'weekly', 'priority' => '0.6'];
            }
            if (!empty($urls)) { write_sitemap($sitemapDir . '/sitemap-products.xml', $urls); $dynamicSitemaps[] = 'sitemap-products.xml'; }
        } catch (Exception $e) { echo "Products sitemap generation skipped: " . $e->getMessage() . "\n"; }

        // Vehicles (vehicle models)
        try {
            $stmt = $db->query("SELECT slug, updated_at FROM vehicle_models ORDER BY updated_at DESC LIMIT 50000");
            $urls = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $loc = rtrim(SITE_URL, '/') . '/vehicles/' . rawurlencode($row['slug']);
                $urls[] = ['loc' => $loc, 'lastmod' => !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : $now, 'changefreq' => 'weekly', 'priority' => '0.5'];
            }
            if (!empty($urls)) { write_sitemap($sitemapDir . '/sitemap-vehicles.xml', $urls); $dynamicSitemaps[] = 'sitemap-vehicles.xml'; }
        } catch (Exception $e) { echo "Vehicles sitemap generation skipped: " . $e->getMessage() . "\n"; }

    } else {
        echo "Database class not found; dynamic sitemaps skipped.\n";
    }
} catch (Exception $e) {
    echo "Error during dynamic sitemap generation: " . $e->getMessage() . "\n";
}

// Create sitemap index
$indexFile = $sitemapDir . '/sitemap_index.xml';
$fp = fopen($indexFile, 'w');
if ($fp) {
    fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fwrite($fp, "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n");
    $staticList = ['sitemap-pages.xml'];
    $all = array_merge($staticList, $dynamicSitemaps);
    foreach ($all as $s) {
        fwrite($fp, "  <sitemap>\n");
        fwrite($fp, "    <loc>" . rtrim(SITE_URL, '/') . '/' . $s . "</loc>\n");
        fwrite($fp, "    <lastmod>" . date('c') . "</lastmod>\n");
        fwrite($fp, "  </sitemap>\n");
    }
    fwrite($fp, "</sitemapindex>\n");
    fclose($fp);
    echo "Wrote: $indexFile\n";
} else {
    echo "Cannot write sitemap index: $indexFile\n";
}

echo "Sitemap generation complete.\n";
