<?php
// Generate simple SVG placeholder images for services and vehicles based on content banks
$root = dirname(__DIR__);
$bank1 = include $root . '/app/Data/content_bank1.php';
$bank2 = include $root . '/app/Data/content_bank2.php';
$svgTemplate = function($text, $w=1200, $h=675) {
    $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE);
    return "<svg xmlns='http://www.w3.org/2000/svg' width='$w' height='$h' viewBox='0 0 $w $h'><rect width='100%' height='100%' fill='#ddd'/><text x='50%' y='50%' font-family='Arial, Helvetica, sans-serif' font-size='36' fill='#666' dominant-baseline='middle' text-anchor='middle'>$escaped</text></svg>";
};
// services
foreach ($bank1 as $serviceSlug => $svc) {
    $dir = $root . '/public/uploads/services/' . $serviceSlug;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $hero = $dir . '/' . $serviceSlug . '.svg';
    $mid = $dir . '/' . $serviceSlug . '-mid.svg';
    file_put_contents($hero, $svgTemplate(($svc['why'] ?? $serviceSlug) . ' — ' . $serviceSlug, 1200, 675));
    file_put_contents($mid, $svgTemplate('نمای سرویس ' . $serviceSlug, 800, 600));
    echo "Wrote placeholders for service: $serviceSlug\n";
}
// vehicles
foreach ($bank2 as $brandSlug => $brand) {
    $models = $brand['models'] ?? [];
    $dirBrand = $root . '/public/uploads/vehicles/' . $brandSlug;
    if (!is_dir($dirBrand)) mkdir($dirBrand, 0755, true);
    foreach ($models as $modelSlug => $model) {
        $hero = $dirBrand . '/' . $modelSlug . '.svg';
        $mid = $dirBrand . '/' . $modelSlug . '-mid.svg';
        $label = ($brand['name'] ?? $brandSlug) . ' ' . ($model['name'] ?? $modelSlug);
        file_put_contents($hero, $svgTemplate($label, 1200, 675));
        file_put_contents($mid, $svgTemplate('نمای ' . $label, 800, 600));
        echo "Wrote placeholders for vehicle: $brandSlug/$modelSlug\n";
    }
}
echo "Placeholders generated under public/uploads/services and public/uploads/vehicles\n";
