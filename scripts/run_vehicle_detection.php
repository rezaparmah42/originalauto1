<?php
// Repository-only vehicle detection script
// Usage: php scripts/run_vehicle_detection.php

$cwd = realpath(__DIR__ . '/../');
chdir($cwd);

function parseInsertStatements($sql, $table) {
    $result = [];
    $pattern = '/INSERT\s+INTO\s+' . preg_quote($table, '/') . '\s*\(([^)]+)\)\s*VALUES\s*(.*?)(?:ON\s+DUPLICATE|;)/is';
    if (preg_match_all($pattern, $sql, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $cols = array_map('trim', explode(',', $m[1]));
            $valsBlock = trim($m[2]);
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

// Load canonical brands and models from SQL seeds
$sqlFiles = [
    'database/production_ready.sql',
    'database/production_complete.sql',
    'database/production_ready_cpanel.sql',
    'database/patch_52_real_vehicle_catalog_seed.sql'
];

$brands = []; // id=>['slug','name_en','name_fa']
$models = []; // id=>['brand_id','slug','name_en','name_fa']

foreach ($sqlFiles as $f) {
    if (!file_exists($f)) continue;
    $sql = file_get_contents($f);
    foreach (parseInsertStatements($sql, 'vehicle_brands') as $r) {
        $id = isset($r['id']) ? (int)$r['id'] : null;
        if ($id) $brands[$id] = [
            'slug'=>isset($r['slug'])?strtolower($r['slug']):null,
            'name_en'=>isset($r['name_en'])?$r['name_en']:(isset($r['name'])?$r['name']:null),
            'name_fa'=>isset($r['name_fa'])?$r['name_fa']:null
        ];
    }
    foreach (parseInsertStatements($sql, 'vehicle_models') as $r) {
        $id = isset($r['id']) ? (int)$r['id'] : null;
        if ($id) $models[$id] = [
            'brand_id'=>isset($r['brand_id'])?(int)$r['brand_id']:null,
            'slug'=>isset($r['slug'])?strtolower($r['slug']):null,
            'name_en'=>isset($r['name_en'])?$r['name_en']:null,
            'name_fa'=>isset($r['name_fa'])?$r['name_fa']:null
        ];
    }
}

// Build lookups
$brandTokens = []; // token => id
foreach ($brands as $id=>$b) {
    $variants = [];
    if (!empty($b['slug'])) $variants[] = $b['slug'];
    if (!empty($b['name_en'])) $variants[] = strtolower($b['name_en']);
    if (!empty($b['name_fa'])) $variants[] = trim($b['name_fa']);
    foreach ($variants as $v) $brandTokens[$v] = $id;
}

$modelTokens = []; // token => [model_id,...]
foreach ($models as $id=>$m) {
    $variants = [];
    if (!empty($m['slug'])) $variants[] = $m['slug'];
    if (!empty($m['name_en'])) $variants[] = strtolower($m['name_en']);
    if (!empty($m['name_fa'])) $variants[] = trim($m['name_fa']);
    // also add numeric tokens from slug/name (e.g., 206)
    foreach ($variants as $v) {
        // split on non-alnum
        $parts = preg_split('/[^\p{L}\p{N}]+/u', $v);
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p==='') continue;
            $pLower = mb_strtolower($p);
            if (!isset($modelTokens[$pLower])) $modelTokens[$pLower] = [];
            $modelTokens[$pLower][] = $id;
        }
    }
}

// invalid tokens to remove
$invalid = [
    'originalauto','originalshargh','page','pages','p1','p2','p3','eng','en','fa','manual','service','repair','page1','page2'
];
$genericTechnical = ['engine','gearbox','brake','brakes','diagnostic','diagnostics','manual','service','repair','fuelsystem','periodicservice','periodic-service','periodic'];

// Read drafts
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('storage/vehicle_knowledge_drafts'));
$draftFiles = [];
foreach ($rii as $file) {
    if ($file->isFile() && strtolower($file->getExtension()) === 'json') $draftFiles[] = $file->getPathname();
}

$report = [
    'total_sources'=>count($draftFiles),
    'detected_brands'=>[],
    'detected_models'=>[],
    'unresolved_sources'=>0,
    'confidence_scores'=>[]
];

$unresolvedCsv = [];

