<?php
/**
 * Enhanced Smoke Test - Production Health Check v2
 * Original Shargh Application
 */

define('PROJECT_ACCESS', true);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

class DetailedSmokeTest {
    private $results = [];
    private $db = null;

    public function __construct() {
        $this->results = [
            'timestamp' => date('Y-m-d H:i:s'),
            'environment' => APP_ENV,
            'tests' => [],
        ];
    }

    public function run() {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║  ORIGINAL SHARGH - PRODUCTION SMOKE TEST v2                ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";

        echo "Environment: " . APP_ENV . "\n";
        echo "Started: " . $this->results['timestamp'] . "\n";
        echo "Site URL: " . SITE_URL . "\n\n";

        $this->testApplicationBoot();
        $this->testDatabaseConnection();
        $this->testDatabaseSchema();
        $this->testRouting();
        $this->testViews();
        $this->testSecurity();
        $this->testFileStructure();

        $this->printDetailedReport();
    }

    private function testApplicationBoot() {
        echo "━━━ 1. APPLICATION BOOTSTRAP ━━━\n";

        $tests = [
            'SITE_NAME' => SITE_NAME,
            'SITE_URL' => SITE_URL,
            'DB_HOST' => DB_HOST,
            'DB_NAME' => DB_NAME,
            'APP_ENV' => APP_ENV,
            'DEBUG' => DEBUG ? 'ON' : 'OFF',
            'UPLOAD_URL' => UPLOAD_URL,
            'UPLOAD_PATH' => UPLOAD_PATH,
        ];

        foreach ($tests as $key => $value) {
            if (!empty($value) || $value === false) {
                echo "  ✓ $key = " . ($value === true ? 'true' : ($value === false ? 'false' : $value)) . "\n";
                $this->results['tests'][] = ['test' => "Config: $key", 'status' => 'pass'];
            }
        }

        if (!is_dir(UPLOAD_PATH)) {
            echo "  ⚠ Upload path does not exist: " . UPLOAD_PATH . "\n";
            $this->results['tests'][] = ['test' => 'Upload directory exists', 'status' => 'warn'];
        } else {
            echo "  ✓ Upload directory exists\n";
            $this->results['tests'][] = ['test' => 'Upload directory exists', 'status' => 'pass'];
        }

        echo "\n";
    }

    private function testDatabaseConnection() {
        echo "━━━ 2. DATABASE CONNECTION ━━━\n";

        try {
            require_once __DIR__ . '/app/Core/Database.php';
            $this->db = \App\Core\Database::connect();

            if ($this->db) {
                echo "  ✓ PDO connection established\n";
                $this->results['tests'][] = ['test' => 'Database connection', 'status' => 'pass'];

                $result = $this->db->query("SELECT 1");
                if ($result) {
                    echo "  ✓ Database query test successful\n";
                    $this->results['tests'][] = ['test' => 'Database query', 'status' => 'pass'];
                }
            } else {
                echo "  ✗ Database connection failed\n";
                $this->results['tests'][] = ['test' => 'Database connection', 'status' => 'fail'];
            }
        } catch (\PDOException $e) {
            echo "  ✗ PDO Error: " . $e->getMessage() . "\n";
            $this->results['tests'][] = ['test' => 'Database connection', 'status' => 'fail', 'error' => $e->getMessage()];
        } catch (Exception $e) {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
            $this->results['tests'][] = ['test' => 'Database connection', 'status' => 'fail', 'error' => $e->getMessage()];
        }

        echo "\n";
    }

    private function testDatabaseSchema() {
        echo "━━━ 3. DATABASE SCHEMA ━━━\n";

        if (!$this->db) {
            echo "  ⚠ Skipped: No database connection\n\n";
            $this->results['tests'][] = ['test' => 'Database schema', 'status' => 'skip'];
            return;
        }

        try {
            $tables = $this->getActualTables();
            echo "  Tables in database: " . count($tables) . "\n";

            if (empty($tables)) {
                echo "  ⚠ Database is empty\n";
                echo "  → Run database/production_schema.sql to initialize\n";
                $this->results['tests'][] = ['test' => 'Database tables exist', 'status' => 'warn'];
            } else {
                echo "  ✓ Database contains tables:\n";
                foreach ($tables as $table) {
                    echo "    - $table\n";
                }
                $this->results['tests'][] = ['test' => 'Database tables exist', 'status' => 'pass'];
            }
        } catch (Exception $e) {
            echo "  ✗ Error checking schema: " . $e->getMessage() . "\n";
            $this->results['tests'][] = ['test' => 'Database schema', 'status' => 'fail'];
        }

        echo "\n";
    }

