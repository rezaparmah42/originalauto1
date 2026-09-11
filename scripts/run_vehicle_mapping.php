<?php
// Repository-only vehicle mapping script
// Usage: php scripts/run_vehicle_mapping.php

$cwd = realpath(__DIR__ . '/../');
chdir($cwd);

function parseInsertStatements($sql, $table) {
    $result = [];
    $pattern = '/INSERT\s+INTO\s+' . preg_quote($table, '/') . '\s*\(([^)]+)\)\s*VALUES\s*(.*?)(?:ON\s+DUPLICATE|;)/is';
    if (preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $cols = array_map('trim', explode(',', $m[1]));
            $valsBlock = trim($m[2]);
            // find tuples
            if (preg_match_all('/\((.*?)\)(?=\s*(,|$))/s', $valsBlock, $tuples)) {
                foreach ($tuples[1] as $tuple) {
                    $vals = [];
                    $len = strlen($tuple);
                    $buf = '';
                    $inQ = false;
                    $qchar = '';
                    for ($i=0;$i<$len;$i++) {
                        $c = $tuple[$i];
                        if ($inQ) {
                            if ($c === $qchar && $tuple[$i-1] !== '\\') { $inQ = false; $buf .= $c; continue; }
                            $buf .= $c;
                        } else {
                            if ($c === '"' || $c === "'") { $inQ = true; $qchar = $c; $buf .= $c; }
                            elseif ($c === ',') { $vals[] = trim($buf); $buf = ''; }
                            else { $buf .= $c; }
                        }
                    }
                    if (strlen(trim($buf))>0) $vals[] = trim($buf);
                    // clean values
                    $clean = [];
                    foreach ($vals as $v) {
                        $v = trim($v);
                        if (strcasecmp($v, 'NULL')===0) $clean[] = null;
                        elseif (preg_match("/^'(.*)'$/s", $v, $vm)) $clean[] = str_replace("\\'","'", $vm[1]);
                        elseif (preg_match('/^"(.*)"$/s', $v, $vm2)) $clean[] = str_replace('\\"','"', $vm2[1]);
                        elseif (preg_match('/^NOW\(\)$/i', $v)) $clean[] = null;
                        else $clean[] = $v;
                    }
                    $row = [];
                    for ($i=0;$i<count($cols);$i++) {
                        $col = trim($cols[$i]);
                        $row[$col] = array_key_exists($i,$clean) ? $clean[$i] : null;
                    }
                    $result[] = $row;
                }
            }
        }
    }
    return $result;
}

$canonical = [
    'brands'=>[],
    'models'=>[],
    'services'=>[],
    'model_service'=>[]
];

// Read relevant SQL files
$sqlFiles = [
    'database/production_ready.sql',
    'database/production_complete.sql',
    'database/production_ready_cpanel.sql',
    'database/patch_52_real_vehicle_catalog_seed.sql',
    'database/vehicle_knowledge_base_migration.sql'
];

foreach ($sqlFiles as $f) {
    if (!file_exists($f)) continue;
    $sql = file_get_contents($f);
    // parse brands
    $brows = parseInsertStatements($sql, 'vehicle_brands');
    foreach ($brows as $r) {
        $id = isset($r['id']) ? (int)$r['id'] : null;
        $slug = isset($r['slug']) ? strtolower($r['slug']) : null;
        $name_en = isset($r['name_en']) ? $r['name_en'] : (isset($r['name']) ? $r['name'] : null);
        $name_fa = isset($r['name_fa']) ? $r['name_fa'] : null;
        if ($id) {
            $canonical['brands'][$id] = ['id'=>$id,'slug'=>$slug,'name_en'=>$name_en,'name_fa'=>$name_fa];
        }
    }
    // parse services
    $srows = parseInsertStatements($sql, 'services');
    foreach ($srows as $r) {
        $id = isset($r['id']) ? (int)$r['id'] : null;
        $slug = isset($r['slug']) ? strtolower($r['slug']) : null;
        $title_en = isset($r['title_en']) ? $r['title_en'] : null;
        $title_fa = isset($r['title_fa']) ? $r['title_fa'] : null;
        if ($id) {
            $canonical['services'][$id] = ['id'=>$id,'slug'=>$slug,'title_en'=>$title_en,'title_fa'=>$title_fa];
        }
    }
    // parse models
    $mrows = parseInsertStatements($sql, 'vehicle_models');
    foreach ($mrows as $r) {
        $id = isset($r['id']) ? (int)$r['id'] : null;
        $brand_id = isset($r['brand_id']) ? (int)$r['brand_id'] : null;
        $slug = isset($r['slug']) ? strtolower($r['slug']) : null;
        $name_en = isset($r['name_en']) ? $r['name_en'] : null;
        $name_fa = isset($r['name_fa']) ? $r['name_fa'] : null;
        if ($id) {
            $canonical['models'][$id] = ['id'=>$id,'brand_id'=>$brand_id,'slug'=>$slug,'name_en'=>$name_en,'name_fa'=>$name_fa];
        }
    }
    // parse vehicle_model_service table
    $vsrows = parseInsertStatements($sql, 'vehicle_model_service');
    foreach ($vsrows as $r) {
        // assume columns model_id, service_id order or named
        $model_id = isset($r['model_id']) ? (int)$r['model_id'] : null;
        $service_id = isset($r['service_id']) ? (int)$r['service_id'] : null;
        if ($model_id && $service_id) {
            $canonical['model_service'][] = ['model_id'=>$model_id,'service_id'=>$service_id];
        }
    }
}

