<?php
session_name('originalshargh_session');
session_save_path('C:/xampp/htdocs/originalshargh/_tmp_session');
if (session_status() === PHP_SESSION_NONE) { session_start(); }

require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';
require __DIR__ . '/app/Models/Model.php';
require __DIR__ . '/app/Helpers/VehicleSelectionSession.php';
require __DIR__ . '/app/Models/Product.php';

App\Helpers\VehicleSelectionSession::setSelectedVehicle([
    'vehicle_brand_id' => 1,
    'vehicle_model_id' => 2,
    'vehicle_year' => '2024',
]);

$selected = App\Helpers\VehicleSelectionSession::getSelectedVehicle();
$p = new \App\Models\Product();
$match = $p->getVisibleProducts(1, 12, ['vehicle_model_id' => (int) ($selected['vehicle_model_id'] ?? 0)]);
App\Helpers\VehicleSelectionSession::clearSelectedVehicle();
$noFilter = $p->getVisibleProducts(1, 12, []);

echo 'selected_vehicle=' . json_encode($selected, JSON_UNESCAPED_UNICODE) . PHP_EOL;
echo 'match_total=' . (int) ($match['total'] ?? 0) . PHP_EOL;
echo 'no_filter_total=' . (int) ($noFilter['total'] ?? 0) . PHP_EOL;
