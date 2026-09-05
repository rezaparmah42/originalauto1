<?php
/**
 * Smoke Test - Production Health Check
 * Original Shargh Application
 */

define('PROJECT_ACCESS', true);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

class SmokeTest {
    private $results = [
        'passed' => [],
        'failed' => [],
        'critical' => [],
        'warnings' => []
    ];
    
    private $db = null;

    public function __construct() {
        $this->results['timestamp'] = date('Y-m-d H:i:s');
        $this->results['environment'] = APP_ENV;
    }

    public function run() {
        echo "=== ORIGINAL SHARGH SMOKE TEST ===\n";
        echo "Started: " . $this->results['timestamp'] . "\n";
        echo "Environment: " . APP_ENV . "\n\n";

        $this->testApplicationBoot();
        $this->testDatabaseConnection();
        $this->testDatabaseSchema();
        $this->testConfigSettings();
        $this->testSecuritySettings();
        $this->testCoreHelpers();
        $this->testControllers();
        $this->testModels();

        $this->printReport();
    }

    private function testApplicationBoot() {
        echo "1. APPLICATION BOOT\n";
        echo str_repeat("-", 50) . "\n";

        try {
            $this->passed("Config constants defined", defined('SITE_NAME') && defined('SITE_URL'));
            $this->passed("Upload paths defined", defined('UPLOAD_URL') && defined('UPLOAD_PATH'));
            $this->passed("Database constants defined", defined('DB_HOST') && defined('DB_NAME'));
            $this->passed("Debug mode setting loaded", defined('DEBUG'));
            
            if (!is_dir(UPLOAD_PATH)) {
                $this->warning("Upload directory does not exist: " . UPLOAD_PATH);
            } else {
                $this->passed("Upload directory exists", true);
            }

            if (!is_writable(UPLOAD_PATH)) {
                $this->warning("Upload directory not writable");
            } else {
                $this->passed("Upload directory writable", true);
            }
        } catch (Exception $e) {
            $this->failed("Application boot", $e->getMessage());
        }
        echo "\n";
    }

    private function testDatabaseConnection() {
        echo "2. DATABASE CONNECTION\n";
        echo str_repeat("-", 50) . "\n";

        try {
            require_once __DIR__ . '/app/Core/Database.php';
            $this->db = \App\Core\Database::connect();
            
            if ($this->db === null) {
                $this->critical("Database connection failed", "Database::connect() returned null");
                return;
            }
            
            $this->passed("PDO connection established", true);

            $stmt = $this->db->query("SELECT 1");
            if ($stmt !== false) {
                $this->passed("Database query execution", true);
            } else {
                $this->critical("Database query failed", "SELECT 1 returned false");
            }
        } catch (\PDOException $e) {
            $this->critical("Database connection error", $e->getMessage());
        } catch (Exception $e) {
            $this->critical("Database error", $e->getMessage());
        }
        echo "\n";
    }

    private function testDatabaseSchema() {
        echo "3. DATABASE SCHEMA\n";
        echo str_repeat("-", 50) . "\n";

        if (!$this->db) {
            $this->critical("Database schema check skipped", "No database connection");
            return;
        }

        $requiredTables = [
            'users' => ['id', 'phone', 'email', 'password', 'name', 'role'],
            'products' => ['id', 'title_fa', 'title_en', 'slug', 'description_fa', 'description_en', 'price', 'stock', 'status'],
            'bookings' => ['id', 'user_id', 'vehicle_id', 'service_id', 'problem', 'booking_date', 'status'],
            'services' => ['id', 'title_fa', 'title_en', 'slug', 'description_fa', 'description_en'],
            'vehicle_models' => ['id', 'brand_id', 'name_fa', 'slug', 'year_from', 'year_to'],
        ];

        $actualTables = $this->getActualTables();

        foreach ($requiredTables as $table => $columns) {
            if (in_array($table, $actualTables)) {
                $this->passed("Table exists: $table", true);

                try {
                    $columnsInfo = $this->getTableColumns($table);
                    $missingColumns = [];
                    foreach ($columns as $col) {
                        if (!isset($columnsInfo[$col])) {
                            $missingColumns[] = $col;
                        }
                    }

                    if (empty($missingColumns)) {
                        $this->passed("  → Required columns in $table", true);
                    } else {
                        $this->failed("  → Missing columns in $table", implode(', ', $missingColumns));
                    }
                } catch (Exception $e) {
                    $this->failed("  → Cannot inspect $table columns", $e->getMessage());
                }
            } else {
                $this->critical("Table missing: $table", "Table not found in database");
            }
        }

        $tables = implode(", ", $actualTables);
        echo "\nActual tables in database:\n$tables\n";
        echo "\n";
    }

