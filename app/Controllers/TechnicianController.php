<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Technician;
use App\Models\WorkshopTask;
use App\Models\RepairNote;
use App\Models\Repair;

class TechnicianController extends Controller
{
    private $technicianModel;
    private $taskModel;
    private $noteModel;
    private $repairModel;

    public function __construct()
    {
        $this->technicianModel = new Technician();
        $this->taskModel = new WorkshopTask();
        $this->noteModel = new RepairNote();
        $this->repairModel = new Repair();
    }

    // Admin: list technicians
    public function index()
    {
        requireLogin();
        $this->requireAdminAccess();

        $items = $this->technicianModel->all();
        $this->view('admin/technicians/index', ['items' => $items]);
    }

    public function create()
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر');
                redirect(SITE_URL . '/admin/technicians');
            }

            $this->technicianModel->create([
                'user_id' => $_POST['user_id'] ?? null,
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active'),
            ]);

            success('تکنسین ایجاد شد.');
            redirect(SITE_URL . '/admin/technicians');
        }

        $this->view('admin/technicians/create');
    }

    public function edit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $item = $this->technicianModel->find((int)$id);
        if (!$item) {
            error('تکنسین یافت نشد.');
            redirect(SITE_URL . '/admin/technicians');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                error('درخواست نامعتبر');
                redirect(SITE_URL . '/admin/technicians');
            }

            $this->technicianModel->update((int)$id, [
                'user_id' => $_POST['user_id'] ?? null,
                'name' => trim($_POST['name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active'),
            ]);

            success('تکنسین به‌روزرسانی شد.');
            redirect(SITE_URL . '/admin/technicians');
        }

        $this->view('admin/technicians/edit', ['item' => $item]);
    }

    public function tasks()
    {
        requireLogin();
        $this->requireAdminAccess();

        $items = $this->taskModel->getByRepair(0);
        $this->view('admin/technicians/tasks', ['items' => $items]);
    }

    // Technician panel
    public function dashboard()
    {
        requireLogin();

        $technicianId = (int) ($_SESSION['technician_id'] ?? 0);
        $technician = $this->technicianModel->find($technicianId);
        $tasks = $this->taskModel->getByTechnician($technicianId);
        $perf = $this->technicianModel->getPerformance($technicianId);

        $this->view('technician/dashboard', ['technician' => $technician, 'tasks' => $tasks, 'perf' => $perf]);
    }

    public function myTasks()
    {
        requireLogin();
        $technicianId = (int) ($_SESSION['technician_id'] ?? 0);
        $tasks = $this->taskModel->getByTechnician($technicianId);
        $this->view('technician/tasks', ['tasks' => $tasks]);
    }

    public function showTask($id)
    {
        requireLogin();
        $task = $this->taskModel->getByRepair((int)$id);
        // mark activity
        if (!empty($_SESSION['technician_id'])) {
            $this->technicianModel->logActivity((int)$_SESSION['technician_id'], 'view_task', 'Viewed task for repair ' . (int)$id);
        }
        $this->view('technician/show', ['task' => $task]);
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}
