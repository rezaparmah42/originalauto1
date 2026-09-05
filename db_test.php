<?php
try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1;port=3306",
        "root",
        ""
    );
    echo "DB_OK";
} catch (Exception $e) {
    echo "DB_ERROR: " . $e->getMessage();
}
