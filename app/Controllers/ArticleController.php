<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    private $articleModel;

    public function __construct()
    {
        $this->articleModel = new Article();
    }

    public function index()
    {
        $this->view('articles/index');
    }

    public function category($slug)
    {
        $this->view('articles/category', [
            'slug' => $slug,
        ]);
    }

    public function show($slug)
    {
        $article = $this->articleModel->findBySlug($slug);
        if (!$article) {
            http_response_code(404);
        }

        $this->view('articles/show', [
            'slug' => $slug,
            'article' => $article,
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

        $result = $this->articleModel->getPaginated($page, $perPage, $search, $statusValue);

        $this->view('admin/articles/index', [
            'articles' => $result['articles'] ?? [],
            'page' => $page,
            'pages' => max(1, (int) ceil(($result['total'] ?? 0) / $perPage)),
            'search' => $search,
            'status' => $statusFilter,
            'total' => (int) ($result['total'] ?? 0),
        ]);
    }

    public function adminCreate()
    {
        requireLogin();
        $this->requireAdminAccess();

        $this->view('admin/articles/create', ['article' => []]);
    }

    public function adminStore()
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/articles/create');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            $this->view('admin/articles/create', ['article' => $_POST]);
            return;
        }

        $data = $this->sanitizeArticleData($_POST);
        if (!empty($_FILES['image']['name'])) {
            $data['image'] = $this->uploadImage($_FILES['image'], 'articles');
        }
        if (empty($data['slug'])) {
            $data['slug'] = slug($data['title_fa'] ?? $data['title_en'] ?? 'article');
        }
        $errors = $this->validateArticleData($data);

        if (!empty($errors)) {
            $this->view('admin/articles/create', ['article' => $data, 'errors' => $errors]);
            return;
        }

        if ($this->articleModel->createArticle($data)) {
            success('مقاله با موفقیت ثبت شد.');
            redirect(SITE_URL . '/admin/articles');
        }

        error('ثبت مقاله با خطا مواجه شد.');
        $this->view('admin/articles/create', ['article' => $data]);
    }

    public function adminEdit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $article = $this->articleModel->findById((int) $id);
        if (!$article) {
            error('مقاله مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/articles');
        }

        $this->view('admin/articles/edit', ['article' => $article]);
    }

    public function adminUpdate($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/articles/edit/' . (int) $id);
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/articles/edit/' . (int) $id);
        }

        $article = $this->articleModel->findById((int) $id);
        if (!$article) {
            error('مقاله مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/articles');
        }

        $data = $this->sanitizeArticleData($_POST);
        $data['image'] = $article['image'] ?? null;
        if (!empty($_FILES['image']['name'])) {
            $uploaded = $this->uploadImage($_FILES['image'], 'articles');
            if ($uploaded !== null) {
                $data['image'] = $uploaded;
            }
        }
        if (empty($data['slug'])) {
            $data['slug'] = slug($data['title_fa'] ?? $data['title_en'] ?? 'article');
        }
        $errors = $this->validateArticleData($data);

        if (!empty($errors)) {
            $this->view('admin/articles/edit', ['article' => array_merge($article, $data), 'errors' => $errors]);
            return;
        }

        if ($this->articleModel->updateArticle((int) $id, $data)) {
            success('مقاله با موفقیت به‌روزرسانی شد.');
            redirect(SITE_URL . '/admin/articles');
        }

        error('به‌روزرسانی مقاله با خطا مواجه شد.');
        $this->view('admin/articles/edit', ['article' => array_merge($article, $data)]);
    }

    public function adminDelete($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/articles');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/articles');
        }

        if ($this->articleModel->deleteArticle((int) $id)) {
            success('مقاله با موفقیت حذف شد.');
        } else {
            error('حذف مقاله با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/articles');
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }

    private function sanitizeArticleData(array $data)
    {
        return [
            'title_fa' => trim($data['title_fa'] ?? ''),
            'title_en' => trim($data['title_en'] ?? ''),
            'slug' => trim($data['slug'] ?? ''),
            'category' => trim($data['category'] ?? ''),
            'author' => trim($data['author'] ?? ''),
            'content_fa' => trim($data['content_fa'] ?? ''),
            'content_en' => trim($data['content_en'] ?? ''),
            'seo_title_fa' => trim($data['seo_title_fa'] ?? ''),
            'seo_title_en' => trim($data['seo_title_en'] ?? ''),
            'seo_description_fa' => trim($data['seo_description_fa'] ?? ''),
            'seo_description_en' => trim($data['seo_description_en'] ?? ''),
            'meta_description_fa' => trim($data['meta_description_fa'] ?? ''),
            'meta_description_en' => trim($data['meta_description_en'] ?? ''),
            'status' => isset($data['status']) ? (int) $data['status'] : 1,
            'image' => trim($data['image'] ?? ''),
        ];
    }

    private function validateArticleData(array $data)
    {
        $errors = [];

        if (trim($data['title_fa'] ?? '') === '') {
            $errors['title_fa'] = 'عنوان فارسی الزامی است.';
        }

        if (trim($data['slug'] ?? '') === '') {
            $errors['slug'] = 'اسلاگ الزامی است.';
        } elseif (!preg_match('/^[a-z0-9آ-ی-]+$/u', $data['slug'])) {
            $errors['slug'] = 'اسلاگ فقط باید شامل حروف فارسی و انگلیسی، اعداد و خط تیره باشد.';
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
