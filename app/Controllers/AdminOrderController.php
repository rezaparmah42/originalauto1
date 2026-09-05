<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;

class AdminOrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }

    public function index()
    {
        requireLogin();
        $this->requireAdminAccess();

        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? 'all';
        $orders = $this->orderModel->getAll($search, $status);

        $this->view('admin/orders/index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $order = $this->orderModel->find((int) $id);
        if (!$order) {
            error('سفارش یافت نشد.');
            redirect(SITE_URL . '/admin/orders');
        }

        $items = $this->orderModel->getItems((int) $id);
        $this->view('admin/orders/show', ['order' => $order, 'items' => $items]);
    }

    public function edit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $order = $this->orderModel->find((int) $id);
        if (!$order) {
            error('سفارش یافت نشد.');
            redirect(SITE_URL . '/admin/orders');
        }

        $this->view('admin/orders/edit', ['order' => $order]);
    }

    public function updateStatus($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/orders');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/orders');
        }

        $status = trim($_POST['status'] ?? 'pending');
        $paymentStatus = trim($_POST['payment_status'] ?? 'pending');
        $note = trim($_POST['note'] ?? '');

        if ($this->orderModel->changeStatus((int) $id, $status, 'admin', $note)) {
            $this->orderModel->updatePaymentStatus((int) $id, $paymentStatus);
            success('وضعیت سفارش به‌روزرسانی شد.');
        } else {
            error('به‌روزرسانی سفارش با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/orders/show/' . (int) $id);
    }
}