    private function testConfigSettings() {
        echo "4. CONFIGURATION SETTINGS\n";
        echo str_repeat("-", 50) . "\n";

        $this->passed("SITE_URL configured", !empty(SITE_URL));
        $this->passed("SITE_NAME configured", !empty(SITE_NAME));
        $this->passed("Database host configured", !empty(DB_HOST));
        $this->passed("Database name configured", !empty(DB_NAME));
        $this->passed("Upload URL configured", !empty(UPLOAD_URL));

        if (DEBUG) {
            $this->warning("DEBUG mode is ON (should be OFF in production)");
        } else {
            $this->passed("DEBUG mode OFF", true);
        }

        echo "\n";
    }

    private function testSecuritySettings() {
        echo "5. SECURITY SETTINGS\n";
        echo str_repeat("-", 50) . "\n";

        $this->testCSRFImplementation();
        $this->testSessionConfig();
        $this->testAuthHelpers();

        echo "\n";
    }

    private function testCSRFImplementation() {
        if (!function_exists('csrf_token')) {
            $this->failed("CSRF token function", "Function not found");
            return;
        }
        
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        try {
            $token1 = csrf_token();
            $this->passed("CSRF token generation", !empty($token1));

            if (function_exists('csrf_field')) {
                $field = csrf_field();
                $this->passed("CSRF field generation", strpos($field, '_token') !== false);
            }
        } catch (Exception $e) {
            $this->failed("CSRF implementation", $e->getMessage());
        }
    }

    private function testSessionConfig() {
        $cookieSetting = ini_get('session.cookie_httponly');
        if ($cookieSetting == '1') {
            $this->passed("Session HttpOnly cookie", true);
        } else {
            $this->warning("Session HttpOnly cookie not set");
        }

        $sameSite = ini_get('session.cookie_samesite');
        if (!empty($sameSite)) {
            $this->passed("Session SameSite setting", true);
        } else {
            $this->warning("Session SameSite not configured");
        }

        $useOnlyCookies = ini_get('session.use_only_cookies');
        if ($useOnlyCookies == '1') {
            $this->passed("Session use_only_cookies", true);
        } else {
            $this->warning("Session use_only_cookies not enabled");
        }
    }

    private function testAuthHelpers() {
        $helpers = ['isLoggedIn', 'isCustomerLoggedIn', 'requireLogin', 'requireCustomer'];
        
        foreach ($helpers as $func) {
            if (function_exists($func)) {
                $this->passed("Auth helper: $func", true);
            } else {
                $this->failed("Auth helper missing: $func", "Function not defined");
            }
        }
    }

    private function testCoreHelpers() {
        echo "6. CORE HELPERS\n";
        echo str_repeat("-", 50) . "\n";

        $helpers = [
            'e' => 'HTML escape',
            'redirect' => 'Redirect',
            'back' => 'Back redirect',
            'asset' => 'Asset path',
            'upload' => 'Upload path',
            'slug' => 'Slug generation',
            'price' => 'Price formatting',
            'getSession' => 'Session getter',
            'setSession' => 'Session setter',
            'isPost' => 'POST check',
            'isGet' => 'GET check',
            'success' => 'Success message',
            'error' => 'Error message',
        ];

        foreach ($helpers as $func => $desc) {
            if (function_exists($func)) {
                $this->passed("Helper: $desc", true);
            } else {
                $this->failed("Helper missing: $desc", "Function $func not defined");
            }
        }

        echo "\n";
    }