// Build lookup maps by slug and names
$brandsBySlug = [];
$brandsByNameEn = [];
$brandsByNameFa = [];
foreach ($canonical['brands'] as $b) {
    if (!empty($b['slug'])) $brandsBySlug[$b['slug']] = $b['id'];
    if (!empty($b['name_en'])) $brandsByNameEn[strtolower($b['name_en'])] = $b['id'];
    if (!empty($b['name_fa'])) $brandsByNameFa[trim($b['name_fa'])] = $b['id'];
}

$servicesBySlug = [];
$servicesByNameEn = [];
$servicesByNameFa = [];
foreach ($canonical['services'] as $s) {
    if (!empty($s['slug'])) $servicesBySlug[$s['slug']] = $s['id'];
    if (!empty($s['title_en'])) $servicesByNameEn[strtolower($s['title_en'])] = $s['id'];
    if (!empty($s['title_fa'])) $servicesByNameFa[trim($s['title_fa'])] = $s['id'];
}

$modelsByBrand = []; // brand_id => [slug=>id, name_en=>id, name_fa=>id]
foreach ($canonical['models'] as $m) {
    $bid = $m['brand_id'];
    if (!isset($modelsByBrand[$bid])) $modelsByBrand[$bid] = ['by_slug'=>[], 'by_name_en'=>[], 'by_name_fa'=>[]];
    if (!empty($m['slug'])) $modelsByBrand[$bid]['by_slug'][$m['slug']] = $m['id'];
    if (!empty($m['name_en'])) $modelsByBrand[$bid]['by_name_en'][strtolower($m['name_en'])] = $m['id'];
    if (!empty($m['name_fa'])) $modelsByBrand[$bid]['by_name_fa'][trim($m['name_fa'])] = $m['id'];
}

// Read drafts
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('storage/vehicle_knowledge_drafts'));
$draftFiles = [];
foreach ($rii as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'json') {
        $draftFiles[] = $file->getPathname();
    }
}

$report = [
    'total_sources'=>count($draftFiles),
    'mapped'=>0,
    'unresolved'=>0,
    'brands_found'=>[],
    'models_found'=>[],
    'services_found'=>[]
];

$mappingTemplate = [];
$unresolvedLines = [];

