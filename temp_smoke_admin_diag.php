<?php
// Temp smoke test: admin diagnostics
$_SERVER['REQUEST_URI'] = '/originalshargh/admin/diagnostics';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
