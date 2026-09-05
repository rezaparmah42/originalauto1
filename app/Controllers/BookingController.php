<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\Repair;
use App\Models\Notification;
use App\Models\Service;

class BookingController extends Controller
{
    private $bookingModel;
    private $repairModel;
    private $notificationModel;
    private $allowedStatuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->repairModel = new Repair();
        $this->notificationModel = new Notification();
    }

    public function create()
    {
        $this->view('booking/create', [
            'services' => (new Service())->getVisibleServices(),
        ]);
    }

    public function confirmation()
    {
        requireCustomer();
        $this->view('booking/confirmation');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/booking');
        }

        if (!isCustomerLoggedIn()) {
            error('شما باید وارد حساب کاربری خود شوید تا ادامه دهید.');
            redirect(SITE_URL . '/login');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.');
            return $this->view('booking/create', ['services' => (new Service())->getVisibleServices()]);
        }

        $customerName = trim((string) ($_POST['customer_name'] ?? ''));
        $customerPhone = trim((string) ($_POST['customer_phone'] ?? ''));
        $vehicleBrand = trim((string) ($_POST['vehicle_brand'] ?? ''));
        $vehicleModel = trim((string) ($_POST['vehicle_model'] ?? ''));
        $vehicleYear = trim((string) ($_POST['vehicle_year'] ?? ''));
        $serviceType = trim((string) ($_POST['service_type'] ?? ''));
        $serviceId = (int) ($_POST['service_id'] ?? 0);
        if ($serviceId > 0) {
            $selectedService = (new Service())->findById($serviceId);
            $serviceType = trim((string) ($selectedService['title_fa'] ?? $selectedService['title_en'] ?? ''));
        }
        $problemType = trim((string) ($_POST['problem_type'] ?? ''));
        $problemText = trim((string) ($_POST['problem'] ?? ''));

        $details = [
            "نام: {$customerName}",
            "شماره تماس: {$customerPhone}",
            "برند خودرو: {$vehicleBrand}",
            "مدل خودرو: {$vehicleModel}",
            "سال تولید: {$vehicleYear}",
            "نوع خدمت: {$serviceType}",
            "نوع مشکل: {$problemType}",
            "توضیحات: {$problemText}",
        ];

        $problemText = implode("\n", array_filter($details, static fn($line) => trim($line, ': ') !== ''));

        $data = [
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'vehicle_brand' => $vehicleBrand,
            'vehicle_model' => $vehicleModel,
            'vehicle_year' => $vehicleYear,
            'service_type' => $serviceType,
            'service_id' => $serviceId,
            'problem_type' => $problemType,
            'problem' => $problemText,
            'booking_date' => trim($_POST['booking_date'] ?? ''),
        ];

        $errors = $this->bookingModel->validate($data);
        if (!empty($errors)) {
            error($errors[0]);
            return $this->view('booking/create', ['services' => (new Service())->getVisibleServices()]);
        }

        $result = $this->bookingModel->createBooking([
            'vehicle_id' => null,
            'service_id' => $serviceId ?: null,
            'problem' => trim("مدل خودرو: {$data['vehicle_model']}\n{$data['problem']}"),
            'booking_date' => $data['booking_date'] ?: date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['customer_id'] ?? null,
            'status' => 'new',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if (!$result) {
            error('خطا در ثبت رزرو. لطفاً دوباره تلاش کنید.');
            return $this->view('booking/create');
        }

        $this->notificationModel->create(
            (int) ($_SESSION['customer_id'] ?? 0),
            'booking_created',
            'رزرو جدید ثبت شد',
            'درخواست رزرو شما با موفقیت ثبت شد و در انتظار بررسی است.'
        );

        success('رزرو شما ثبت شد و به زودی با شما تماس می‌گیریم.');
        redirect(SITE_URL . '/booking/confirmation');
    }

    public function index()
    {
        requireLogin();
        $this->requireAdminAccess();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 15;
        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $result = $this->bookingModel->getPaginated($page, $perPage, $search, $status);

        $this->view('admin/bookings/index', [
            'bookings' => $result['bookings'] ?? [],
            'page' => $page,
            'pages' => max(1, (int) ceil(($result['total'] ?? 0) / $perPage)),
            'search' => $search,
            'status' => $status,
            'total' => (int) ($result['total'] ?? 0),
        ]);
    }

    public function show($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $booking = $this->bookingModel->findById((int) $id);
        if (!$booking) {
            error('رزرو مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/bookings');
        }

        $repairs = $this->repairModel->findByBooking((int) $id);

        $this->view('admin/bookings/show', [
            'booking' => $booking,
            'repairs' => $repairs,
        ]);
    }

    public function updateStatus($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/bookings/view/' . (int) $id);
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/bookings/view/' . (int) $id);
        }

        $status = trim($_POST['status'] ?? '');
        if (!in_array($status, $this->allowedStatuses, true)) {
            error('وضعیت رزرو نامعتبر است.');
            redirect(SITE_URL . '/admin/bookings/view/' . (int) $id);
        }

        $booking = $this->bookingModel->findById((int) $id);
        if ($this->bookingModel->updateStatus((int) $id, $status)) {
            if (!empty($booking['user_id'])) {
                $this->notificationModel->create((int) $booking['user_id'], 'booking_status', 'وضعیت رزرو تغییر کرد', 'وضعیت رزرو شما به «' . $status . '» تغییر کرد.');
            }
            success('وضعیت رزرو به‌روزرسانی شد.');
        } else {
            error('به‌روزرسانی وضعیت رزرو با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/bookings/view/' . (int) $id);
    }

    public function delete($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/bookings');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/bookings');
        }

        if ($this->bookingModel->deleteBooking((int) $id)) {
            success('رزرو با موفقیت حذف شد.');
        } else {
            error('حذف رزرو با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/bookings');
    }

    public function convertToRepair($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $booking = $this->bookingModel->findById((int)$id);
        if (!$booking) {
            error('رزرو یافت نشد.');
            redirect(SITE_URL . '/admin/bookings');
        }

        $description = $booking['problem'] ?? 'تبدیل رزرو به تعمیر';
        $repairId = $this->repairModel->createRepair((int)$id, $description, 'pending', 0);

        if ($repairId) {
            // create initial workshop task
            $taskModel = new \App\Models\WorkshopTask();
            $taskModel->create([
                'repair_id' => (int)$repairId,
                'title' => 'تعمیر اولیه',
                'description' => $description,
                'priority' => 'normal',
                'status' => 'pending',
            ]);

            success('رزرو به تعمیر تبدیل شد.');
        } else {
            error('خطا در ایجاد تعمیر.');
        }

        redirect(SITE_URL . '/admin/bookings/view/' . (int)$id);
    }

    private function dbLastInsertId()
    {
        try {
            return $this->repairModel->db->lastInsertId();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}

