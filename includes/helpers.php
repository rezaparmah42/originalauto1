<?php

// Ensure canonical global helpers are loaded from the app functions file
// so standalone legacy includes get the same implementations as the
// front-controller bootstrap. Do not redefine helpers if the app-level
// functions file is already authoritative.
if (file_exists(__DIR__ . '/../app/functions/functions.php')) {
    require_once __DIR__ . '/../app/functions/functions.php';
}

// Ensure core Database alias is available for legacy code that expects
// a global `Database` class name.
if (file_exists(__DIR__ . '/../app/Core/Database.php')) {
    require_once __DIR__ . '/../app/Core/Database.php';
    if (!class_exists('Database') && class_exists('App\\Core\\Database')) {
        class_alias('App\\Core\\Database', 'Database');
    }
}

// Backwards-compatible minimal fallbacks only if the app helpers are not
// already present (this keeps behavior stable for legacy scripts).
if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
}
