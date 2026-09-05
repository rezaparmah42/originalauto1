<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Admin as AdminModel;
use App\Models\Booking as BookingModel;
use App\Models\Diagnostic as DiagnosticModel;
use App\Models\DiagnosticAI as DiagnosticAIModel;
use App\Models\Repair as RepairModel;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    private $adminModel;
    private $bookingModel;
    private $repairModel;
    private $diagnosticModel;
    private $diagnosticAIModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->bookingModel = new BookingModel();
        $this->repairModel = new RepairModel();
        $this->diagnosticModel = new DiagnosticModel();
        $this->diagnosticAIModel = new DiagnosticAIModel();
    }

    public function login()
    {
        ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر است. مجدداً تلاش کنید.');
                return $this->view('admin/login');
            }

            $login = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($login === '' || $password === '') {
                error('نام کاربری و رمز عبور لازم است.');
                return $this->view('admin/login');
            }

            $user = $this->adminModel->findByLogin($login);

            if (!$user) {
                error('نام کاربری یا رمز عبور اشتباه است.');
                return $this->view('admin/login');
            }

            // Ensure the account has an allowed admin role.
            $allowedRoles = ['admin', 'manager', 'administrator', 'superadmin', 'owner'];
            if (!in_array(strtolower($user['role'] ?? ''), $allowedRoles, true)) {
                error('نام کاربری یا رمز عبور اشتباه است.');
                return $this->view('admin/login');
            }

            $storedPassword = $user['password'] ?? '';
            $authenticated = false;

            if (password_verify($password, $storedPassword)) {
                $authenticated = true;
            } else {
                $passwordInfo = password_get_info($storedPassword);
                if ($passwordInfo['algo'] === 0 && hash_equals($storedPassword, $password)) {
                    $authenticated = true;
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    if ($newHash) {
                        $this->adminModel->updatePassword((int)$user['id'], $newHash);
                    }
                }
            }

            if (!$authenticated) {
                error('نام کاربری یا رمز عبور اشتباه است.');
                return $this->view('admin/login');
            }

            session_regenerate_id(true);
            $_SESSION = [];
            $_SESSION['admin'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ];

            redirect(SITE_URL . '/admin/dashboard');
        }

        if (isAdmin()) {
            redirect(SITE_URL . '/admin/dashboard');
        }

        if (isCustomerLoggedIn()) {
            redirect(SITE_URL . '/dashboard');
        }

        $this->view('admin/login');
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();
        redirect(SITE_URL . '/admin/login');
    }

    public function index()
    {
        requireAdmin();
        $this->dashboard();
    }

    public function dashboard()
    {
        requireAdmin();

        $stats = $this->getDashboardStats();
        
        try {
            $stats['pending_bookings'] = $this->bookingModel->getPendingCount();
        } catch (\Exception $e) {
            $stats['pending_bookings'] = 0;
        }
        
        try {
            $stats['active_repairs'] = $this->repairModel->getActiveCount();
        } catch (\Exception $e) {
            $stats['active_repairs'] = 0;
        }

        $recentUsers = [];
        $recentBookings = [];
        $recentOrders = [];
        $recentDiagnostics = [];
        $diagnosticStats = ['total_scans' => 0, 'active_faults' => 0];
        $aiStats = ['knowledge_count' => 0];

        try {
            $recentUsers = $this->getRecentUsers();
        } catch (\Exception $e) {
            $recentUsers = [];
        }

        try {
            $recentBookings = $this->getRecentBookings();
        } catch (\Exception $e) {
            $recentBookings = [];
        }

        try {
            $recentOrders = $this->getRecentOrders();
        } catch (\Exception $e) {
            $recentOrders = [];
        }

        try {
            $recentDiagnostics = $this->diagnosticModel->getRecentDiagnostics(5);
        } catch (\Exception $e) {
            $recentDiagnostics = [];
        }

        try {
            $diagnosticStats['total_scans'] = $this->diagnosticModel->getScanCount();
        } catch (\Exception $e) {
            $diagnosticStats['total_scans'] = 0;
        }

        try {
            $diagnosticStats['active_faults'] = $this->diagnosticModel->getActiveFaultCount();
        } catch (\Exception $e) {
            $diagnosticStats['active_faults'] = 0;
        }

        try {
            $aiStats['knowledge_count'] = $this->diagnosticAIModel->getKnowledgeCount();
        } catch (\Exception $e) {
            $aiStats['knowledge_count'] = 0;
        }

        $db = \App\Core\Database::connect();

        $shopStats = [
            'total_products' => 0,
            'active_products' => 0,
            'low_stock' => 0,
            'total_revenue' => 0.0,
        ];

        try {
            $shopStats['total_products'] = (int) $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        } catch (\Throwable $e) {
            $shopStats['total_products'] = 0;
        }

        try {
            $shopStats['active_products'] = (int) $db->query("SELECT COUNT(*) FROM products WHERE status = 1")->fetchColumn();
        } catch (\Throwable $e) {
            $shopStats['active_products'] = 0;
        }

        try {
            $shopStats['low_stock'] = (int) $db->query("SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 10")->fetchColumn();
        } catch (\Throwable $e) {
            $shopStats['low_stock'] = 0;
        }

        try {
            $shopStats['total_revenue'] = (float) $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
        } catch (\Throwable $e) {
            $shopStats['total_revenue'] = 0.0;
        }

        $data = [
            'stats' => $stats,
            'shopStats' => $shopStats,
            'recentUsers' => $recentUsers,
            'recentBookings' => $recentBookings,
            'recentOrders' => $recentOrders,
            'recentDiagnostics' => $recentDiagnostics,
            'diagnosticStats' => $diagnosticStats,
            'aiStats' => $aiStats,
            'adminSession' => $_SESSION['admin'] ?? [],
            'today' => date('Y-m-d'),
            'status' => 'Operational',
        ];

        $this->view('admin/dashboard', $data);
    }

    public function getDashboardStats()
    {
        return $this->adminModel->getDashboardStats();
    }

    public function getRecentUsers($limit = 10)
    {
        return $this->adminModel->getRecentUsers($limit);
    }

    public function getRecentBookings($limit = 10)
    {
        return $this->adminModel->getRecentBookings($limit);
    }

    public function getRecentOrders($limit = 10)
    {
        return $this->adminModel->getRecentOrders($limit);
    }

    public function services()
    {
        requireAdmin();
        $this->view('admin/services');
    }

    public function brands()
    {
        requireAdmin();
        $this->view('admin/brands');
    }

    public function articles()
    {
        requireAdmin();
        $this->view('admin/articles');
    }

    public function inquiries()
    {
        requireAdmin();
        $this->view('admin/inquiries');
    }

    public function appointments()
    {
        requireAdmin();
        $this->view('admin/appointments');
    }

    public function products()
    {
        requireAdmin();
        $this->view('admin/products');
    }

    public function homepage()
    {
        requireAdmin();
        $this->view('admin/homepage');
    }

    public function seo()
    {
        requireAdmin();
        $this->view('admin/seo');
    }

    public function media()
    {
        requireAdmin();
        $this->view('admin/media');
    }
}
