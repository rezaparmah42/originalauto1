<?php
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = App\Core\Database::connect();
    $stmt = $db->prepare('SELECT id, email, name, role, password FROM users WHERE email = ? LIMIT 1');
    $stmt->execute(['admin@originalshargh.com']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "FOUND\n";
        echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "NOT_FOUND\n";
    }
} catch (Throwable $t) {
    echo "ERROR: " . get_class($t) . " - " . $t->getMessage() . "\n";
}