foreach ($draftFiles as $f) {
    $data = json_decode(file_get_contents($f), true);
    if (!is_array($data)) continue;
    $detected_brand = isset($data['brand']) ? strtolower(trim($data['brand'])) : null;
    $detected_model = isset($data['model']) ? strtolower(trim($data['model'])) : null;
    $detected_service = isset($data['service_category']) ? strtolower(trim($data['service_category'])) : null;

    $matched_brand_id = null;
    $matched_model_id = null;
    $matched_service_id = null;

    // Brand match: try slug then name_en then name_fa
    if ($detected_brand) {
        if (isset($brandsBySlug[$detected_brand])) $matched_brand_id = $brandsBySlug[$detected_brand];
        elseif (isset($brandsByNameEn[$detected_brand])) $matched_brand_id = $brandsByNameEn[$detected_brand];
        elseif (isset($brandsByNameFa[$detected_brand])) $matched_brand_id = $brandsByNameFa[$detected_brand];
    }

    // Service match: try slug then title_en/title_fa contains
    if ($detected_service) {
        if (isset($servicesBySlug[$detected_service])) $matched_service_id = $servicesBySlug[$detected_service];
        elseif (isset($servicesByNameEn[$detected_service])) $matched_service_id = $servicesByNameEn[$detected_service];
        elseif (isset($servicesByNameFa[$detected_service])) $matched_service_id = $servicesByNameFa[$detected_service];
        else {
            // try partial match in title_en
            foreach ($servicesByNameEn as $tek=>$sid) {
                if (strpos($tek, $detected_service) !== false) { $matched_service_id = $sid; break; }
            }
            if (!$matched_service_id) {
                foreach ($servicesByNameFa as $tek=>$sid) { if (strpos($tek, $detected_service) !== false) { $matched_service_id = $sid; break; } }
            }
        }
    }

    // Model match: only if brand matched
    if ($matched_brand_id && $detected_model) {
        $bm = $modelsByBrand[$matched_brand_id] ?? null;
        if ($bm) {
            if (isset($bm['by_slug'][$detected_model])) $matched_model_id = $bm['by_slug'][$detected_model];
            elseif (isset($bm['by_name_en'][$detected_model])) $matched_model_id = $bm['by_name_en'][$detected_model];
            elseif (isset($bm['by_name_fa'][$detected_model])) $matched_model_id = $bm['by_name_fa'][$detected_model];
            else {
                // try partial match
                foreach ($bm['by_name_en'] as $tek=>$mid) { if (strpos($tek, $detected_model) !== false) { $matched_model_id = $mid; break; } }
            }
        }
    }

    // Validate vehicle_model_service relationship if model and service found
    $relationship_ok = false;
    if ($matched_model_id && $matched_service_id) {
        foreach ($canonical['model_service'] as $rel) {
            if ($rel['model_id'] == $matched_model_id && $rel['service_id'] == $matched_service_id) { $relationship_ok = true; break; }
        }
    }

    $mapping_status = 'unresolved';
    if ($matched_brand_id && $matched_model_id && $matched_service_id && $relationship_ok) $mapping_status = 'mapped';
    elseif ($matched_brand_id || $matched_model_id || $matched_service_id) $mapping_status = 'partial';

    $report['brands_found'][$detected_brand] = ($report['brands_found'][$detected_brand] ?? 0) + 1;
    $report['models_found'][$detected_model] = ($report['models_found'][$detected_model] ?? 0) + 1;
    $report['services_found'][$detected_service] = ($report['services_found'][$detected_service] ?? 0) + 1;

    if ($mapping_status === 'mapped') $report['mapped']++; else $report['unresolved']++;

    $mappingTemplate[] = [
        'source_file' => $data['source_file'] ?? $f,
        'detected_brand' => $detected_brand,
        'detected_model' => $detected_model,
        'detected_service' => $detected_service,
        'matched_brand_id' => $matched_brand_id,
        'matched_model_id' => $matched_model_id,
        'matched_service_id' => $matched_service_id,
        'mapping_status' => $mapping_status
    ];

    if ($mapping_status !== 'mapped') {
        $unresolvedLines[] = implode(',', [
            $data['slug'] ?? '',
            $data['source_file'] ?? '',
            $detected_brand,
            $detected_model,
            $detected_service,
            $matched_brand_id ?? '',
            $matched_model_id ?? '',
            $matched_service_id ?? '',
            $mapping_status
        ]);
    }
}

// write template
file_put_contents('storage/vehicle_model_mapping_template.json', json_encode($mappingTemplate, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
// write report
file_put_contents('storage/vehicle_mapping_report.json', json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
// write unresolved CSV
$csvHeader = "slug,source_file,detected_brand,detected_model,detected_service,matched_brand_id,matched_model_id,matched_service_id,mapping_status\n";
file_put_contents('storage/unresolved_vehicle_mapping.csv', $csvHeader . implode("\n", $unresolvedLines));

// Done
echo "Mapping pass complete. Template, report, and unresolved CSV written to storage/.\n";

exit(0);
