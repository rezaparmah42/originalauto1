<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    private $userModel;

    private $allowedRoles = ['customer', 'admin', 'manager', 'administrator', 'superadmin', 'owner'];

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        $this->guard();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $search = trim((string) ($_GET['search'] ?? ''));
        $result = $this->userModel->getPaginated($page, $perPage, $search);

        $this->view('admin/users/index', [
            'users' => $result['users'] ?? [],
            'page' => $page,
            'pages' => max(1, (int) ceil(($result['total'] ?? 0) / $perPage)),
            'search' => $search,
            'total' => (int) ($result['total'] ?? 0),
        ]);
    }

    public function edit($id)
    {
        $this->guard();
        $user = $this->userModel->findById((int) $id);
        if (!$user) {
            error('کاربر مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/users');
        }

        $this->view('admin/users/edit', [
            'user' => $user,
            'roles' => $this->allowedRoles,
        ]);
    }

    public function update($id)
    {
        $this->guard();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/users/edit/' . (int) $id);
        }
        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/users/edit/' . (int) $id);
        }

        $user = $this->userModel->findById((int) $id);
        if (!$user) {
            error('کاربر مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/users');
        }

        $data = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'role' => trim((string) ($_POST['role'] ?? 'customer')),
            'status' => (int) ($_POST['status'] ?? 1),
        ];
        $errors = [];
        if ($data['name'] === '') {
            $errors[] = 'نام کاربر الزامی است.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'ایمیل کاربر نامعتبر است.';
        }
        if (!in_array($data['role'], $this->allowedRoles, true)) {
            $errors[] = 'نقش کاربر نامعتبر است.';
        }
        if (!in_array($data['status'], [0, 1], true)) {
            $errors[] = 'وضعیت کاربر نامعتبر است.';
        }

        if ($data['role'] === 'customer' && (int) ($_SESSION['admin']['id'] ?? 0) === (int) $id) {
            $errors[] = 'حساب مدیر جاری نمی‌تواند به مشتری تبدیل شود.';
        }

        if (!empty($errors)) {
            $this->view('admin/users/edit', ['user' => array_merge($user, $data), 'roles' => $this->allowedRoles, 'errors' => $errors]);
            return;
        }

        if ($this->userModel->updateAdminUser((int) $id, $data)) {
            success('اطلاعات کاربر به‌روزرسانی شد.');
        } else {
            error('به‌روزرسانی کاربر با خطا مواجه شد.');
        }
        redirect(SITE_URL . '/admin/users');
    }

    private function guard()
    {
        requireLogin();
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}