foreach ($draftFiles as $f) {
    $data = json_decode(file_get_contents($f), true);
    if (!is_array($data)) continue;
    $source = $data['source_file'] ?? $f;
    $filename = pathinfo($source, PATHINFO_BASENAME);
    $dirname = pathinfo($f, PATHINFO_DIRNAME);
    // collect candidate text from filename and dir
    $candidates = [];
    $candidates[] = strtolower($filename);
    $candidates[] = strtolower($dirname);
    if (!empty($data['title_fa'])) $candidates[] = mb_strtolower($data['title_fa']);
    if (!empty($data['seo_title'])) $candidates[] = mb_strtolower($data['seo_title']);
    if (!empty($data['excerpt'])) $candidates[] = mb_strtolower($data['excerpt']);
    if (!empty($data['content'])) $candidates[] = mb_strtolower($data['content']);

    $text = implode(' ', $candidates);
    // replace separators with spaces
    $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text);
    $tokens = array_filter(array_map('trim', preg_split('/\s+/u', $text)));
    // remove invalid and generic technical tokens
    $filtered = [];
    foreach ($tokens as $t) {
        $tl = mb_strtolower($t);
        if (in_array($tl, $invalid, true)) continue;
        if (in_array($tl, $genericTechnical, true)) continue;
        // remove tokens that are just single letters
        if (mb_strlen($tl) === 1) continue;
        $filtered[] = $tl;
    }

    // detect brand
    $detected_brand = null;
    $brand_match_token = null;
    // prefer folder name as brand if it maps to known brand tokens
    $dirParts = explode(DIRECTORY_SEPARATOR, $f);
    foreach ($dirParts as $part) {
        $part = strtolower($part);
        if (isset($brandTokens[$part])) { $detected_brand = $brandTokens[$part]; $brand_match_token = $part; break; }
    }
    if (!$detected_brand) {
        foreach ($filtered as $t) {
            if (isset($brandTokens[$t])) { $detected_brand = $brandTokens[$t]; $brand_match_token = $t; break; }
        }
    }

    // detect model
    $detected_model = null;
    $model_match_token = null;
    foreach ($filtered as $t) {
        if (isset($modelTokens[$t])) {
            $candidatesModelIds = $modelTokens[$t];
            // if brand known, prefer models for that brand
            if ($detected_brand) {
                foreach ($candidatesModelIds as $mid) {
                    if (isset($models[$mid]) && $models[$mid]['brand_id']==$detected_brand) { $detected_model = $mid; $model_match_token = $t; break 2; }
                }
            }
            // otherwise pick first candidate
            $detected_model = $candidatesModelIds[0];
            $model_match_token = $t;
            break;
        }
    }

    // confidence scoring
    $score = 0.0;
    if ($detected_brand) $score += 0.6;
    if ($detected_model) $score += 0.6;
    // if both found and brand matches model, boost
    if ($detected_brand && $detected_model && isset($models[$detected_model]) && $models[$detected_model]['brand_id']==$detected_brand) $score = min(1.0, $score + 0.2);
    // service token presence small boost
    foreach ($filtered as $t) { if (in_array($t, ['brakes','engine','gearbox','diagnostic','electrical','cooling','suspension','fuel','fuelsystem','periodicservice','periodic'], true)) { $score += 0.05; break; } }
    if ($score>1.0) $score = 1.0;

    $detected_brand_label = $detected_brand ? ($brands[$detected_brand]['name_en'] ?? $brands[$detected_brand]['slug'] ?? $detected_brand) : null;
    $detected_model_label = $detected_model ? ($models[$detected_model]['name_en'] ?? $models[$detected_model]['slug'] ?? $detected_model) : null;

    $report['confidence_scores'][] = $score;
    if ($detected_brand_label) $report['detected_brands'][$detected_brand_label] = ($report['detected_brands'][$detected_brand_label] ?? 0) + 1;
    if ($detected_model_label) $report['detected_models'][$detected_model_label] = ($report['detected_models'][$detected_model_label] ?? 0) + 1;

    $entry = [
        'source_file'=>$source,
        'filename'=>$filename,
        'detected_brand'=>$detected_brand_label,
        'detected_model'=>$detected_model_label,
        'brand_token'=>$brand_match_token,
        'model_token'=>$model_match_token,
        'confidence_score'=>$score
    ];

    // unresolved if confidence < 0.6
    if ($score < 0.6) {
        $report['unresolved_sources']++;
        $unresolvedCsv[] = implode(',', [
            $filename,
            $source,
            $detected_brand_label ?? '',
            $detected_model_label ?? '',
            $brand_match_token ?? '',
            $model_match_token ?? '',
            $score
        ]);
    }
}

// finalize report stats
$avg = count($report['confidence_scores']) ? array_sum($report['confidence_scores'])/count($report['confidence_scores']) : 0.0;
$report['avg_confidence'] = $avg;
$report['ready_for_mapping'] = max(0, $report['total_sources'] - $report['unresolved_sources']);

file_put_contents('storage/vehicle_detection_report.json', json_encode($report, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
$csvHeader = "filename,source_file,detected_brand,detected_model,brand_token,model_token,confidence_score\n";
file_put_contents('storage/unresolved_vehicle_detection.csv', $csvHeader . implode("\n", $unresolvedCsv));

echo "Detection pass complete. Report and unresolved CSV written to storage/.\n";

exit(0);
