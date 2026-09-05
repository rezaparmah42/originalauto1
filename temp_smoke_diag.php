<?php
// Temp smoke test: diagnose route
$_SERVER['REQUEST_URI'] = '/originalshargh/diagnostic';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
