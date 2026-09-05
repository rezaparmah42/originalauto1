<?php
// Smoke test for /technician/dashboard
$_SERVER['REQUEST_URI'] = '/originalshargh/technician/dashboard';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
