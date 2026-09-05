<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;

class AdminProductImagesController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function upload($productId)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        if (!verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        if (empty($_FILES['image']) || empty($_FILES['image']['tmp_name'])) {
            error('فایلی ارسال نشده است.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        $file = $_FILES['image'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'] ?? 'image.jpg', PATHINFO_EXTENSION));
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
        if ($finfo) {
            finfo_close($finfo);
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            error('حداکثر حجم تصویر 5MB است.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        if (($mime === null || !in_array($mime, $allowedMime, true)) && !in_array($ext, $allowedExt, true)) {
            error('فرمت تصویر مجاز نیست.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        if (preg_match('/\.(php|phtml|php3|php4|php5|php7|php8|exe|bat|cmd|js|jar)$/i', $file['name'] ?? '')) {
            error('نوع فایل مجاز نیست.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        $productController = new ProductController();
        $uploaded = $productController->uploadProductImage($file);
        if ($uploaded) {
            $this->productModel->addProductImage((int) $productId, $uploaded);
            success('تصویر با موفقیت بارگذاری شد.');
        } else {
            error('بارگذاری تصویر با خطا مواجه شد.');
        }

        redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
    }

    public function delete($productId)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        $imageId = (int) ($_POST['image_id'] ?? 0);
        if ($imageId > 0) {
            $this->productModel->deleteProductImage((int) $productId, $imageId);
            success('تصویر حذف شد.');
        }

        redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
    }

    public function setPrimary($productId)
    {
        requireLogin();
        $this->requireAdminAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
            error('درخواست نامعتبر است.');
            redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
        }

        $imageId = (int) ($_POST['image_id'] ?? 0);
        if ($imageId > 0) {
            if ($this->productModel->setPrimaryProductImage((int) $productId, $imageId)) {
                success('تصویر اصلی تغییر کرد.');
            } else {
                error('تغییر تصویر اصلی ناموفق بود.');
            }
        }

        redirect(SITE_URL . '/admin/products/edit/' . (int) $productId);
    }

    private function requireAdminAccess()
    {
        if (!isAdmin()) {
            error('دسترسی مدیریتی لازم است.');
            redirect(SITE_URL . '/admin/login');
        }
    }
}
