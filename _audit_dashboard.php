<?php
$db = new PDO('mysql:host=localhost;dbname=original_east;charset=utf8mb4', 'root', '');

$tables = ['users', 'products', 'services', 'bookings', 'repairs', 'orders', 'articles', 'diagnostic_sessions', 'diagnostic_results', 'diagnostic_knowledge'];

foreach($tables as $table) {
    echo "=== $table ===\n";
    $result = $db->query("DESCRIBE $table");
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo "  {$row['Field']} ({$row['Type']})\n";
    }
    $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
    echo "  Count: $count\n\n";
}

echo "\n=== DASHBOARD QUERY VERIFICATION ===\n\n";

// Test the exact queries used in dashboard

// 1. Admin Model getDashboardStats
echo "Testing Admin.getDashboardStats() queries:\n";
$queries = [
    'users' => 'SELECT COUNT(*) FROM users',
    'products' => 'SELECT COUNT(*) FROM products',
    'services' => 'SELECT COUNT(*) FROM services',
    'bookings' => 'SELECT COUNT(*) FROM bookings',
    'repairs' => 'SELECT COUNT(*) FROM repairs',
];

foreach ($queries as $label => $query) {
    try {
        $result = $db->query($query)->fetchColumn();
        echo "  ✓ $label: $result\n";
    } catch (Exception $e) {
        echo "  ✗ $label: ERROR - {$e->getMessage()}\n";
    }
}

// 2. Optional queries
echo "\nOptional queries (may fail if tables missing columns):\n";
try {
    $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE stock > 0 AND stock <= 10");
    echo "  ✓ products.stock column exists\n";
} catch (Exception $e) {
    echo "  ✗ products.stock check failed\n";
}

try {
    $result = $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending')");
    echo "  ✓ orders table exists\n";
} catch (Exception $e) {
    echo "  ✗ orders table missing\n";
}

// 3. Booking pending count
echo "\nBooking model getPendingCount():\n";
try {
    $result = $db->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
    echo "  ✓ Pending bookings: $result\n";
} catch (Exception $e) {
    echo "  ✗ Error: {$e->getMessage()}\n";
}

// 4. Repair active count
echo "\nRepair model getActiveCount():\n";
try {
    $result = $db->query("SELECT COUNT(*) FROM repairs WHERE status NOT IN ('completed', 'cancelled')")->fetchColumn();
    echo "  ✓ Active repairs: $result\n";
} catch (Exception $e) {
    echo "  ✗ Error: {$e->getMessage()}\n";
}

// 5. Diagnostic scans
echo "\nDiagnostic model getScanCount():\n";
try {
    $result = $db->query("SELECT COUNT(*) AS total FROM diagnostic_sessions")->fetchColumn();
    echo "  ✓ Total scans: $result\n";
} catch (Exception $e) {
    echo "  ✗ Error: {$e->getMessage()}\n";
}

// 6. DiagnosticAI knowledge
echo "\nDiagnosticAI model getKnowledgeCount():\n";
try {
    $result = $db->query("SELECT COUNT(*) FROM diagnostic_knowledge")->fetchColumn();
    echo "  ✓ Knowledge records: $result\n";
} catch (Exception $e) {
    echo "  ✗ Error: {$e->getMessage()}\n";
}

// 7. Recent records
echo "\nRecent data queries:\n";
try {
    $result = $db->query("SELECT COUNT(*) FROM users ORDER BY created_at DESC LIMIT 10")->fetchColumn();
    echo "  ✓ Recent users queryable\n";
} catch (Exception $e) {
    echo "  ✗ Recent users error: {$e->getMessage()}\n";
}

try {
    $result = $db->query("SELECT COUNT(*) FROM bookings b LEFT JOIN users u ON u.id = b.user_id LEFT JOIN services s ON s.id = b.service_id ORDER BY b.created_at DESC LIMIT 10")->fetchColumn();
    echo "  ✓ Recent bookings queryable\n";
} catch (Exception $e) {
    echo "  ✗ Recent bookings error: {$e->getMessage()}\n";
}

try {
    $result = $db->query("SELECT COUNT(*) FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 10")->fetchColumn();
    echo "  ✓ Recent orders queryable\n";
} catch (Exception $e) {
    echo "  ✗ Recent orders error: {$e->getMessage()}\n";
}

echo "\nDone.\n";
?>
