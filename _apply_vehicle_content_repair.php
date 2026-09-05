<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';
$db = App\Core\Database::connect();
$sql = file_get_contents(__DIR__ . '/database/patch_37_vehicle_content_repair.sql');
foreach (array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $sql))) as $statement) {
    $statement = preg_replace('/^(?:--.*\R)+/', '', $statement);
    if (trim($statement) !== '') $db->exec($statement);
}
echo 'vehicle content repaired';
