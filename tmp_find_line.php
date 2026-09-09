<?php
$file = 'app/Controllers/ServiceController.php';
$contents = file($file);
foreach ($contents as $i => $line) {
    if (strpos($line, "serviceModel->db->prepare('SELECT 1 FROM vehicle_model_service") !== false) {
        echo 'Found at line: ' . ($i+1) . "\n";
        echo trim($line) . "\n";
    }
}
