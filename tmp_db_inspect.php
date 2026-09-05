<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::connect();
$tables = ['articles', 'products', 'services', 'invoices'];

foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $res = $db->query("SHOW TABLES LIKE '$table'");
    if (!$res || $res->rowCount() === 0) {
        echo "  MISSING\n\n";
        continue;
    }

    $cols = $db->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_column($cols, 'Field');
    foreach ($cols as $col) {
        echo "  {$col['Field']} {$col['Type']} {$col['Null']} {$col['Key']} {$col['Extra']}\n";
    }

    if (in_array('status', $columnNames, true) && in_array('slug', $columnNames, true)) {
        $counts = $db->query("SELECT COUNT(*) AS total, SUM(status = 1) AS active, SUM(status = 1 AND slug != '') AS active_with_slug FROM `$table`")->fetch(PDO::FETCH_ASSOC);
        echo sprintf("  ROWS: total=%s, active=%s, active_with_slug=%s\n", $counts['total'], $counts['active'], $counts['active_with_slug']);
        if ((int) ($counts['active'] ?? 0) === 0) {
            echo "  NOTE: No production content exists for $table\n";
        }
        if ((int) ($counts['active_with_slug'] ?? 0) === 0) {
            echo "  NOTE: No valid slug content exists for $table\n";
        }
    } elseif (in_array('slug', $columnNames, true)) {
        $counts = $db->query("SELECT COUNT(*) AS total, SUM(slug != '') AS valid_slugs FROM `$table`")->fetch(PDO::FETCH_ASSOC);
        echo sprintf("  ROWS: total=%s, valid_slugs=%s\n", $counts['total'], $counts['valid_slugs']);
        if ((int) ($counts['valid_slugs'] ?? 0) === 0) {
            echo "  NOTE: No valid slug content exists for $table\n";
        }
    }

    if (in_array('created_at', $columnNames, true)) {
        $row = $db->query("SELECT created_at FROM `$table` ORDER BY created_at DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo "  LATEST created_at: {$row['created_at']}\n";
        }
    }

    if (in_array('title', $columnNames, true) || in_array('title_fa', $columnNames, true) || in_array('title_en', $columnNames, true)) {
        $titleField = in_array('title', $columnNames, true) ? 'title' : (in_array('title_fa', $columnNames, true) ? 'title_fa' : 'title_en');
        $row = $db->query("SELECT `$titleField` FROM `$table` WHERE `$titleField` IS NOT NULL AND `$titleField` != '' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo "  SAMPLE title field ($titleField): {$row[$titleField]}\n";
        }
    }

    if (in_array('slug', $columnNames, true)) {
        $slugRow = $db->query("SELECT slug FROM `$table` WHERE slug IS NOT NULL AND slug != '' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($slugRow) {
            echo "  SLUG: " . $slugRow['slug'] . "\n";
        }
    }

    echo "\n";
}
