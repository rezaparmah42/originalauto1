<?php
// Smoke test for /admin/notifications
$_SERVER['REQUEST_URI'] = '/originalshargh/admin/notifications';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
