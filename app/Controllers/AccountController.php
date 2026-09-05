<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Diagnostic;
use App\Models\Maintenance;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Repair;
use App\Models\User;
use App\Models\Vehicle;

class AccountController extends Controller
{
    private $userModel;
    private $vehicleModel;
    private $bookingModel;
    private $repairModel;
    private $diagnosticModel;
    private $orderModel;
    private $paymentModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->vehicleModel = new Vehicle();
        $this->bookingModel = new Booking();
        $this->repairModel = new Repair();
        $this->diagnosticModel = new Diagnostic();
        $this->orderModel = new Order();
        $this->paymentModel = new Payment();
    }

    public function login()
    {
        ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.');
                return $this->view('account/login');
            }

            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($login === '' || $password === '') {
                error('لطفاً شماره موبایل یا ایمیل و رمز عبور را وارد کنید.');
                return $this->view('account/login');
            }

            $user = $this->userModel->findByPhoneOrEmail($login);
            if (!$user || !password_verify($password, $user['password'] ?? '')) {
                error('نام کاربری یا رمز عبور اشتباه است.');
                return $this->view('account/login');
            }

            $anonymousCart = $_SESSION['cart'] ?? [];
            if (!is_array($anonymousCart)) {
                $anonymousCart = [];
            }
            $redirect = $_SESSION['redirect_after_login'] ?? SITE_URL . '/dashboard';
            unset($_SESSION['redirect_after_login']);

            session_regenerate_id(true);
            $_SESSION = [];
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_name'] = $user['name'] ?? $user['phone'];

            if (!empty($anonymousCart)) {
                $_SESSION['cart_' . (int) $user['id']] = $anonymousCart;
            }

            redirect($redirect);
        }

        if (isCustomerLoggedIn()) {
            redirect(SITE_URL . '/dashboard');
        }

        $this->view('account/login');
    }

    public function register()
    {
        ensureSessionStarted();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.');
                return $this->view('account/register');
            }

            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirm'] ?? '';

            if ($name === '' || $phone === '' || $password === '' || $confirm === '') {
                error('تمام فیلدهای الزامی باید پر شوند.');
                return $this->view('account/register');
            }

            if ($password !== $confirm) {
                error('کلمه عبور و تکرار آن مطابقت ندارند.');
                return $this->view('account/register');
            }

            if (!isMobile($phone)) {
                error('شماره موبایل معتبر نیست.');
                return $this->view('account/register');
            }

            if ($email !== '' && !isEmail($email)) {
                error('ایمیل معتبر نیست.');
                return $this->view('account/register');
            }

            if ($this->userModel->findByPhone($phone)) {
                error('این شماره موبایل قبلاً ثبت شده است.');
                return $this->view('account/register');
            }

            if ($email !== '' && $this->userModel->findByEmail($email)) {
                error('این ایمیل قبلاً ثبت شده است.');
                return $this->view('account/register');
            }

            $newUser = $this->userModel->createUser([
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);

            if (!$newUser) {
                error('خطا در ثبت نام. لطفاً دوباره تلاش کنید.');
                return $this->view('account/register');
            }

            $user = $this->userModel->findByPhone($phone);
            $redirect = $_SESSION['redirect_after_login'] ?? SITE_URL . '/dashboard';
            unset($_SESSION['redirect_after_login']);

            session_regenerate_id(true);
            $_SESSION = [];
            $_SESSION['customer_id'] = $user['id'];
            $_SESSION['customer_name'] = $user['name'];

            redirect($redirect);
        }

        $this->view('account/register');
    }

    public function logout()
    {
        ensureSessionStarted();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        redirect(SITE_URL . '/');
    }

    public function dashboard()
    {
        requireCustomer();

        $customerId = currentCustomerId();
        $vehicles = $this->vehicleModel->getUserVehicles($customerId);
        $bookings = $this->bookingModel->getByUserId($customerId);
        $upcomingMaintenance = $this->vehicleModel->getUpcomingMaintenance($customerId);
        $repairs = $this->repairModel->getCustomerRepairs($customerId);
        $customerOrders = $this->repairModel->getCustomerOrders($customerId);
        $orderRecords = $this->orderModel->getByUserId($customerId);
        $pendingPayments = array_values(array_filter($orderRecords, function ($order) {
            return ($order['payment_status'] ?? 'pending') === 'pending';
        }));
        $maintenanceReminders = (new \App\Models\Maintenance())->getUpcomingForCustomer($customerId);
        $history = [];
        $latestDiagnostic = null;
        $healthScore = 100;
        $recommendedProducts = [];
        $vehicleSummaries = [];

        if (!empty($vehicles)) {
            foreach ($vehicles as $vehicle) {
                $vehicleId = (int) ($vehicle['id'] ?? 0);
                if ($vehicleId > 0) {
                    $vehicleSummaries[$vehicleId] = $this->vehicleModel->getVehicleStats($vehicleId);
                }
            }

            $firstVehicleId = (int) ($vehicles[0]['id'] ?? 0);
            if ($firstVehicleId > 0) {
                $history = $this->vehicleModel->getVehicleHistory($firstVehicleId);
                $latestDiagnostic = $this->diagnosticModel->getLatestDiagnostic($firstVehicleId);
                $healthScore = $this->vehicleModel->getHealthScore($firstVehicleId);
                $recommendedProducts = $this->vehicleModel->getVehicleAwareSuggestions($firstVehicleId, 4);
            }
        }

        $stats = [
            'total_vehicles' => count($vehicles),
            'total_repairs' => count($repairs),
            'active_repairs' => count(array_filter($repairs, function ($repair) {
                return !in_array(($repair['status'] ?? ''), ['completed', 'cancelled'], true);
            })),
            'completed_repairs' => count(array_filter($repairs, function ($repair) {
                return ($repair['status'] ?? '') === 'completed';
            })),
            'upcoming_maintenance' => count($upcomingMaintenance),
            'pending_payments' => count($pendingPayments),
        ];

        $this->view('account/dashboard', [
            'vehicles' => $vehicles,
            'bookings' => $bookings,
            'upcomingMaintenance' => $upcomingMaintenance,
            'history' => $history,
            'repairs' => $repairs,
            'orders' => $customerOrders,
            'pendingPayments' => $pendingPayments,
            'stats' => $stats,
            'latestDiagnostic' => $latestDiagnostic,
            'healthScore' => $healthScore,
            'maintenanceReminders' => $maintenanceReminders,
            'recommendedProducts' => $recommendedProducts,
            'vehicleSummaries' => $vehicleSummaries,
        ]);
    }

    /**
     * Customer Garage view - focused summary of customer's vehicles, latest repairs, upcoming maintenance and parts used.
     */
    public function garage()
    {
        requireCustomer();

        $customerId = currentCustomerId();
        $vehicles = $this->vehicleModel->getUserVehicles($customerId);
        $repairs = $this->repairModel->getCustomerRepairs($customerId);
        $maintenanceReminders = (new \App\Models\Maintenance())->getUpcomingForCustomer($customerId);

        $vehicleSummaries = [];
        foreach ($vehicles as $vehicle) {
            $vehicleId = (int) ($vehicle['id'] ?? 0);
            if ($vehicleId > 0) {
                $vehicleSummaries[$vehicleId] = $this->vehicleModel->getVehicleStats($vehicleId);
            }
        }

        // Build a lightweight parts summary per vehicle (recent repairs -> parts)
        $partsByVehicle = [];
        $repairModel = $this->repairModel;
        foreach ($vehicles as $vehicle) {
            $vid = (int) ($vehicle['id'] ?? 0);
            $partsByVehicle[$vid] = [];
            if ($vid <= 0) continue;

            // reuse vehicle history which includes repair_id when available
            $history = $this->vehicleModel->getVehicleHistory($vid);
            $seenParts = [];
            foreach ($history as $h) {
                $repairId = (int) ($h['repair_id'] ?? 0);
                if ($repairId <= 0) continue;
                $parts = $repairModel->getParts($repairId);
                foreach ($parts as $p) {
                    $title = $p['title_fa'] ?: ($p['title_en'] ?: ($p['part_name'] ?? ''));
                    if ($title === '') $title = 'Part';
                    $key = md5($title);
                    if (!isset($seenParts[$key])) {
                        $seenParts[$key] = ['title' => $title, 'quantity' => (int) ($p['quantity'] ?? 1)];
                    } else {
                        $seenParts[$key]['quantity'] += (int) ($p['quantity'] ?? 1);
                    }
                }
            }

            $partsByVehicle[$vid] = array_values($seenParts);
        }

        // Enhanced management data per vehicle: profile, full history (repairs/services/diagnostics) and overdue services
        $maintenanceModel = new \App\Models\Maintenance();
        $vehicleDetails = [];
        foreach ($vehicles as $vehicle) {
            $vid = (int) ($vehicle['id'] ?? 0);
            if ($vid <= 0) continue;

            // profile returns aggregated stats and history fields
            $profile = $this->vehicleModel->getVehicleProfile($vid);

            // ensure profile belongs to this customer (defense-in-depth)
            if (!empty($profile) && (int) ($profile['user_id'] ?? $profile['user_id'] ?? 0) !== (int) $customerId) {
                // skip any unexpected vehicle not owned by current customer
                continue;
            }

            // full timeline: vehicle history (bookings+repairs), repairs details and parts, maintenance and diagnostics
            $history = $this->vehicleModel->getVehicleHistory($vid);
            $fullRepairs = [];
            foreach ($history as $h) {
                $repairId = (int) ($h['repair_id'] ?? 0);
                if ($repairId > 0) {
                    $r = $repairModel->findById($repairId);
                    if ($r) {
                        $r['parts'] = $repairModel->getParts($repairId);
                        $fullRepairs[] = $r;
                    }
                }
            }

            $services = $maintenanceModel->getByVehicle($vid);
            $diagnostics = [];
            if (method_exists($this->diagnosticModel, 'getByVehicle')) {
                $diagnostics = $this->diagnosticModel->getByVehicle($vid);
            } elseif (method_exists($this->diagnosticModel, 'getDiagnosticsForVehicle')) {
                $diagnostics = $this->diagnosticModel->getDiagnosticsForVehicle($vid);
            } else {
                $diagnostics = $this->vehicleModel->getDiagnosticHistory($vid);
            }

            // classify overdue services from upcoming list
            $upcomingForVehicle = $maintenanceModel->getUpcomingByVehicle($vid);
            $overdueCount = 0;
            foreach ($upcomingForVehicle as $u) {
                if (($u['due_state'] ?? '') === 'overdue') $overdueCount++;
            }

            $vehicleDetails[$vid] = [
                'profile' => $profile,
                'full_repairs' => $fullRepairs,
                'services' => $services,
                'diagnostics' => $diagnostics,
                'upcoming' => $upcomingForVehicle,
                'overdue_services_count' => $overdueCount,
            ];
        }

        $this->view('account/garage', [
            'vehicles' => $vehicles,
            'repairs' => $repairs,
            'maintenanceReminders' => $maintenanceReminders,
            'vehicleSummaries' => $vehicleSummaries,
            'partsByVehicle' => $partsByVehicle,
            'vehicleDetails' => $vehicleDetails,
        ]);
    }

    public function profile()
    {
        requireCustomer();

        $user = $this->userModel->findById(currentCustomerId());
        if (!$user) {
            redirect(SITE_URL . '/dashboard');
        }

        $this->view('account/profile', ['user' => $user]);
    }

    public function updateProfile()
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/account/profile');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/account/profile');
        }

        $userId = currentCustomerId();
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $phone === '') {
            error('نام و شماره موبایل الزامی هستند.');
            redirect(SITE_URL . '/account/profile');
        }

        if (!isMobile($phone)) {
            error('شماره موبایل معتبر نیست.');
            redirect(SITE_URL . '/account/profile');
        }

        if ($email !== '' && !isEmail($email)) {
            error('ایمیل معتبر نیست.');
            redirect(SITE_URL . '/account/profile');
        }

        $existingPhone = $this->userModel->findByPhone($phone);
        if ($existingPhone && (int) ($existingPhone['id'] ?? 0) !== (int) $userId) {
            error('این شماره موبایل قبلاً ثبت شده است.');
            redirect(SITE_URL . '/account/profile');
        }

        $existingEmail = $this->userModel->findByEmail($email);
        if ($email !== '' && $existingEmail && (int) ($existingEmail['id'] ?? 0) !== (int) $userId) {
            error('این ایمیل قبلاً ثبت شده است.');
            redirect(SITE_URL . '/account/profile');
        }

        if ($this->userModel->updateProfile($userId, ['name' => $name, 'phone' => $phone, 'email' => $email])) {
            success('اطلاعات حساب کاربری به‌روزرسانی شد.');
        } else {
            error('به‌روزرسانی اطلاعات با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/account/profile');
    }

    public function changePassword()
    {
        requireCustomer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/account/profile');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/account/profile');
        }

        $user = $this->userModel->findById(currentCustomerId());
        if (!$user) {
            redirect(SITE_URL . '/login');
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!password_verify($currentPassword, $user['password'] ?? '')) {
            error('رمز عبور فعلی اشتباه است.');
            redirect(SITE_URL . '/account/profile');
        }

        if ($newPassword === '' || $confirmPassword === '') {
            error('رمز عبور جدید و تکرار آن الزامی هستند.');
            redirect(SITE_URL . '/account/profile');
        }

        if ($newPassword !== $confirmPassword) {
            error('تکرار رمز عبور مطابقت ندارد.');
            redirect(SITE_URL . '/account/profile');
        }

        if ($this->userModel->updatePassword(currentCustomerId(), password_hash($newPassword, PASSWORD_DEFAULT))) {
            success('رمز عبور با موفقیت تغییر کرد.');
        } else {
            error('تغییر رمز عبور با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/account/profile');
    }

    public function repairs()
    {
        requireCustomer();

        $customerId = currentCustomerId();
        $vehicleId = isset($_GET['vehicle_id']) ? (int) $_GET['vehicle_id'] : 0;
        $repairs = $this->repairModel->getCustomerRepairs($customerId);

        if ($vehicleId > 0) {
            $repairs = array_values(array_filter($repairs, static function ($repair) use ($vehicleId) {
                return (int) ($repair['vehicle_id'] ?? 0) === $vehicleId;
            }));
        }

        $this->view('account/repairs/index', ['repairs' => $repairs, 'vehicle_id' => $vehicleId]);
    }

    /**
     * Dedicated vehicle detail page for owner: shows profile, repairs, services, parts and suggestions.
     */
    private function normalizeVehicleHealthData(int $vehicleId): array
    {
        $maintenanceModel = new Maintenance();
        $services = $maintenanceModel->getByVehicle($vehicleId);
        $upcoming = $maintenanceModel->getUpcomingByVehicle($vehicleId);

        $profile = $this->vehicleModel->getVehicleProfile($vehicleId);
        $history = $this->vehicleModel->getVehicleHistory($vehicleId);

        $repairs = [];
        foreach ($history as $h) {
            $rid = (int) ($h['repair_id'] ?? 0);
            if ($rid <= 0) {
                continue;
            }

            $repair = $this->repairModel->findById($rid);
            if ($repair) {
                $repair['parts'] = $this->repairModel->getParts($rid);
                $repairs[] = $repair;
            }
        }

        $lastRepair = null;
        if (!empty($repairs)) {
            usort($repairs, static function ($a, $b) {
                return (strtotime((string) ($b['created_at'] ?? '1970-01-01')) ?: 0) <=> (strtotime((string) ($a['created_at'] ?? '1970-01-01')) ?: 0);
            });
            $lastRepair = $repairs[0];
        }

        $lastServiceDate = null;
        foreach ($services as $service) {
            $serviceDate = trim((string) ($service['service_date'] ?? ''));
            if ($serviceDate !== '') {
                $lastServiceDate = $serviceDate;
                break;
            }
        }

        $daysSinceLastService = null;
        if ($lastServiceDate) {
            $today = new \DateTime('today');
            $serviceDate = new \DateTime((string) $lastServiceDate);
            $daysSinceLastService = (int) $today->diff($serviceDate)->format('%r%a');
        }

        $recommendedServices = [];
        foreach ($upcoming as $item) {
            $recommendedServices[] = [
                'title' => $item['title'] ?? 'سرویس پیشنهادی',
                'service_date' => $item['next_service_date'] ?? $item['service_date'] ?? '-',
                'status' => $item['status'] ?? 'scheduled',
                'reason' => $item['description'] ?? 'پیشنهاد بر اساس تاریخ و کیلومتر خودرو',
            ];
        }

        if (empty($recommendedServices)) {
            $recommendedServices[] = [
                'title' => 'سرویس دوره‌ای',
                'service_date' => date('Y-m-d', strtotime('+3 months')),
                'status' => 'scheduled',
                'reason' => 'بر اساس نوبت سرویس خودرو و شرایط فعلی',
            ];
        }

        $likelyParts = [];
        $seenParts = [];
        foreach ($repairs as $repair) {
            foreach (($repair['parts'] ?? []) as $part) {
                $partName = trim((string) ($part['title_fa'] ?? $part['title_en'] ?? $part['part_name'] ?? ''));
                if ($partName === '') {
                    continue;
                }
                $key = strtolower($partName);
                if (isset($seenParts[$key])) {
                    continue;
                }
                $seenParts[$key] = true;
                $likelyParts[] = [
                    'name' => $partName,
                    'reason' => 'بر اساس سابقه تعمیرات این خودرو',
                ];
            }
        }

        if (empty($likelyParts)) {
            $suggestions = $this->vehicleModel->getVehicleAwareSuggestions($vehicleId, 6);
            foreach ($suggestions as $item) {
                $title = trim((string) ($item['title_fa'] ?? $item['title_en'] ?? $item['title'] ?? ''));
                if ($title === '') {
                    continue;
                }
                $likelyParts[] = [
                    'name' => $title,
                    'reason' => 'قطعه سازگار با مدل و برند این خودرو',
                ];
            }
        }

        $calendarItems = [];
        foreach ($services as $service) {
            $calendarItems[] = [
                'title' => $service['title'] ?? 'سرویس',
                'date' => $service['service_date'] ?? '-',
                'type' => 'completed',
                'status' => $service['status'] ?? 'completed',
            ];
        }
        foreach ($upcoming as $item) {
            $calendarItems[] = [
                'title' => $item['title'] ?? 'سرویس آینده',
                'date' => $item['next_service_date'] ?? $item['service_date'] ?? '-',
                'type' => 'upcoming',
                'status' => $item['status'] ?? 'scheduled',
            ];
        }

        usort($calendarItems, static function ($a, $b) {
            return (strtotime((string) ($a['date'] ?? '1970-01-01')) ?: 0) <=> (strtotime((string) ($b['date'] ?? '1970-01-01')) ?: 0);
        });

        return [
            'profile' => $profile,
            'history' => $history,
            'repairs' => $repairs,
            'services' => $services,
            'upcoming' => $upcoming,
            'last_repair' => $lastRepair,
            'last_service_date' => $lastServiceDate,
            'days_since_last_service' => $daysSinceLastService,
            'recommended_services' => $recommendedServices,
            'likely_parts' => array_slice($likelyParts, 0, 6),
            'calendar_items' => $calendarItems,
            'suggestions' => $this->vehicleModel->getVehicleAwareSuggestions($vehicleId, 8),
        ];
    }

    public function vehicleDetail($id)
    {
        requireCustomer();

        $vehicleId = (int) $id;
        if ($vehicleId <= 0) {
            error('خودرو نامعتبر است.');
            redirect(SITE_URL . '/account/garage');
        }

        $vehicle = $this->vehicleModel->findById($vehicleId);
        if (!$vehicle) {
            error('خودرو یافت نشد.');
            redirect(SITE_URL . '/account/garage');
        }

        $customerId = currentCustomerId();
        if ((int) ($vehicle['user_id'] ?? $vehicle['owner_id'] ?? 0) !== (int) $customerId) {
            error('دسترسی به این خودرو مجاز نیست.');
            redirect(SITE_URL . '/account/garage');
        }

        $health = $this->normalizeVehicleHealthData($vehicleId);

        $this->view('account/vehicle_detail', [
            'vehicle' => $vehicle,
            'profile' => $health['profile'],
            'history' => $health['history'],
            'repairs' => $health['repairs'],
            'services' => $health['services'],
            'upcoming' => $health['upcoming'],
            'diagnostics' => method_exists($this->diagnosticModel, 'getByVehicle') ? $this->diagnosticModel->getByVehicle($vehicleId) : $this->vehicleModel->getDiagnosticHistory($vehicleId),
            'suggestions' => $health['suggestions'],
            'health' => $health,
        ]);
    }

    public function vehicleHealthReport($id)
    {
        requireCustomer();

        $vehicleId = (int) $id;
        if ($vehicleId <= 0) {
            error('خودرو نامعتبر است.');
            redirect(SITE_URL . '/account/garage');
        }

        $vehicle = $this->vehicleModel->findById($vehicleId);
        if (!$vehicle) {
            error('خودرو یافت نشد.');
            redirect(SITE_URL . '/account/garage');
        }

        $customerId = currentCustomerId();
        if ((int) ($vehicle['user_id'] ?? $vehicle['owner_id'] ?? 0) !== (int) $customerId) {
            error('دسترسی به این خودرو مجاز نیست.');
            redirect(SITE_URL . '/account/garage');
        }

        $health = $this->normalizeVehicleHealthData($vehicleId);

        $this->view('account/vehicle_health', [
            'vehicle' => $vehicle,
            'health' => $health,
            'profile' => $health['profile'],
            'repairs' => $health['repairs'],
            'maintenance' => $health['services'],
            'upcoming' => $health['upcoming'],
            'suggestions' => $health['suggestions'],
        ]);
    }

    public function repairDetail($id)
    {
        requireCustomer();

        $repair = $this->repairModel->getCustomerRepairDetail(currentCustomerId(), (int) $id);
        if (!$repair) {
            error('تعمیر مورد نظر یافت نشد.');
            redirect(SITE_URL . '/account/repairs');
        }

        $timeline = $this->repairModel->getFullTimeline((int) $id);
        $orderRecords = $this->orderModel->getByUserId(currentCustomerId());
        $paymentHistory = [];
        foreach ($orderRecords as $order) {
            $payment = $this->paymentModel->findByOrderId((int) ($order['id'] ?? 0));
            if ($payment) {
                $paymentHistory[] = [
                    'order_id' => (int) ($order['id'] ?? 0),
                    'payment_status' => $payment['status'] ?? 'pending',
                    'transaction_id' => $payment['transaction_id'] ?? '-',
                    'created_at' => $payment['created_at'] ?? '-',
                ];
            }
        }

        $this->view('account/repairs/show', [
            'repair' => $repair,
            'timeline' => $timeline,
            'paymentHistory' => $paymentHistory,
            'vehicle_id' => (int) ($repair['vehicle_id'] ?? 0),
        ]);
    }

    public function orders()
    {
        requireCustomer();

        $customerId = currentCustomerId();
        $orders = $this->orderModel->getCustomerOrders($customerId);
        $this->view('account/orders/index', ['orders' => $orders]);
    }

    public function orderShow($id)
    {
        requireCustomer();

        $order = $this->orderModel->find((int) $id);
        if (!$order || (int) ($order['user_id'] ?? 0) !== (int) currentCustomerId()) {
            error('سفارش یافت نشد.');
            redirect(SITE_URL . '/account/orders');
        }

        $this->view('account/orders/show', [
            'order' => $order,
            'items' => $this->orderModel->getOrderItems((int) $id),
            'statusHistory' => $this->orderModel->getStatusHistory((int) $id),
        ]);
    }
}
