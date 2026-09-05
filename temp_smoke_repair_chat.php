<?php
// Smoke test for /repair/chat/1
$_SERVER['REQUEST_URI'] = '/originalshargh/repair/chat/1';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
