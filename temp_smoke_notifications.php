<?php
// Smoke test for /notifications
$_SERVER['REQUEST_URI'] = '/originalshargh/notifications';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