    private function testRouting() {
        echo "━━━ 4. ROUTING CONFIGURATION ━━━\n";

        $routesFile = __DIR__ . '/app/routes.php';
        if (file_exists($routesFile)) {
            echo "  ✓ Routes file exists\n";
            $this->results['tests'][] = ['test' => 'Routes file', 'status' => 'pass'];

            $content = file_get_contents($routesFile);
            $routeCount = substr_count($content, '$router->');
            echo "  ✓ Defined routes: $routeCount\n";
            $this->results['tests'][] = ['test' => 'Routes defined', 'status' => 'pass'];

            $expectedRoutes = ['/', '/login', '/register', '/admin/login', '/admin/dashboard', '/booking', '/services', '/shop'];
            foreach ($expectedRoutes as $route) {
                if (strpos($content, "'$route'") !== false || strpos($content, "\"$route\"") !== false) {
                    echo "  ✓ Route registered: $route\n";
                    $this->results['tests'][] = ['test' => "Route: $route", 'status' => 'pass'];
                } else {
                    echo "  ⚠ Route not found: $route\n";
                    $this->results['tests'][] = ['test' => "Route: $route", 'status' => 'warn'];
                }
            }
        } else {
            echo "  ✗ Routes file not found\n";
            $this->results['tests'][] = ['test' => 'Routes file', 'status' => 'fail'];
        }

        echo "\n";
    }

    private function testViews() {
        echo "━━━ 5. VIEW FILES ━━━\n";

        $views = [
            'home' => 'app/Views/home/index.php',
            'login' => 'app/Views/account/login.php',
            'register' => 'app/Views/account/register.php',
            'admin_login' => 'app/Views/admin/login.php',
            'booking' => 'app/Views/booking/create.php',
            'services' => 'app/Views/services/index.php',
            'products' => 'app/Views/shop/index.php',
        ];

        foreach ($views as $name => $file) {
            $path = __DIR__ . '/' . $file;
            if (file_exists($path)) {
                echo "  ✓ View exists: $name\n";
                $this->results['tests'][] = ['test' => "View: $name", 'status' => 'pass'];
            } else {
                echo "  ⚠ View missing: $name ($file)\n";
                $this->results['tests'][] = ['test' => "View: $name", 'status' => 'warn'];
            }
        }

        echo "\n";
    }

    private function testSecurity() {
        echo "━━━ 6. SECURITY CONFIGURATION ━━━\n";

        $tests = [
            'CSRF token function' => function_exists('csrf_token'),
            'CSRF field function' => function_exists('csrf_field'),
            'Verify CSRF function' => function_exists('verify_csrf'),
            'HTML escape function (e)' => function_exists('e'),
            'Password verification available' => function_exists('password_verify'),
            'Session helpers present' => function_exists('getSession') && function_exists('setSession'),
            'Auth helpers present' => function_exists('isLoggedIn') && function_exists('isCustomerLoggedIn'),
        ];

        foreach ($tests as $test => $result) {
            if ($result) {
                echo "  ✓ $test\n";
                $this->results['tests'][] = ['test' => "Security: $test", 'status' => 'pass'];
            } else {
                echo "  ✗ $test\n";
                $this->results['tests'][] = ['test' => "Security: $test", 'status' => 'fail'];
            }
        }

        if (!DEBUG) {
            echo "  ✓ DEBUG mode is OFF (production-ready)\n";
            $this->results['tests'][] = ['test' => 'DEBUG off', 'status' => 'pass'];
        } else {
            echo "  ⚠ DEBUG mode is ON (not production-ready)\n";
            $this->results['tests'][] = ['test' => 'DEBUG off', 'status' => 'warn'];
        }

        echo "\n";
    }