    private function testControllers() {
        echo "7. CONTROLLER FILES\n";
        echo str_repeat("-", 50) . "\n";

        $controllers = [
            'AccountController' => 'app/Controllers/AccountController.php',
            'AdminController' => 'app/Controllers/AdminController.php',
            'BookingController' => 'app/Controllers/BookingController.php',
            'ProductController' => 'app/Controllers/ProductController.php',
            'ServiceController' => 'app/Controllers/ServiceController.php',
        ];

        foreach ($controllers as $class => $file) {
            $path = __DIR__ . '/' . $file;
            if (file_exists($path)) {
                $this->passed("Controller file exists: $class", true);
            } else {
                $this->failed("Controller file missing: $class", "File not found: $file");
            }
        }

        echo "\n";
    }

    private function testModels() {
        echo "8. MODEL FILES\n";
        echo str_repeat("-", 50) . "\n";

        $models = [
            'User' => 'app/Models/User.php',
            'Admin' => 'app/Models/Admin.php',
            'Booking' => 'app/Models/Booking.php',
            'Product' => 'app/Models/Product.php',
            'Service' => 'app/Models/Service.php',
        ];

        foreach ($models as $class => $file) {
            $path = __DIR__ . '/' . $file;
            if (file_exists($path)) {
                $this->passed("Model file exists: $class", true);
            } else {
                $this->warning("Model file may be missing: $class (optional)");
            }
        }

        echo "\n";
    }

    private function getActualTables() {
        try {
            $stmt = $this->db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "'");
            $tables = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tables[] = $row['TABLE_NAME'];
            }
            return $tables;
        } catch (Exception $e) {
            return [];
        }
    }

    private function getTableColumns($table) {
        try {
            $stmt = $this->db->query("DESCRIBE " . $table);
            $columns = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $columns[$row['Field']] = $row['Type'];
            }
            return $columns;
        } catch (Exception $e) {
            return [];
        }
    }

    private function passed($test, $result) {
        if ($result) {
            echo "✓ $test\n";
            $this->results['passed'][] = $test;
        }
    }

    private function failed($test, $reason = "") {
        echo "✗ $test";
        if ($reason) {
            echo " — $reason";
        }
        echo "\n";
        $this->results['failed'][] = ['test' => $test, 'reason' => $reason];
    }

    private function critical($test, $reason = "") {
        echo "⚠ CRITICAL: $test";
        if ($reason) {
            echo " — $reason";
        }
        echo "\n";
        $this->results['critical'][] = ['test' => $test, 'reason' => $reason];
    }

    private function warning($message) {
        echo "⚠ WARNING: $message\n";
        $this->results['warnings'][] = $message;
    }

    private function printReport() {
        echo "\n";
        echo "=== SMOKE TEST REPORT ===\n";
        echo "Completed: " . date('Y-m-d H:i:s') . "\n\n";

        echo "PASSED TESTS: " . count($this->results['passed']) . "\n";
        foreach ($this->results['passed'] as $test) {
            echo "  ✓ $test\n";
        }

        if (!empty($this->results['failed'])) {
            echo "\nFAILED TESTS: " . count($this->results['failed']) . "\n";
            foreach ($this->results['failed'] as $item) {
                echo "  ✗ " . $item['test'];
                if ($item['reason']) echo " — " . $item['reason'];
                echo "\n";
            }
        }

        if (!empty($this->results['critical'])) {
            echo "\nCRITICAL ISSUES: " . count($this->results['critical']) . "\n";
            foreach ($this->results['critical'] as $item) {
                echo "  ⚠ " . $item['test'];
                if ($item['reason']) echo " — " . $item['reason'];
                echo "\n";
            }
        }

        if (!empty($this->results['warnings'])) {
            echo "\nWARNINGS: " . count($this->results['warnings']) . "\n";
            foreach ($this->results['warnings'] as $warn) {
                echo "  ⚠ $warn\n";
            }
        }

        echo "\n=== SUMMARY ===\n";
        $totalTests = count($this->results['passed']) + count($this->results['failed']) + count($this->results['critical']);
        $passRate = $totalTests > 0 ? round((count($this->results['passed']) / $totalTests) * 100) : 0;
        echo "Pass Rate: $passRate% ($totalTests tests)\n";
        echo "Status: " . ($passRate >= 90 ? "✓ HEALTHY" : ($passRate >= 70 ? "⚠ DEGRADED" : "✗ UNHEALTHY")) . "\n";
    }
}

$test = new SmokeTest();
$test->run();
