<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::connect();
$tables = [
    'api_tokens','api_devices','api_logs','chat_messages','diagnostic_sessions','diagnostic_results','obd_error_codes','diagnostic_knowledge','repair_recommendations','maintenance_records','diagnostic_reports','notifications','order_items','payments','invoices','orders','inventory_history','product_compatibility','repair_notes','workshop_tasks','repair_updates','sms_logs','suppliers','technicians','technician_activity','vehicle_profiles','products','services','vehicles','bookings','vehicle_models'
];
foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $stmt = $db->query("SHOW TABLES LIKE '" . str_replace("'", "''", $table) . "'");
    $exists = $stmt && $stmt->fetchColumn();
    echo "  EXISTS: " . ($exists ? 'yes' : 'no') . "\n";
    if ($exists) {
        $cols = $db->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $col) {
            echo "    " . $col['Field'] . " " . $col['Type'] . " " . ($col['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . " Default=" . var_export($col['Default'], true) . " Key=" . $col['Key'] . "\n";
        }
    }
}
