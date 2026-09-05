<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';

$pdo = \App\Core\Database::connect();
$stmt = $pdo->prepare('SELECT COUNT(*) AS total FROM articles');
$stmt->execute();
echo 'ARTICLE_COUNT=' . $stmt->fetchColumn() . PHP_EOL;
