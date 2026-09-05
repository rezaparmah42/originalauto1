<?php
/**
 * Simple JSON-LD schema validator for rendered HTML files.
 * Usage: php scripts/schema_validator.php path/to/rendered.html
 * It extracts <script type="application/ld+json"> blocks and validates basic required fields for Article/Product/Service.
 */

if ($argc < 2) {
    echo "Usage: php scripts/schema_validator.php path/to/file.html\n";
    exit(1);
}

$path = $argv[1];
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(2);
}

$html = file_get_contents($path);
if ($html === false) {
    echo "Cannot read file: $path\n";
    exit(3);
}

// Extract JSON-LD blocks
preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is', $html, $matches);
$blocks = $matches[1] ?? [];
if (empty($blocks)) {
    echo "No JSON-LD blocks found in $path\n";
    exit(0);
}

$index = 0;
foreach ($blocks as $raw) {
    $index++;
    $json = trim($raw);
    // Some JSON-LD may be embedded via PHP echo; try to decode
    $data = json_decode($json, true);
    if ($data === null) {
        echo "Block #$index: invalid JSON (json_decode error)\n";
        continue;
    }
    // Support arrays of graphs
    $graphs = is_assoc($data) ? [$data] : $data;
    foreach ($graphs as $g) {
        $type = $g['@type'] ?? ($g['type'] ?? null);
        echo "Block #$index: type=" . ($type ?? 'unknown') . "\n";
        $errors = [];
        if (strcasecmp($type, 'Article') === 0) {
            foreach (['headline','url','datePublished'] as $f) {
                if (empty($g[$f])) $errors[] = $f;
            }
            if (empty($g['author'])) $errors[] = 'author';
        } elseif (strcasecmp($type, 'Product') === 0) {
            foreach (['name','url'] as $f) {
                if (empty($g[$f])) $errors[] = $f;
            }
        } elseif (strcasecmp($type, 'Service') === 0) {
            foreach (['name','url','provider'] as $f) {
                if (empty($g[$f])) $errors[] = $f;
            }
        } elseif (is_array($g['@type'] ?? null)) {
            // multiple types - do basic checks
            if (empty($g['name']) && empty($g['headline'])) $errors[] = 'name/headline';
        }
        if (empty($errors)) {
            echo "  → OK\n";
        } else {
            echo "  → Missing fields: " . implode(', ', $errors) . "\n";
        }
    }
}

function is_assoc($arr) {
    if (!is_array($arr)) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}
