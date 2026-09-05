<?php
/**
 * اورجینال شرق - Compatibility Helper Loader
 * Stage 1 core stabilization
 */
if (!defined('PROJECT_ACCESS')) {
    exit;
}

// Minimal PSR-4-like autoloader for `App\` namespace so standalone pages
// that include this file can access `App\` classes (e.g. App\Core\Database).
if (!function_exists('originalshargh_autoload_register')) {
    spl_autoload_register(function($class){
        $class = str_replace('App\\', '', $class);
        $class = str_replace('\\', '/', $class);
        $file = __DIR__ . '/../app/' . $class . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });
}

// Ensure the core Database class is available for legacy callers that expect a
// global `Database` alias (some entrypoints include this file directly).
if (file_exists(__DIR__ . '/../app/Core/Database.php')) {
    require_once __DIR__ . '/../app/Core/Database.php';
    if (!class_exists('Database') && class_exists('App\\Core\\Database')) {
        class_alias('App\\Core\\Database', 'Database');
    }
}

    if (!function_exists('brand_match_sql')) {
        /**
         * Return a SQL clause to match a brand identifier against available name
         * columns. Produces a clause containing $placeholders placeholders so it
         * can be used in place of existing conditions that compared slug+names.
         *
         * Usage: brand_match_sql('vb', 4) -> "(LOWER(vb.name_fa)=LOWER(?) OR LOWER(vb.name_en)=LOWER(?) OR LOWER(vb.name)=LOWER(?) OR LOWER(vb.name)=LOWER(?))"
         */
        function brand_match_sql($alias = 'vb', $placeholders = 4)
        {
            $alias = trim((string)$alias);
            if ($alias === '') $alias = 'vb';
            // base candidates: name_fa, name_en, name
            $candidates = ["{$alias}.name_fa", "{$alias}.name_en", "{$alias}.name"];
            $parts = [];
            for ($i = 0; $i < $placeholders; $i++) {
                $col = $candidates[min($i, count($candidates) - 1)];
                $parts[] = "LOWER({$col}) = LOWER(?)";
            }
            return '(' . implode(' OR ', $parts) . ')';
        }
    }

require_once __DIR__ . '/../app/functions/functions.php';
// Legacy helpers for pages that include `includes/functions.php` directly
if (file_exists(__DIR__ . '/helpers.php')) {
    require_once __DIR__ . '/helpers.php';
}


/*
|--------------------------------------------------------------------------
| Admin Compatibility Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('ensureSessionStarted')) {
    function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        ensureSessionStarted();
        if (!isset($_SESSION['admin'])) {
            return false;
        }

        $role = strtolower((string) ($_SESSION['admin']['role'] ?? ''));
        return in_array($role, ['admin', 'manager', 'administrator', 'superadmin', 'owner'], true);
    }
}

if (!function_exists('requireAdmin')) {
    function requireAdmin()
    {
        ensureSessionStarted();
        if (!isAdmin()) {
            if (isset($_SESSION['admin'])) {
                unset($_SESSION['admin']);
            }
            redirect(SITE_URL . '/admin/login');
        }
    }
}

if (!function_exists('requireLogin')) {
    function requireLogin()
    {
        ensureSessionStarted();

        if (isAdmin() || isCustomerLoggedIn()) {
            return;
        }

        $requestUri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));
        if ($requestUri !== '' && strpos($requestUri, '/admin') === 0) {
            redirect(SITE_URL . '/admin/login');
        }

        redirect(SITE_URL . '/login');
    }
}


if (!function_exists('clean')) {
    function clean($value)
    {
        return htmlspecialchars(
            trim((string)$value),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

/*
|--------------------------------------------------------------------------
| Customer Auth Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('ensureSessionStarted')) {
    function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

if (!function_exists('isCustomerLoggedIn')) {
    function isCustomerLoggedIn()
    {
        ensureSessionStarted();
        return isset($_SESSION['customer_id']);
    }
}

if (!function_exists('requireCustomer')) {
    function requireCustomer()
    {
        ensureSessionStarted();
        if (!isCustomerLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? SITE_URL;
            redirect(SITE_URL . '/login');
        }
    }
}

if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        ensureSessionStarted();
        if (function_exists('isAdmin') && isAdmin()) {
            return true;
        }

        if (function_exists('isCustomerLoggedIn') && isCustomerLoggedIn()) {
            return true;
        }

        return false;
    }
}

if (!function_exists('currentCustomerId')) {
    function currentCustomerId()
    {
        ensureSessionStarted();
        return $_SESSION['customer_id'] ?? null;
    }
}

if (!function_exists('currentCustomerName')) {
    function currentCustomerName()
    {
        ensureSessionStarted();
        return $_SESSION['customer_name'] ?? 'کاربر';
    }
}

/*
|--------------------------------------------------------------------------
| Repair Helpers
|--------------------------------------------------------------------------
*/

if (!function_exists('repairStatuses')) {
    function repairStatuses()
    {
        return [
            'received' => 'دریافت شد',
            'diagnosing' => 'در حال تشخیص',
            'waiting_parts' => 'انتظار قطعات',
            'repairing' => 'در حال تعمیر',
            'completed' => 'تکمیل شده',
            'delivered' => 'تحویل داده شد',
            'pending' => 'درخواست ثبت شد',
            'inspection' => 'بازرسی خودرو',
            'approved' => 'تأیید شده',
            'in_progress' => 'در حال تعمیر',
            'cancelled' => 'لغو شده',
        ];
    }
}

if (!function_exists('repairStatusLabel')) {
    function repairStatusLabel($status)
    {
        return repairStatuses()[$status] ?? $status;
    }
}

if (!function_exists('generateRepairCode')) {
    function generateRepairCode()
    {
        return 'RP-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}

if (!function_exists('generateInvoiceCode')) {
    function generateInvoiceCode()
    {
        return 'INV-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}
