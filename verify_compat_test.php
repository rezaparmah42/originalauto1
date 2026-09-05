<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';
require __DIR__ . '/app/Models/Model.php';
require __DIR__ . '/app/Models/ProductCompatibility.php';

$db = \App\Core\Database::connect();
$pc = new \App\Models\ProductCompatibility();
$rows = $pc->getVehicleModelOptions();
if (empty($rows)) {
    echo "NO_VEHICLE_OPTIONS\n";
    exit(1);
}

$ids = array_map(function ($row) { return (int) $row['id']; }, array_slice($rows, 0, 2));
$pc->replaceRelations(3, $ids);
$relations = $pc->getCompatibleVehicleModels(3);

printf("vehicle_options=%d\n", count($rows));
printf("after_replace=%d\n", count($relations));
foreach ($relations as $row) {
    printf("%d|%s|%s\n", (int) $row['model_id'], (string) ($row['brand_name_fa'] ?? ''), (string) ($row['name_fa'] ?? ''));
}

$stmt = $db->prepare('SELECT product_id, model_id FROM product_compatibility WHERE product_id = 3 ORDER BY model_id');
$stmt->execute();
foreach ($stmt->fetchAll() as $row) {
    echo 'live_db_row=' . (int)$row['product_id'] . ':' . (int)$row['model_id'] . PHP_EOL;
}
