<?php

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$brandId = $_GET['brand_id'] ?? null;

if ($brandId) {
    $db = Database::connect();
    $stmt = $db->prepare('SELECT id, name_en, name_fa, slug FROM vehicle_models WHERE brand_id = ? AND status = 1 ORDER BY name_fa, name_en');
    $stmt->execute([$brandId]);
    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($models);
} else {
    echo json_encode([]);
}