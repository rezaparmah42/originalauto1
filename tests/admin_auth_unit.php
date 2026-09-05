<?php
define('SITE_URL', 'http://localhost/originalshargh');
require_once __DIR__ . '/../includes/functions.php';

$out = [];

// NO_SESSION
@session_start();
$_SESSION = [];
$out[] = 'NO_SESSION isAdmin=' . (isAdmin() ? '1' : '0');
$out[] = 'NO_SESSION isCustomer=' . (isCustomerLoggedIn() ? '1' : '0');
$out[] = 'NO_SESSION isLoggedIn=' . (isLoggedIn() ? '1' : '0');

// CUSTOMER
@session_start();
$_SESSION = ['customer_id' => 42, 'customer_name' => 'Cust'];
$out[] = 'CUSTOMER isAdmin=' . (isAdmin() ? '1' : '0');
$out[] = 'CUSTOMER isCustomer=' . (isCustomerLoggedIn() ? '1' : '0');
$out[] = 'CUSTOMER isLoggedIn=' . (isLoggedIn() ? '1' : '0');

// ADMIN
@session_start();
$_SESSION = ['admin' => ['id' => 1, 'role' => 'admin', 'name' => 'Admin']];
$out[] = 'ADMIN isAdmin=' . (isAdmin() ? '1' : '0');
$out[] = 'ADMIN isCustomer=' . (isCustomerLoggedIn() ? '1' : '0');
$out[] = 'ADMIN isLoggedIn=' . (isLoggedIn() ? '1' : '0');

file_put_contents(__DIR__ . '/admin_auth_unit_results.txt', implode(PHP_EOL, $out));
echo 'WROTE';
