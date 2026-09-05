<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class InventoryController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
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

        $products = $this->productModel->getAll();
        $this->view('admin/inventory/index', ['products' => $products]);
    }

    public function history()
    {
        requireLogin();
        $this->requireAdminAccess();

        $history = $this->productModel->getStockHistory();
        $this->view('admin/inventory/history', ['history' => $history]);
    }

    public function lowStock()
    {
        requireLogin();
        $this->requireAdminAccess();

        $products = $this->productModel->getLowStockProducts(10);
        $this->view('admin/inventory/low_stock', [
            'products' => $products,
            'threshold' => 10,
        ]);
    }

    public function increase($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/inventory');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/inventory');
        }

        $amount = max(1, (int) ($_POST['amount'] ?? 0));
        $note = trim($_POST['note'] ?? '');
        $ok = $this->productModel->increaseStock((int) $id, $amount, $note);

        if ($ok) {
            success('موجودی با موفقیت افزایش یافت.');
        } else {
            error('به‌روزرسانی موجودی با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/inventory');
    }

    public function decrease($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/inventory');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/inventory');
        }

        $amount = max(1, (int) ($_POST['amount'] ?? 0));
        $note = trim($_POST['note'] ?? '');
        $ok = $this->productModel->decreaseStock((int) $id, $amount, $note);

        if ($ok) {
            success('موجودی با موفقیت کاهش یافت.');
        } else {
            error('به‌روزرسانی موجودی با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/inventory');
    }

    public function adjust($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/inventory');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/inventory');
        }

        $quantity = (int) ($_POST['quantity'] ?? 0);
        $note = trim($_POST['note'] ?? '');
        $ok = $this->productModel->adjustStock((int) $id, $quantity, $note);

        if ($ok) {
            success('تعدیل موجودی انجام شد.');
        } else {
            error('تعدیل موجودی با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/inventory');
    }
}
