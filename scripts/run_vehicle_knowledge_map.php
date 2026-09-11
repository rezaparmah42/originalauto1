<?php
// Repository-only mapping tool for vehicle knowledge drafts
// Usage:
//  php scripts/run_vehicle_knowledge_map.php [--mapping=path/to/mapping.json] [--apply]

$cwd = realpath(__DIR__ . '/../');
chdir($cwd);

$opts = getopt('', ['mapping::', 'apply', 'help']);
if (isset($opts['help'])) {
    echo "Usage: php scripts/run_vehicle_knowledge_map.php [--mapping=path] [--apply]\n";
    exit(0);
}

$mappingPath = isset($opts['mapping']) ? $opts['mapping'] : 'storage/vehicle_model_mapping.json';
$apply = isset($opts['apply']);

// Find draft JSON files
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('storage/vehicle_knowledge_drafts'));
$files = [];
foreach ($rii as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'json') {
        $files[] = $file->getPathname();
    }
}

// Stats
$total = count($files);
$mapped = 0;
$unresolved = 0;
$missing_fields = 0;
$slugs = [];
$duplicate_slugs = [];
$seedStatements = [];

$mapping = null;
if (file_exists($mappingPath)) {
    $mapping = json_decode(file_get_contents($mappingPath), true);
    if (!is_array($mapping)) {
        echo "Mapping file exists but is not valid JSON: $mappingPath\n";
        $mapping = null;
    }
}

$required = ['brand','model','service_category','slug','source_hash'];

foreach ($files as $f) {
    $data = json_decode(file_get_contents($f), true);
    if (!is_array($data)) continue;
    $slug = isset($data['slug']) ? $data['slug'] : null;
    if ($slug) {
        if (isset($slugs[$slug])) {
            $duplicate_slugs[$slug] = ($duplicate_slugs[$slug] ?? 1) + 1;
        } else {
            $slugs[$slug] = $f;
        }
    }

    $hasRequired = true;
    foreach ($required as $r) {
        if (empty($data[$r])) {
            $hasRequired = false;
        }
    }
    if (!$hasRequired) {
        $missing_fields++;
        $unresolved++;
        continue;
    }

    $brand = strtolower($data['brand']);
    $model = strtolower($data['model']);
    $service = strtolower($data['service_category']);

    $found = false;
    if (is_array($mapping) && isset($mapping[$brand]) && isset($mapping[$brand][$model]) && isset($mapping[$brand][$model][$service])) {
        $entry = $mapping[$brand][$model][$service];
        if (!empty($entry['model_id']) || !empty($entry['service_id'])) {
            // apply mapping
            if ($apply) {
                if (!empty($entry['model_id'])) $data['model_id'] = (int)$entry['model_id'];
                if (!empty($entry['service_id'])) $data['service_id'] = (int)$entry['service_id'];
                $data['mapping_status'] = 'mapped';
                file_put_contents($f, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
            }
            // prepare seed SQL insert (repository-only)
            $slugEsc = addslashes($data['slug']);
            $srcHash = addslashes($data['source_hash']);
            $brandEsc = addslashes($brand);
            $modelEsc = addslashes($model);
            $modelId = !empty($entry['model_id']) ? (int)$entry['model_id'] : 'NULL';
            $serviceId = !empty($entry['service_id']) ? (int)$entry['service_id'] : 'NULL';
            $seedStatements[] = "INSERT INTO vehicle_knowledge_contents (slug, status, brand, model, model_id, service_id, source_hash, mapping_status, created_at) VALUES ('{$slugEsc}', 'draft', '{$brandEsc}', '{$modelEsc}', {$modelId}, {$serviceId}, '{$srcHash}', 'mapped', NOW());";
            $mapped++;
            $found = true;
        }
    }
    if (!$found) {
        $unresolved++;
    }
}

// If mapping file missing, generate template with unique combos
if ($mapping === null) {
    $combos = [];
    foreach ($files as $f) {
        $data = json_decode(file_get_contents($f), true);
        if (!is_array($data)) continue;
        if (empty($data['brand']) || empty($data['model']) || empty($data['service_category'])) continue;
        $brand = strtolower($data['brand']);
        $model = strtolower($data['model']);
        $service = strtolower($data['service_category']);
        if (!isset($combos[$brand])) $combos[$brand] = [];
        if (!isset($combos[$brand][$model])) $combos[$brand][$model] = [];
        if (!isset($combos[$brand][$model][$service])) {
            $combos[$brand][$model][$service] = ['model_id' => null, 'service_id' => null];
        }
    }
    $templatePath = 'storage/vehicle_model_mapping_template.json';
    file_put_contents($templatePath, json_encode($combos, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    echo "Mapping file not found. Template written to {$templatePath}\n";
}

// Write seed SQL if any statements
if (!empty($seedStatements)) {
    if (!is_dir('database/seeds')) @mkdir('database/seeds', 0755, true);
    $seedPath = 'database/seeds/vehicle_knowledge_mapping_seed.sql';
    file_put_contents($seedPath, implode("\n", $seedStatements) . "\n");
    echo "Seed SQL written to {$seedPath}\n";
}

// Summary
echo "Processed: {$total}\n";
echo "Mapped (would be applied" . ($apply?"/applied":"") . "): {$mapped}\n";
echo "Unresolved: {$unresolved}\n";
echo "Missing required fields: {$missing_fields}\n";
echo "Duplicate slugs: " . count($duplicate_slugs) . "\n";
if (!empty($duplicate_slugs)) {
    echo "- Examples: " . implode(', ', array_slice(array_keys($duplicate_slugs),0,10)) . "\n";
}

echo "Template mapping path: {$mappingPath}\n";
if (file_exists('storage/vehicle_model_mapping_template.json')) {
    echo "Generated template: storage/vehicle_model_mapping_template.json\n";
}

exit(0);

?>