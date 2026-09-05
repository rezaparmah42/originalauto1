<?php
// Smoke test for API analyze
$_SERVER['REQUEST_URI'] = '/originalshargh/api/diagnostic/analyze';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
$_SERVER['REQUEST_METHOD'] = 'POST';

// Provide form-like POST data
$_POST['dtc_codes'] = 'P0300,P0101,P0420';

require __DIR__ . '/index.php';
