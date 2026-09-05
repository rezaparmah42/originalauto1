<?php
// Smoke test for /admin/technicians
$_SERVER['REQUEST_URI'] = '/originalshargh/admin/technicians';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
