<?php

header('Content-Type: application/json');

$brandId = $_GET['brand_id'] ?? null;
$modelId = $_GET['model_id'] ?? null;
$year = $_GET['year'] ?? null;
$engineType = $_GET['engine_type'] ?? null;

$query = Product::query();

if ($brandId) {
    $query->join('product_vehicle_compatibilities', 'products.id', '=', 'product_vehicle_compatibilities.product_id')
          ->where('product_vehicle_compatibilities.brand_id', $brandId);
}

if ($modelId) {
    $query->join('vehicle_models', 'vehicles.id', '=', 'vehicle_models.id')
          ->join('product_vehicle_compatibilities', 'vehicles.id', '=', 'product_vehicle_compatibilities.model_id')
          ->where('vehicle_models.id', $modelId);
}

if ($year) {
    $query->join('product_vehicle_compatibilities', 'products.id', '=', 'product_vehicle_compatibilities.product_id')
          ->where('product_vehicle_compatibilities.year', $year);
}

if ($engineType) {
    $query->join('product_vehicle_compatibilities', 'products.id', '=', 'product_vehicle_compatibilities.product_id')
          ->where('product_vehicle_compatibilities.engine_type', $engineType);
}

$products = $query->get();

echo json_encode($products);