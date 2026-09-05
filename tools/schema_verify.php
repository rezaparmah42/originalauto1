<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$db = App\Core\Database::connect();
if (!$db) {
    echo "ERROR: unable to connect to database\n";
    exit(1);
}

$tables = ['repairs', 'vehicles'];
foreach ($tables as $table) {
    echo "TABLE $table:\n";
    $stmt = $db->query("SHOW COLUMNS FROM `$table`");
    if ($stmt === false) {
        echo "  ERROR: cannot show columns for $table\n\n";
        continue;
    }
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . ' | ' . $col['Type'] . ' | ' . $col['Null'] . ' | ' . $col['Key'] . ' | ' . $col['Default'] . ' | ' . $col['Extra'] . "\n";
    }
    echo "\n";
    $stmt = $db->query("SHOW INDEX FROM `$table`");
    if ($stmt !== false) {
        echo "INDEXES for $table:\n";
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $idx) {
            echo $idx['Key_name'] . ' | ' . $idx['Column_name'] . ' | ' . $idx['Non_unique'] . ' | ' . $idx['Index_type'] . "\n";
        }
        echo "\n";
    }
}

$stmt = $db->query("SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('repairs', 'vehicles') AND REFERENCED_TABLE_NAME IS NOT NULL");
if ($stmt !== false) {
    echo "FOREIGN KEYS:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['CONSTRAINT_NAME'] . ' | ' . $row['TABLE_NAME'] . ' | ' . $row['COLUMN_NAME'] . ' | ' . $row['REFERENCED_TABLE_NAME'] . ' | ' . $row['REFERENCED_COLUMN_NAME'] . "\n";
    }
}
