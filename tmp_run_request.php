<?php
// temporary runner to simulate HTTP request for CLI
$_SERVER['REQUEST_URI'] = '/vehicles/peugeot/peugeot-206';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTP_HOST'] = '127.0.0.1';
require __DIR__ . '/index.php';
