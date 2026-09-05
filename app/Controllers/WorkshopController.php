<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Repair;

class WorkshopController extends Controller
{
    private $repairModel;

    public function __construct()
    {
        $this->repairModel = new Repair();
    }

    private function requireAdminAccess()
    {
        requireLogin();

        $role = strtolower((string) ($_SESSION['admin']['role'] ?? ''));
        $allowedRoles = ['admin', 'manager', 'administrator', 'superadmin', 'owner'];

        if (!in_array($role, $allowedRoles, true)) {
            redirect(SITE_URL . '/admin/login');
        }
    }

    public function index()
    {
        $this->requireAdminAccess();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 15;
        $status = trim((string) ($_GET['status'] ?? ''));
        $search = trim((string) ($_GET['search'] ?? ''));

        $result = $this->repairModel->getAdminRepairList($page, $perPage, $search, $status);

        $this->view('admin/repairs/index', [
            'repairs' => $result['repairs'] ?? [],
            'page' => $page,
            'pages' => max(1, (int) ceil((($result['total'] ?? 0) / $perPage) ?: 1)),
            'total' => (int) ($result['total'] ?? 0),
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function dashboard()
    {
        $this->requireAdminAccess();

        $stats = $this->repairModel->getWorkshopDashboardStats();

        $this->view('admin/workshop/dashboard', [
            'stats' => $stats,
            'today' => date('Y-m-d'),
            'status' => 'Operational',
        ]);
    }

    public function show($id)
    {
        $this->requireAdminAccess();

        $repairId = (int) $id;
        $repair = $this->repairModel->findAdminById($repairId);

        if (!$repair) {
            error('تعمیر مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/workshop/repairs');
        }

        $this->view('admin/repairs/show', [
            'repair' => $repair,
            'timeline' => $this->repairModel->getTimeline($repairId),
        ]);
    }

    public function updateStatus($id)
    {
        $this->requireAdminAccess();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/workshop/repairs/' . (int) $id);
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است. لطفاً دوباره تلاش کنید.');
            redirect(SITE_URL . '/admin/workshop/repairs/' . (int) $id);
        }

        $repairId = (int) ($id ?? ($_POST['repair_id'] ?? 0));
        $status = trim((string) ($_POST['status'] ?? ''));

        if (!$this->repairModel->validateStatus($status)) {
            error('وضعیت تعمیر نامعتبر است.');
            redirect(SITE_URL . '/admin/workshop/repairs/' . $repairId);
        }

        if ($this->repairModel->updateRepairStatus($repairId, $status)) {
            $this->repairModel->addTimeline($repairId, $status, 'به‌روزرسانی وضعیت', 'وضعیت تعمیر توسط مدیر تغییر کرد.', $_SESSION['admin']['id'] ?? null);
            success('وضعیت تعمیر با موفقیت به‌روزرسانی شد.');
        } else {
            error('به‌روزرسانی وضعیت تعمیر با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/workshop/repairs/' . $repairId);
    }
}
