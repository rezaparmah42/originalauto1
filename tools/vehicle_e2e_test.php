<?php
// tools/vehicle_e2e_test.php - lightweight end-to-end vehicle foundation smoke test (read-only)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Models/Model.php';
require_once __DIR__ . '/../app/Models/VehicleCatalog.php';
require_once __DIR__ . '/../app/Models/Product.php';
require_once __DIR__ . '/../app/Models/ProductCompatibility.php';

$out = ['ok' => false, 'steps' => []];
try {
    $vc = new \App\Models\VehicleCatalog();
    $brands = $vc->getBrands();
    $out['steps'][] = ['step' => 'brands', 'count' => count($brands), 'sample' => array_slice($brands, 0, 5)];
    if (empty($brands)) {
        echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit(0);
    }

    $brand = $brands[0];
    $models = $vc->getModels($brand);
    $out['steps'][] = ['step' => 'models_for_brand', 'brand' => $brand, 'count' => count($models), 'sample' => array_slice($models, 0, 5)];
    if (empty($models)) {
        echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit(0);
    }

    $model = $models[0];
    $years = $vc->getYears($brand, $model);
    $out['steps'][] = ['step' => 'years_for_model', 'brand' => $brand, 'model' => $model, 'count' => count($years), 'sample' => array_slice($years, 0, 5)];

    $year = null;
    if (!empty($years) && isset($years[0]['year_start'])) {
        $year = $years[0]['year_start'];
    }

    $vehicle = $vc->getVehicle($brand, $model, $year);
    $out['steps'][] = ['step' => 'get_vehicle', 'brand' => $brand, 'model' => $model, 'year' => $year, 'found' => (bool) $vehicle, 'vehicle' => $vehicle ?: null];

    if ($vehicle && isset($vehicle['id'])) {
        $compat = new \App\Models\ProductCompatibility();
        $products = $compat->getProductsByVehicle((int) $vehicle['id']);
        $out['steps'][] = ['step' => 'products_for_vehicle', 'vehicle_id' => (int) $vehicle['id'], 'count' => $products['total'] ?? count($products), 'sample' => array_slice($products['products'] ?? $products, 0, 5)];
    }

    $out['ok'] = true;
} catch (Throwable $e) {
    $out['error'] = $e->getMessage();
}

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
