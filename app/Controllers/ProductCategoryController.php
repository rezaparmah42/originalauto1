<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new ProductCategory();
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }

    private function normalizeCategoryData(array $data): array
    {
        return [
            'parent_id' => isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : null,
            'name_fa' => trim((string) ($data['name_fa'] ?? '')),
            'name_en' => trim((string) ($data['name_en'] ?? '')),
            'slug' => trim((string) ($data['slug'] ?? '')),
            'description_fa' => trim((string) ($data['description_fa'] ?? '')),
            'description_en' => trim((string) ($data['description_en'] ?? '')),
            'seo_title_fa' => trim((string) ($data['seo_title_fa'] ?? '')),
            'seo_description_fa' => trim((string) ($data['seo_description_fa'] ?? '')),
            'status' => isset($data['status']) ? (int) $data['status'] : 1,
        ];
    }

    public function index()
    {
        requireLogin();
        $this->requireAdminAccess();

        $categories = $this->categoryModel->getAll();
        $this->view('admin/categories/index', ['categories' => $categories]);
    }

    public function create()
    {
        requireLogin();
        $this->requireAdminAccess();

        $this->view('admin/categories/create', ['category' => []]);
    }

    public function store()
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/categories');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            $this->view('admin/categories/create', ['category' => $_POST]);
            return;
        }

        $data = $this->normalizeCategoryData($_POST);
        if ($data['name_fa'] === '' && $data['name_en'] === '') {
            error('نام دسته‌بندی الزامی است.');
            $this->view('admin/categories/create', ['category' => $data]);
            return;
        }

        $slug = $data['slug'] !== '' ? $data['slug'] : ($data['name_fa'] !== '' ? $data['name_fa'] : $data['name_en']);
        $data['slug'] = slug($slug);

        if ($this->categoryModel->findBySlug($data['slug'])) {
            $data['slug'] .= '-' . time();
        }

        if ($this->categoryModel->create($data)) {
            success('دسته‌بندی با موفقیت ثبت شد.');
            redirect(SITE_URL . '/admin/categories');
        }

        error('ثبت دسته‌بندی با خطا مواجه شد.');
        $this->view('admin/categories/create', ['category' => $data]);
    }

    public function edit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $category = $this->categoryModel->findById((int) $id);
        if (!$category) {
            error('دسته‌بندی مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/categories');
        }

        $this->view('admin/categories/edit', ['category' => $category]);
    }

    public function update($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/categories');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/categories/edit/' . (int) $id);
        }

        $data = $this->normalizeCategoryData($_POST);
        if ($data['name_fa'] === '' && $data['name_en'] === '') {
            error('نام دسته‌بندی الزامی است.');
            redirect(SITE_URL . '/admin/categories/edit/' . (int) $id);
        }

        $slug = $data['slug'] !== '' ? $data['slug'] : ($data['name_fa'] !== '' ? $data['name_fa'] : $data['name_en']);
        $data['slug'] = slug($slug);

        $existing = $this->categoryModel->findBySlug($data['slug']);
        if ($existing && (int) $existing['id'] !== (int) $id) {
            $data['slug'] .= '-' . time();
        }

        if ($this->categoryModel->update((int) $id, $data)) {
            success('دسته‌بندی با موفقیت به‌روزرسانی شد.');
            redirect(SITE_URL . '/admin/categories');
        }

        error('به‌روزرسانی دسته‌بندی با خطا مواجه شد.');
        redirect(SITE_URL . '/admin/categories/edit/' . (int) $id);
    }

    public function delete($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/categories');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/categories');
        }

        $this->categoryModel->delete((int) $id);
        success('دسته‌بندی حذف شد.');
        redirect(SITE_URL . '/admin/categories');
    }
}
