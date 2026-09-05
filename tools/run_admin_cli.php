<?php
// Simulate a GET request to /admin for local CLI diagnosis
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/originalshargh/admin';
$_SERVER['SCRIPT_NAME'] = '/originalshargh/index.php';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = '';
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../index.php';
