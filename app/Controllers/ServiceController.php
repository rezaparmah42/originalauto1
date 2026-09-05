<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    private $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new Service();
    }

    public function index()
    {
        $this->view('services/index');
    }

    public function show($slug)
    {
        if (!$slug) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $uri = rtrim($uri, '/') ?: '/';
            $slug = basename($uri);
        }

        $viewPath = 'services/' . $slug;
        if (file_exists(__DIR__ . '/../Views/' . $viewPath . '.php')) {
            $this->view($viewPath);
            return;
        }

        $this->view('services/show', [
            'slug' => $slug
        ]);
    }

    public function adminIndex()
    {
        requireLogin();
        $this->requireAdminAccess();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $search = trim($_GET['search'] ?? '');
        $statusFilter = $_GET['status'] ?? 'all';
        $statusValue = null;

        if ($statusFilter === 'active') {
            $statusValue = 1;
        } elseif ($statusFilter === 'inactive') {
            $statusValue = 0;
        }

        $result = $this->serviceModel->getPaginated($page, $perPage, $search, $statusValue);

        $this->view('admin/services/index', [
            'services' => $result['services'] ?? [],
            'page' => $page,
            'pages' => max(1, (int) ceil(($result['total'] ?? 0) / $perPage)),
            'search' => $search,
            'status' => $statusFilter,
            'total' => (int) ($result['total'] ?? 0),
        ]);
    }

    public function create()
    {
        requireLogin();
        $this->requireAdminAccess();
        $this->view('admin/services/create', ['service' => []]);
    }

    public function store()
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/services/create');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            $this->view('admin/services/create', ['service' => $_POST]);
            return;
        }

        $data = $this->sanitizeServiceData($_POST);
        $errors = $this->validateServiceData($data);
        if (!empty($_FILES['image']['name'])) {
            $data['image'] = $this->uploadImage($_FILES['image'], 'services');
            if ($data['image'] === null) {
                $errors['image'] = 'بارگذاری تصویر انجام نشد.';
            }
        }

        if (!empty($errors)) {
            $this->view('admin/services/create', ['service' => $data, 'errors' => $errors]);
            return;
        }

        if ($this->serviceModel->createService($data)) {
            success('سرویس با موفقیت ثبت شد.');
            redirect(SITE_URL . '/admin/services');
        }

        error('ثبت سرویس با خطا مواجه شد.');
        $this->view('admin/services/create', ['service' => $data]);
    }

    public function edit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $service = $this->serviceModel->findById((int) $id);
        if (!$service) {
            error('سرویس مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/services');
        }

        $this->view('admin/services/edit', ['service' => $service]);
    }

    public function update($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/services/edit/' . (int) $id);
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/services/edit/' . (int) $id);
        }

        $data = $this->sanitizeServiceData($_POST);
        $data['image'] = $this->serviceModel->findById((int) $id)['image'] ?? null;
        $errors = $this->validateServiceData($data);
        if (!empty($_FILES['image']['name'])) {
            $uploaded = $this->uploadImage($_FILES['image'], 'services');
            if ($uploaded === null) {
                $errors['image'] = 'بارگذاری تصویر انجام نشد.';
            } else {
                $data['image'] = $uploaded;
            }
        }

        if (!empty($errors)) {
            $service = $this->serviceModel->findById((int) $id);
            $this->view('admin/services/edit', ['service' => array_merge($service ?: [], $data), 'errors' => $errors]);
            return;
        }

        if ($this->serviceModel->updateService((int) $id, $data)) {
            success('سرویس با موفقیت به‌روزرسانی شد.');
            redirect(SITE_URL . '/admin/services');
        }

        error('به‌روزرسانی سرویس با خطا مواجه شد.');
        $this->view('admin/services/edit', ['service' => array_merge($this->serviceModel->findById((int) $id) ?: [], $data)]);
    }

    public function delete($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/services');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/services');
        }

        if ($this->serviceModel->deleteService((int) $id)) {
            success('سرویس با موفقیت حذف شد.');
        } else {
            error('حذف سرویس با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/services');
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }

    private function sanitizeServiceData(array $data)
    {
        return [
            'title_fa' => trim($data['title_fa'] ?? ''),
            'title_en' => trim($data['title_en'] ?? ''),
            'slug' => trim($data['slug'] ?? ''),
            'description_fa' => trim($data['description_fa'] ?? ''),
            'description_en' => trim($data['description_en'] ?? ''),
            'seo_title_fa' => trim($data['seo_title_fa'] ?? ''),
            'seo_title_en' => trim($data['seo_title_en'] ?? ''),
            'seo_description_fa' => trim($data['seo_description_fa'] ?? ''),
            'seo_description_en' => trim($data['seo_description_en'] ?? ''),
            'price' => (float) ($data['price'] ?? 0),
            'duration' => trim($data['duration'] ?? ''),
            'status' => isset($data['status']) ? (int) $data['status'] : 1,
            'image' => trim($data['image'] ?? ''),
        ];
    }

    private function validateServiceData(array $data)
    {
        $errors = [];

        if (trim($data['title_fa'] ?? '') === '') {
            $errors['title_fa'] = 'عنوان فارسی الزامی است.';
        }

        if (trim($data['slug'] ?? '') === '') {
            $errors['slug'] = 'اسلاگ الزامی است.';
        } elseif (!preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $errors['slug'] = 'اسلاگ فقط باید شامل حروف کوچک، اعداد و خط تیره باشد.';
        }

        if (!is_numeric($data['price'] ?? null)) {
            $errors['price'] = 'قیمت باید عددی باشد.';
        }

        if (!in_array((int) ($data['status'] ?? 1), [0, 1], true)) {
            $errors['status'] = 'وضعیت نامعتبر است.';
        }

        return $errors;
    }

    private function uploadImage(array $file, string $folder)
    {
        $name = upload_file($file);
        if ($name === null) {
            return null;
        }
        $base = ensureUploadPath();
        $directory = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            return null;
        }
        $source = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;
        $target = $directory . $name;
        if (file_exists($source) && !rename($source, $target)) {
            return null;
        }
        return file_exists($target) ? $folder . '/' . $name : null;
    }
}
