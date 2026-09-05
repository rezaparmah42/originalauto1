<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $products = $this->productModel->getVisibleProducts();

        echo '<h1>محصولات</h1>';
        echo '<ul>';
        foreach ($products as $product) {
            $slug = $product['slug'] ?? $product['id'];
            $title = $product['title_fa'] ?? $product['title_en'] ?? $product['slug'] ?? 'محصول';
            echo '<li><a href="' . SITE_URL . '/products/' . rawurlencode($slug) . '">' . e($title) . '</a></li>';
        }
        echo '</ul>';
    }

    public function show($slug = null)
    {
        $product = null;

        if ($slug) {
            $product = $this->productModel->findBySlug($slug);
        }

        if (!$product && isset($_GET['id'])) {
            $product = $this->productModel->findById((int) $_GET['id']);
        }

        if (!$product) {
            http_response_code(404);
            echo 'محصول مورد نظر یافت نشد.';
            return;
        }

        $title = $product['title_fa'] ?? $product['title_en'] ?? $product['slug'] ?? 'محصول';
        $description = $product['description_fa'] ?? $product['description_en'] ?? 'توضیحات محصول در دسترس نیست.';

        // increment view count safely
        try {
            $this->productModel->incrementViews((int) ($product['id'] ?? 0));
        } catch (\Throwable $e) {
            // ignore
        }

        echo '<h1>' . e($title) . '</h1>';
        echo '<p>' . e($description) . '</p>';
        if (!empty($product['price'])) {
            echo '<p>قیمت: ' . e($product['price']) . '</p>';
        }
    }

    public function adminIndex()
    {
        requireLogin();
        $this->requireAdminAccess();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $search = trim((string) ($_GET['search'] ?? ''));
        $statusFilter = $_GET['status'] ?? 'all';
        $categoryFilter = isset($_GET['category']) && $_GET['category'] !== '' ? (int) $_GET['category'] : null;
        $stockFilter = $_GET['stock'] ?? 'all';
        $statusValue = null;

        if ($statusFilter === 'active') {
            $statusValue = 1;
        } elseif ($statusFilter === 'inactive') {
            $statusValue = 0;
        }

        $result = $this->productModel->getAllAdmin($page, $perPage, $search, $statusValue, $categoryFilter, $stockFilter);
        $products = $result['products'] ?? [];
        $total = (int) ($result['total'] ?? 0);
        $pages = max(1, (int) ceil($total / $perPage));
        $categories = $this->productModel->categoriesList();

        $this->view('admin/products/index', [
            'products' => $products,
            'page' => $page,
            'pages' => $pages,
            'search' => $search,
            'status' => $statusFilter,
            'category' => $categoryFilter,
            'stock' => $stockFilter,
            'total' => $total,
            'categories' => $categories,
        ]);
    }

    public function adminCreate()
    {
        requireLogin();
        $this->requireAdminAccess();
        $this->view('admin/products/create', ['product' => []]);
    }

    public function adminStore()
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/products/create');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            $this->view('admin/products/create', ['product' => $_POST]);
            return;
        }

        $data = $this->sanitizeProductData($_POST);
        $data['seo_title_fa'] = trim($_POST['seo_title_fa'] ?? '');
        $data['seo_description_fa'] = trim($_POST['seo_description_fa'] ?? '');
        $data['search_keywords_fa'] = trim($_POST['search_keywords_fa'] ?? '');
        $data['category_id'] = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
        $data['featured'] = isset($_POST['featured']) ? (int) $_POST['featured'] : 0;
        $errors = $this->validateProductData($data);

        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $uploadedImage = $this->uploadProductImage($_FILES['image']);
            if ($uploadedImage) {
                $data['image'] = $uploadedImage;
            } else {
                $errors['image'] = 'بارگذاری تصویر انجام نشد.';
            }
        }

        if (!empty($errors)) {
            $this->view('admin/products/create', ['product' => $data, 'errors' => $errors]);
            return;
        }

        $data['slug'] = $this->buildSlug($data['title_fa'] ?? $data['title_en'] ?? 'product');
        // ensure slug uniqueness
        $existing = $this->productModel->findBySlug($data['slug']);
        if ($existing) {
            $data['slug'] .= '-' . time();
        }
        $data['status'] = (int) ($data['status'] ?? 1);

        $createdProductId = $this->productModel->create($data);
        if ($createdProductId) {
            $compatibleModelIds = [];
            foreach ($_POST['compatible_model_ids'] ?? [] as $modelId) {
                $modelId = (int) $modelId;
                if ($modelId > 0) {
                    $compatibleModelIds[$modelId] = $modelId;
                }
            }

            $compatibilityModel = new \App\Models\ProductCompatibility();
            $compatibilityModel->replaceRelations((int) $createdProductId, array_values($compatibleModelIds));

            success('محصول با موفقیت ثبت شد.');
            redirect(SITE_URL . '/admin/products');
        }

        error('ثبت محصول با خطا مواجه شد.');
        $this->view('admin/products/create', ['product' => $data]);
    }

    public function adminEdit($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        $product = $this->productModel->findById((int) $id);
        if (!$product) {
            error('محصول مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/products');
        }

        $compatibilityModel = new \App\Models\ProductCompatibility();
        $compatibleModelIds = [];
        foreach ($compatibilityModel->getByProductId((int) $id) as $relation) {
            $modelId = (int) ($relation['model_id'] ?? 0);
            if ($modelId > 0) {
                $compatibleModelIds[$modelId] = $modelId;
            }
        }

        $this->view('admin/products/edit', [
            'product' => $product,
            'compatibleModelIds' => $compatibleModelIds,
            'vehicleModels' => $compatibilityModel->getVehicleModelOptions(),
        ]);
    }

    public function adminUpdate($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/products/edit/' . (int) $id);
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $id);
        }

        $product = $this->productModel->findById((int) $id);
        if (!$product) {
            error('محصول مورد نظر یافت نشد.');
            redirect(SITE_URL . '/admin/products');
        }

        $data = $this->sanitizeProductData($_POST);
        $data['seo_title_fa'] = trim($_POST['seo_title_fa'] ?? '');
        $data['seo_description_fa'] = trim($_POST['seo_description_fa'] ?? '');
        $data['search_keywords_fa'] = trim($_POST['search_keywords_fa'] ?? '');
        $data['category_id'] = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
        $data['featured'] = isset($_POST['featured']) ? (int) $_POST['featured'] : 0;
        $errors = $this->validateProductData($data);

        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $uploadedImage = $this->uploadProductImage($_FILES['image']);
            if ($uploadedImage) {
                $data['image'] = $uploadedImage;
                $this->deleteImageFile($product['image'] ?? null);
            } else {
                $errors['image'] = 'بارگذاری تصویر انجام نشد.';
            }
        } else {
            $data['image'] = $product['image'] ?? null;
        }

        if (!empty($errors)) {
            $this->view('admin/products/edit', ['product' => array_merge($product, $data), 'errors' => $errors]);
            return;
        }

        $data['slug'] = $this->buildSlug($data['title_fa'] ?? $data['title_en'] ?? $product['slug'] ?? 'product');
        // avoid colliding slug with other products
        $exists = $this->productModel->findBySlug($data['slug']);
        if ($exists && (int)$exists['id'] !== (int)$id) {
            $data['slug'] .= '-' . time();
        }
        $data['status'] = (int) ($data['status'] ?? 1);

        if ($this->productModel->update((int) $id, $data)) {
            $compatibleModelIds = [];
            foreach ($_POST['compatible_model_ids'] ?? [] as $modelId) {
                $modelId = (int) $modelId;
                if ($modelId > 0) {
                    $compatibleModelIds[$modelId] = $modelId;
                }
            }

            $compatibilityModel = new \App\Models\ProductCompatibility();
            $compatibilityModel->replaceRelations((int) $id, array_values($compatibleModelIds));

            success('محصول با موفقیت به‌روزرسانی شد.');
            redirect(SITE_URL . '/admin/products');
        }

        error('به‌روزرسانی محصول با خطا مواجه شد.');
        $this->view('admin/products/edit', ['product' => array_merge($product, $data)]);
    }

    public function adminDelete($id)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/products');
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/products');
        }

        $product = $this->productModel->findById((int) $id);
        if ($product) {
            $this->deleteImageFile($product['image'] ?? null);
            $this->productModel->delete((int) $id);
            success('محصول با موفقیت حذف شد.');
        } else {
            error('محصول مورد نظر یافت نشد.');
        }

        redirect(SITE_URL . '/admin/products');
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }

    private function sanitizeProductData(array $data)
    {
        return [
            'title_fa' => trim($data['title_fa'] ?? ''),
            'title_en' => trim($data['title_en'] ?? ''),
            'description_fa' => trim($data['description_fa'] ?? ''),
            'description_en' => trim($data['description_en'] ?? ''),
            'price' => trim((string) ($data['price'] ?? '')),
            'stock' => trim((string) ($data['stock'] ?? '')),
            'sku' => trim((string) ($data['sku'] ?? '')),
            'specifications' => trim((string) ($data['specifications'] ?? '')),
            'status' => isset($data['status']) ? (int) $data['status'] : 1,
        ];
    }

    private function validateProductData(array $data)
    {
        $errors = [];

        if (trim($data['title_fa'] ?? '') === '') {
            $errors['title_fa'] = 'عنوان فارسی الزامی است.';
        }

        if (($data['price'] ?? '') === '' || !is_numeric($data['price']) || (float) $data['price'] < 0) {
            $errors['price'] = 'قیمت باید عددی باشد.';
        }

        if (($data['stock'] ?? '') === '' || filter_var($data['stock'], FILTER_VALIDATE_INT) === false || (int) $data['stock'] < 0) {
            $errors['stock'] = 'موجودی باید عددی باشد.';
        }

        if (!in_array((int) ($data['status'] ?? 1), [0, 1], true)) {
            $errors['status'] = 'وضعیت نامعتبر است.';
        }

        if (isset($data['sku']) && trim((string) $data['sku']) !== '' && mb_strlen($data['sku']) > 100) {
            $errors['sku'] = 'کد کالا بیش از حد مجاز است.';
        }

        return $errors;
    }

    private function buildSlug($value)
    {
        $base = trim((string) $value);
        if ($base === '') {
            $base = 'product';
        }

        return slug($base);
    }

    public function uploadProductImage(array $file)
    {
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
        if ($finfo) {
            finfo_close($finfo);
        }

        $ext = strtolower(pathinfo($file['name'] ?? 'image.jpg', PATHINFO_EXTENSION));
        if (($mime === null || !in_array($mime, $allowedMime, true)) && !in_array($ext, $allowedExt, true)) {
            return null;
        }

        $basePath = ensureUploadPath();
        if ($basePath === null) {
            return null;
        }

        $productDirectory = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
        if (!is_dir($productDirectory) && !mkdir($productDirectory, 0755, true) && !is_dir($productDirectory)) {
            return null;
        }

        $safeName = 'product_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . ($ext !== '' ? $ext : 'jpg');
        $destinationPath = $productDirectory . $safeName;

        if (!@move_uploaded_file($file['tmp_name'], $destinationPath)) {
            return null;
        }

        return 'products/' . $safeName;
    }

    private function deleteImageFile($image)
    {
        if (empty($image)) {
            return;
        }

        $basePath = ensureUploadPath();
        if ($basePath === null) {
            return;
        }

        $filePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $image;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}
