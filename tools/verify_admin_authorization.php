<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/functions/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_name('originalshargh_session');
    session_start();
}

$allowedRoles = ['admin', 'manager', 'administrator', 'superadmin', 'owner'];
$routes = [
    '/admin',
    '/admin/dashboard',
    '/admin/users',
    '/admin/orders',
    '/admin/products',
    '/admin/services',
    '/admin/inventory',
    '/admin/suppliers',
    '/admin/articles',
    '/admin/ai',
];

function resetAuthState(): void
{
    $_SESSION = [];
}

function sessionState(string $state): void
{
    resetAuthState();

    switch ($state) {
        case 'NO_SESSION':
            break;
        case 'CUSTOMER':
            $_SESSION['customer_id'] = 42;
            $_SESSION['customer_name'] = 'Customer A';
            break;
        case 'MANAGER':
            $_SESSION['admin'] = [
                'id' => 7,
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'role' => 'manager',
            ];
            break;
        case 'ADMIN':
            $_SESSION['admin'] = [
                'id' => 8,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ];
            break;
        case 'UNKNOWN_ROLE':
            $_SESSION['admin'] = [
                'id' => 9,
                'name' => 'Unknown',
                'email' => 'unknown@example.com',
                'role' => 'ghost-role',
            ];
            break;
        default:
            throw new InvalidArgumentException('Unknown auth state: ' . $state);
    }
}

function evaluateAdminAccess(string $state): string
{
    sessionState($state);
    return isAdmin() ? 'ALLOWED' : 'DENIED';
}

function routeAccessFromState(string $state): array
{
    global $routes;
    $result = [];

    foreach ($routes as $route) {
        sessionState($state);
        $allowed = false;
        if (isset($_SESSION['admin'])) {
            $role = strtolower((string) ($_SESSION['admin']['role'] ?? ''));
            $allowed = in_array($role, ['admin', 'manager', 'administrator', 'superadmin', 'owner'], true);
        }
        $result[$route] = $allowed ? 'ALLOWED' : 'DENIED';
    }

    return $result;
}

echo "ADMIN_SESSION_CONSTRUCTION=AdminController::login() sets session admin data after allowed-role validation.\n";
echo "ALLOWED_ADMIN_ROLES=" . implode(',', $allowedRoles) . "\n";

echo "\nSTATE_CHECK\n";
foreach (['NO_SESSION', 'CUSTOMER', 'MANAGER', 'ADMIN', 'UNKNOWN_ROLE'] as $state) {
    echo $state . '_ADMIN_ACCESS=' . evaluateAdminAccess($state) . "\n";
}

echo "\nROUTE_MATRIX\n";
foreach (['NO_SESSION', 'CUSTOMER', 'MANAGER', 'ADMIN', 'UNKNOWN_ROLE'] as $state) {
    $checks = routeAccessFromState($state);
    echo $state . ':';
    foreach ($checks as $route => $status) {
        echo ' ' . $route . '=' . $status;
    }
    echo "\n";
}

$customerAccess = evaluateAdminAccess('CUSTOMER');
$unknownAccess = evaluateAdminAccess('UNKNOWN_ROLE');
$managerAccess = evaluateAdminAccess('MANAGER');
$adminAccess = evaluateAdminAccess('ADMIN');

echo "\nSUMMARY\n";
echo "CUSTOMER_ADMIN_ACCESS=" . $customerAccess . "\n";
echo "UNKNOWN_ROLE_ADMIN_ACCESS=" . $unknownAccess . "\n";
echo "MANAGER_ADMIN_ACCESS=" . $managerAccess . "\n";
echo "ADMIN_ADMIN_ACCESS=" . $adminAccess . "\n";

$fail = ($customerAccess !== 'DENIED') || ($unknownAccess !== 'DENIED') || ($managerAccess !== 'ALLOWED') || ($adminAccess !== 'ALLOWED');
exit($fail ? 1 : 0);
