<?php
// Generates pre-rendered model content files under app/Data/generated_models
$root = dirname(__DIR__);
require_once $root . '/app/Helpers/ContentGenerator.php';
$bank2 = include $root . '/app/Data/content_bank2.php';
foreach ($bank2 as $brandSlug => $brand) {
    $models = $brand['models'] ?? [];
    foreach ($models as $modelSlug => $model) {
        try {
            $out = \App\Helpers\ContentGenerator::generateModelContent($brandSlug, $modelSlug, 1500);
            $dir = $root . '/app/Data/generated_models/' . $brandSlug;
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file = $dir . '/' . $modelSlug . '.php';
            $content = "<?php\n// Generated content for $brandSlug/$modelSlug\nreturn [\n    'title' => " . var_export($out['title'] ?? '', true) . ",\n    'html' => " . var_export($out['html'] ?? '', true) . ",\n    'faqSchema' => " . var_export($out['faqSchema'] ?? [], true) . ",\n];\n";
            file_put_contents($file, $content);
            echo "Wrote: " . $file . PHP_EOL;
        } catch (\Throwable $e) {
            echo "Failed to generate for $brandSlug/$modelSlug: " . $e->getMessage() . PHP_EOL;
        }
    }
}
