<?php
// Generates full matrix files for every service×model using ContentGenerator
$root = dirname(__DIR__);
require_once $root . '/app/Helpers/ContentGenerator.php';
$bank1 = include $root . '/app/Data/content_bank1.php';
$bank2 = include $root . '/app/Data/content_bank2.php';
foreach ($bank1 as $serviceSlug => $svc) {
    foreach ($bank2 as $brandSlug => $brand) {
        foreach (($brand['models'] ?? []) as $modelSlug => $model) {
            $out = \App\Helpers\ContentGenerator::generateMatrixContent($serviceSlug, $brandSlug, $modelSlug, 1200);
            $dir = $root . '/app/Data/generated_matrices/' . $serviceSlug . '/' . $brandSlug;
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file = $dir . '/' . $modelSlug . '.php';
            $content = "<?php\n// Generated matrix content for $serviceSlug / $brandSlug / $modelSlug\nreturn [\n    'title' => " . var_export($out['title'] ?? '', true) . ",\n    'html' => " . var_export($out['html'] ?? '', true) . ",\n];\n";
            file_put_contents($file, $content);
            echo "Wrote: " . $file . PHP_EOL;
        }
    }
}
