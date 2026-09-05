<?php
// Compatibility fallback: ensure `e()` is available when meta is included
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Minimal meta output to keep legacy includes/header.php happy
// Avoid using settings or DB here to prevent side-effects during early bootstrap
// Provide minimal, safe fallbacks for a few commonly used helpers so that
// header/meta can be included by legacy entrypoints that may not have
// performed a full bootstrap yet. These fallbacks are intentionally
// side-effect free and do NOT perform DB access.
if (!function_exists('ensureSessionStarted')) {
    function ensureSessionStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
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

if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        // Prefer existing implementations if available
        if (function_exists('isAdmin') && isAdmin()) {
            return true;
        }
        if (function_exists('isCustomerLoggedIn')) {
            return isCustomerLoggedIn();
        }
        ensureSessionStarted();
        return isset($_SESSION['customer_id']);
    }
}

if (!function_exists('currentCustomerName')) {
    function currentCustomerName()
    {
        ensureSessionStarted();
        return $_SESSION['customer_name'] ?? 'کاربر';
    }
}
?>