    private function testFileStructure() {
        echo "━━━ 7. FILE STRUCTURE ━━━\n";

        $dirs = [
            'config' => 'app/config directory',
            'controllers' => 'app/Controllers directory',
            'models' => 'app/Models directory',
            'views' => 'app/Views directory',
            'core' => 'app/Core directory',
            'public' => 'public directory',
            'database' => 'database directory',
        ];

        foreach ($dirs as $dir => $desc) {
            $path = __DIR__ . '/app/' . str_replace('app/', '', $dir);
            if (str_starts_with($dir, 'public') || str_starts_with($dir, 'database')) {
                $path = __DIR__ . '/' . $dir;
            }
            if (is_dir($path)) {
                echo "  ✓ $desc exists\n";
                $this->results['tests'][] = ['test' => "Directory: $desc", 'status' => 'pass'];
            } else {
                echo "  ⚠ $desc missing\n";
                $this->results['tests'][] = ['test' => "Directory: $desc", 'status' => 'warn'];
            }
        }

        echo "\n";
    }

    private function getActualTables() {
        try {
            $stmt = $this->db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "' ORDER BY TABLE_NAME");
            $tables = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tables[] = $row['TABLE_NAME'];
            }
            return $tables;
        } catch (Exception $e) {
            return [];
        }
    }

    private function printDetailedReport() {
        echo "\n╔════════════════════════════════════════════════════════════╗\n";
        echo "║  SMOKE TEST REPORT                                         ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n\n";

        $passed = count(array_filter($this->results['tests'], fn($t) => $t['status'] === 'pass'));
        $failed = count(array_filter($this->results['tests'], fn($t) => $t['status'] === 'fail'));
        $warned = count(array_filter($this->results['tests'], fn($t) => $t['status'] === 'warn'));
        $skipped = count(array_filter($this->results['tests'], fn($t) => $t['status'] === 'skip'));
        $total = count($this->results['tests']);

        echo "Summary:\n";
        echo "  ✓ Passed:  $passed\n";
        echo "  ⚠ Warned:  $warned\n";
        echo "  ✗ Failed:  $failed\n";
        echo "  ⊘ Skipped: $skipped\n";
        echo "  ─────────────\n";
        echo "  Total:     $total\n\n";

        $passRate = $total > 0 ? round(($passed / $total) * 100) : 0;
        echo "Pass Rate: $passRate%\n\n";

        if ($failed === 0 && $warned === 0) {
            echo "Status: ✓ HEALTHY\n";
        } elseif ($failed === 0) {
            echo "Status: ⚠ DEGRADED (warnings present)\n";
        } else {
            echo "Status: ✗ UNHEALTHY (failures detected)\n";
        }

        echo "\n━━━ Critical Issues (if any) ━━━\n\n";

        $criticalTests = array_filter($this->results['tests'], fn($t) => $t['status'] === 'fail');
        if (empty($criticalTests)) {
            echo "None detected.\n";
        } else {
            foreach ($criticalTests as $test) {
                echo "  ✗ " . $test['test'];
                if (isset($test['error'])) {
                    echo " — " . $test['error'];
                }
                echo "\n";
            }
        }

        echo "\n━━━ Recommended Actions ━━━\n\n";

        $failures = array_filter($this->results['tests'], fn($t) => $t['status'] === 'fail');
        $warnings = array_filter($this->results['tests'], fn($t) => $t['status'] === 'warn');

        if (!empty($failures)) {
            echo "Required Fixes:\n";
            foreach ($failures as $test) {
                echo "  • " . $test['test'] . "\n";
            }
            echo "\n";
        }

        if (!empty($warnings)) {
            echo "Recommended Improvements:\n";
            foreach ($warnings as $test) {
                echo "  • " . $test['test'] . "\n";
            }
            echo "\n";
        }

        if (count($this->results['tests'], COUNT_RECURSIVE) > 0) {
            echo "Next Steps:\n";
            echo "  1. Review all failed tests\n";
            echo "  2. If database is empty, run: mysql -u root original_east < database/production_schema.sql\n";
            echo "  3. Create upload directory: mkdir -p public/uploads && chmod 755 public/uploads\n";
            echo "  4. Test key routes by accessing them via browser\n";
            echo "  5. Verify authentication flows work correctly\n";
            echo "\n";
        }

        echo "Report Generated: " . date('Y-m-d H:i:s') . "\n";
    }
}

$test = new DetailedSmokeTest();
$test->run();
