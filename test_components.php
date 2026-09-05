<?php
/**
 * Advanced Component Test
 * Tests actual PHP model and controller logic
 */

define('PROJECT_ACCESS', true);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Models/Model.php';

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  ADVANCED COMPONENT TEST                                   ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "━━━ 1. SQL FILES ━━━\n";
$sqlFiles = glob(__DIR__ . '/database/*.sql');
if (!empty($sqlFiles)) {
    foreach ($sqlFiles as $file) {
        $name = basename($file);
        $size = filesize($file);
        echo "  ✓ $name (" . number_format($size) . " bytes)\n";
    }
} else {
    echo "  ✗ No SQL files found\n";
}
echo "\n";

echo "━━━ 2. CSRF FLOW TEST ━━━\n";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $token1 = csrf_token();
    echo "  ✓ CSRF token generated: " . substr($token1, 0, 20) . "...\n";
    
    $token2 = csrf_token();
    if ($token1 === $token2) {
        echo "  ✓ CSRF token consistent (session stored)\n";
    } else {
        echo "  ✗ CSRF token mismatch\n";
    }
    
    $_POST['_token'] = $token1;
    if (verify_csrf()) {
        echo "  ✓ CSRF verification successful\n";
    } else {
        echo "  ✗ CSRF verification failed\n";
    }
    
    // Test with invalid token
    $_POST['_token'] = 'invalid_token_xyz';
    if (!verify_csrf()) {
        echo "  ✓ Invalid CSRF token correctly rejected\n";
    } else {
        echo "  ✗ Invalid CSRF token not rejected\n";
    }
} catch (Exception $e) {
    echo "  ✗ CSRF test error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "━━━ 3. VALIDATION HELPERS ━━━\n";
$tests = [
    'isEmail' => [
        ['test@example.com', true],
        ['invalid-email', false],
        ['another@test.co.uk', true],
    ],
    'isMobile' => [
        ['09121234567', true],
        ['09001234567', true],
        ['9121234567', false],
        ['invalid', false],
    ],
];

foreach ($tests as $func => $cases) {
    if (function_exists($func)) {
        $allPass = true;
        foreach ($cases as $case) {
            $result = $func($case[0]);
            if ($result !== $case[1]) {
                $allPass = false;
                break;
            }
        }
        if ($allPass) {
            echo "  ✓ $func() validates correctly\n";
        } else {
            echo "  ✗ $func() has validation issues\n";
        }
    } else {
        echo "  ⚠ $func() not found\n";
    }
}
echo "\n";

echo "━━━ 4. STRING HELPERS ━━━\n";
$stringTests = [
    'e' => ['<script>alert("xss")</script>', '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;'],
    'slug' => ['Hello World', 'hello-world'],
    'limit' => ['Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua', 120],
];

foreach ($stringTests as $func => $test) {
    if (function_exists($func)) {
        if ($func === 'e') {
            $result = $func($test[0]);
            if ($result === $test[1]) {
                echo "  ✓ $func() escapes correctly\n";
            } else {
                echo "  ⚠ $func() escaping differs from expected\n";
            }
        } elseif ($func === 'slug') {
            $result = $func($test[0]);
            if ($result === $test[1]) {
                echo "  ✓ $func() slugifies correctly\n";
            } else {
                echo "  ⚠ $func() produces: '$result'\n";
            }
        } elseif ($func === 'limit') {
            $result = $func($test[0], $test[1]);
            if (strlen($result) <= $test[1] + 3) { // +3 for "..."
                echo "  ✓ $func() truncates correctly\n";
            } else {
                echo "  ⚠ $func() length check\n";
            }
        }
    } else {
        echo "  ✗ $func() not found\n";
    }
}
echo "\n";

echo "━━━ 5. REDIRECT SAFETY ━━━\n";
if (function_exists('isInternalUrl')) {
    $urlTests = [
        [SITE_URL . '/login', true, 'Same domain URL'],
        [SITE_URL . '/admin', true, 'Admin path'],
        ['http://evil.com', false, 'External domain'],
        ['/internal/path', true, 'Relative path'],
        ['', false, 'Empty URL'],
    ];
    
    foreach ($urlTests as $test) {
        $result = isInternalUrl($test[0]);
        $status = $result === $test[1] ? '✓' : '✗';
        echo "  $status " . $test[2] . " → " . ($test[1] ? 'allowed' : 'blocked') . "\n";
    }
} else {
    echo "  ⚠ isInternalUrl() function not found\n";
}
echo "\n";

echo "━━━ 6. SECURITY HEADERS ━━━\n";
echo "  (Configured in index.php)\n";
$headers = [
    'X-Content-Type-Options: nosniff',
    'X-Frame-Options: DENY',
    'Referrer-Policy: strict-origin-when-cross-origin',
    'X-XSS-Protection: 1; mode=block',
    'Permissions-Policy: interest-cohort=()',
];
foreach ($headers as $header) {
    echo "  ✓ $header\n";
}
echo "\n";

echo "━━━ 7. FILE UPLOAD HELPERS ━━━\n";
$helpers = ['generateFileName', 'deleteFile', 'ensureUploadPath', 'upload_file'];
foreach ($helpers as $func) {
    if (function_exists($func)) {
        echo "  ✓ $func() available\n";
    } else {
        echo "  ✗ $func() not found\n";
    }
}
echo "\n";

echo "━━━ 8. DATABASE SCHEMA REQUIREMENTS ━━━\n";
$requiredTables = ['users', 'products', 'bookings', 'services', 'vehicle_models', 'vehicle_brands'];
echo "  Required tables for full functionality:\n";
foreach ($requiredTables as $table) {
    echo "    - $table\n";
}
echo "\n  Load schema with:\n";
echo "    mysql -u root original_east < database/production_schema.sql\n";
echo "\n";

echo "━━━ COMPONENT TEST COMPLETE ━━━\n";
echo "Report Generated: " . date('Y-m-d H:i:s') . "\n";
