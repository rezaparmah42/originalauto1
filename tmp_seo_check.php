<?php
$urls = ['http://localhost/originalshargh/', 'http://localhost/originalshargh/sitemap.xml'];
foreach($urls as $u){
    echo "URL: $u\n";
    $c = @file_get_contents($u);
    if ($c === false) { echo "FETCH_FAIL\n\n"; continue; }
    if (preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $c, $m)) {
        $json = trim($m[1]);
        json_decode($json);
        echo 'JSONLD: ' . (json_last_error() === 0 ? 'OK' : 'ERR: ' . json_last_error_msg()) . "\n";
    } else {
        echo "JSONLD: NONE\n";
    }
    if (strpos($c, '<urlset') !== false) { echo "SITEMAP: FOUND\n"; }
    echo "\n";
}
