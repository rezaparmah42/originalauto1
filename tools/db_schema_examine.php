<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::connect();
$tables = ['orders', 'order_items', 'products', 'invoices', 'payments', 'api_tokens', 'api_devices', 'api_logs', 'notifications', 'sms_logs', 'chat_messages', 'repair_updates', 'repair_notes', 'technicians', 'vehicle_profiles', 'diagnostic_sessions', 'diagnostic_results', 'obd_error_codes', 'diagnostic_knowledge', 'repair_recommendations', 'maintenance_records', 'diagnostic_reports', 'suppliers', 'inventory_history', 'product_compatibility'];
foreach ($tables as $table) {
    echo "--- TABLE: $table ---\n";
    try {
        $row = $db->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo $row['Create Table'] . "\n";
        } else {
            echo "MISSING\n";
        }
    } catch (PDOException $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
