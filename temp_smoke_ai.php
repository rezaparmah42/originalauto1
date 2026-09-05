<?php
// Smoke test for AI diagnostic UI
$_SERVER['REQUEST_URI'] = '/originalshargh/ai-diagnostic';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
require __DIR__ . '/index.php';
