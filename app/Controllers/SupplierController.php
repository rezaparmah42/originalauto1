<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Supplier;

class SupplierController extends Controller
{
    private $supplierModel;
    private $productModel;

    public function __construct()
    {
        $this->supplierModel = new Supplier();
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

        $suppliers = $this->supplierModel->all();
        $this->view('admin/suppliers/index', ['suppliers' => $suppliers]);
    }

    public function create()
    {
        requireLogin();
        $this->requireAdminAccess();

        $this->view('admin/suppliers/create', ['supplier' => []]);
    }

    public function store()
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/suppliers');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            return $this->view('admin/suppliers/create', ['supplier' => $_POST]);
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            error('نام تأمین‌کننده لازم است.');
            return $this->view('admin/suppliers/create', ['supplier' => $_POST]);
        }

        $ok = $this->supplierModel->create($_POST);
        if ($ok) {
            success('تأمین‌کننده با موفقیت ثبت شد.');
            redirect(SITE_URL . '/admin/suppliers');
        }

        error('خطا در ثبت تأمین‌کننده.');
        $this->view('admin/suppliers/create', ['supplier' => $_POST]);
    }

    public function show($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $supplier = $this->supplierModel->find((int) $id);
        if (!$supplier) {
            error('تأمین‌کننده یافت نشد.');
            redirect(SITE_URL . '/admin/suppliers');
        }

        $products = $this->productModel->getProductsBySupplier((int) $id);
        $this->view('admin/suppliers/show', [
            'supplier' => $supplier,
            'products' => $products,
        ]);
    }

    public function edit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $supplier = $this->supplierModel->find((int) $id);
        if (!$supplier) {
            error('تأمین‌کننده یافت نشد.');
            redirect(SITE_URL . '/admin/suppliers');
        }

        $this->view('admin/suppliers/edit', ['supplier' => $supplier]);
    }

    public function update($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/suppliers');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/suppliers');
        }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            error('نام تأمین‌کننده لازم است.');
            return $this->view('admin/suppliers/edit', ['supplier' => $_POST]);
        }

        $ok = $this->supplierModel->update((int) $id, $_POST);
        if ($ok) {
            success('تأمین‌کننده با موفقیت به‌روزرسانی شد.');
            redirect(SITE_URL . '/admin/suppliers');
        }

        error('به‌روزرسانی تأمین‌کننده با خطا مواجه شد.');
        $this->view('admin/suppliers/edit', ['supplier' => $_POST]);
    }

    public function delete($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/suppliers');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/suppliers');
        }

        if ($this->supplierModel->delete((int) $id)) {
            success('تأمین‌کننده حذف شد.');
        } else {
            error('حذف تأمین‌کننده با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/suppliers');
    }
}
