<?php

use App\Controllers\HomeController;
use App\Controllers\VehicleController;
use App\Controllers\ServiceController;
use App\Controllers\CommonProblemController;
use App\Controllers\GuideController;
use App\Controllers\MechanicController;
use App\Controllers\ArticleController;
use App\Controllers\ShopController;
use App\Controllers\BookingController;
use App\Controllers\BrandController;
use App\Controllers\AdminController;
use App\Controllers\AdminDiagnosticController;
use App\Controllers\AdminOrderController;
use App\Controllers\CartController;
use App\Controllers\DiagnosticController;
use App\Controllers\InventoryController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\SupplierController;
use App\Controllers\VehicleProfileController;
use App\Controllers\MaintenanceController;
use App\Controllers\AdminVehicleController;
use App\Controllers\AIRepairController;
use App\Controllers\AdminAIController;
use App\Controllers\TechnicianController;
use App\Controllers\NotificationController;
use App\Controllers\ChatController;
use App\Controllers\AdminCommunicationController;

$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/contact', [HomeController::class, 'contact']);

$router->get('/vehicles', [App\Controllers\VehicleCatalogController::class, 'index']);
$router->get('/vehicles/{brand}/{model}/{year}', [App\Controllers\VehicleCatalogController::class, 'model']);
$router->get('/vehicles/{brand}/{model}', [App\Controllers\VehicleCatalogController::class, 'model']);
$router->get('/vehicles/{brand}', [App\Controllers\VehicleCatalogController::class, 'brand']);

$router->get('/services', [ServiceController::class, 'index']);
$router->get('/services/{slug}', [ServiceController::class, 'show']);
$router->get('/diagnostic', [DiagnosticController::class, 'index']);
$router->get('/diagnostic/connect', [DiagnosticController::class, 'connect']);
$router->get('/diagnostic/scan', [DiagnosticController::class, 'scan']);
$router->post('/diagnostic/scan', [DiagnosticController::class, 'scan']);
$router->get('/diagnostic/result', [DiagnosticController::class, 'result']);
// AI Diagnostic
$router->get('/ai-diagnostic', [AIRepairController::class, 'index']);
$router->post('/ai-diagnostic/analyze', [AIRepairController::class, 'analyze']);
$router->get('/ai-diagnostic/report/{vehicle}', [AIRepairController::class, 'report']);
// API
$router->post('/api/diagnostic/analyze', [AIRepairController::class, 'apiAnalyze']);

$router->get('/common-problems', [CommonProblemController::class, 'index']);
$router->get('/maintenance-guides', [GuideController::class, 'index']);
$router->get('/mechanics', [MechanicController::class, 'index']);

$router->get('/articles', [ArticleController::class, 'index']);
$router->get('/articles/category/{slug}', [ArticleController::class, 'category']);
$router->get('/articles/{slug}', [ArticleController::class, 'show']);

$router->get('/shop', [ShopController::class, 'index']);
$router->get('/shop/search', [ShopController::class, 'search']);
$router->get('/products', [ShopController::class, 'index']);
$router->get('/products/{slug}', [ShopController::class, 'product']);

$router->get('/booking', [BookingController::class, 'create']);
$router->post('/booking', [BookingController::class, 'store']);
$router->get('/booking/confirmation', [BookingController::class, 'confirmation']);
$router->get('/bookings', function () {
    redirect(SITE_URL . '/booking');
});
$router->get('/repairs', function () {
    redirect(SITE_URL . '/account/repairs');
});
$router->get('/payment', function () {
    redirect(SITE_URL . '/payment/start');
});
$router->get('/invoice', function () {
    redirect(SITE_URL . '/orders');
});
$router->get('/api', function () {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'status' => 'ok',
        'message' => 'Use /api/auth/login, /api/vehicles, /api/diagnostics',
    ]);
    exit;
});
$router->get('/login', [App\Controllers\AccountController::class, 'login']);
$router->post('/login', [App\Controllers\AccountController::class, 'login']);
$router->get('/register', [App\Controllers\AccountController::class, 'register']);
$router->post('/register', [App\Controllers\AccountController::class, 'register']);
$router->get('/logout', [App\Controllers\AccountController::class, 'logout']);
$router->get('/dashboard', [App\Controllers\AccountController::class, 'dashboard']);
$router->get('/account', [App\Controllers\AccountController::class, 'dashboard']);
$router->get('/account/dashboard', [App\Controllers\AccountController::class, 'dashboard']);
// Customer Garage (PATCH_49)
$router->get('/account/garage', [App\Controllers\AccountController::class, 'garage']);
$router->get('/account/orders', [App\Controllers\AccountController::class, 'orders']);
$router->get('/account/orders/{id}', [App\Controllers\AccountController::class, 'orderShow']);
$router->get('/account/profile', [App\Controllers\AccountController::class, 'profile']);
$router->post('/account/profile', [App\Controllers\AccountController::class, 'updateProfile']);
$router->post('/account/password', [App\Controllers\AccountController::class, 'changePassword']);
$router->get('/account/repairs', [App\Controllers\AccountController::class, 'repairs']);
$router->get('/account/repairs/{id}', [App\Controllers\AccountController::class, 'repairDetail']);

$router->get('/brands', [BrandController::class, 'index']);
$router->get('/brands/{brand}', [BrandController::class, 'show']);

$router->get('/admin/login', [AdminController::class, 'login']);
$router->post('/admin/login', [AdminController::class, 'login']);
$router->get('/admin/logout', [AdminController::class, 'logout']);
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/services', [ServiceController::class, 'adminIndex']);
$router->get('/admin/services/create', [ServiceController::class, 'create']);
$router->post('/admin/services/store', [ServiceController::class, 'store']);
$router->get('/admin/services/edit/{id}', [ServiceController::class, 'edit']);
$router->post('/admin/services/update/{id}', [ServiceController::class, 'update']);
$router->post('/admin/services/delete/{id}', [ServiceController::class, 'delete']);
$router->get('/admin/bookings', [BookingController::class, 'index']);
$router->get('/admin/bookings/view/{id}', [BookingController::class, 'show']);
$router->post('/admin/bookings/status/{id}', [BookingController::class, 'updateStatus']);
$router->post('/admin/bookings/delete/{id}', [BookingController::class, 'delete']);
$router->get('/admin/users', [App\Controllers\AdminUserController::class, 'index']);
$router->get('/admin/users/edit/{id}', [App\Controllers\AdminUserController::class, 'edit']);
$router->post('/admin/users/edit/{id}', [App\Controllers\AdminUserController::class, 'update']);
$router->get('/admin/vehicles', [VehicleController::class, 'adminIndex']);
$router->get('/admin/vehicles/view/{id}', [VehicleController::class, 'adminShow']);
$router->get('/admin/vehicles/intelligence', [AdminVehicleController::class, 'intelligence']);
$router->get('/admin/vehicles/intelligence/profile/{id}', [AdminVehicleController::class, 'profile']);
$router->get('/admin/diagnostics', [AdminDiagnosticController::class, 'index']);
$router->get('/admin/diagnostics/show/{id}', [AdminDiagnosticController::class, 'show']);
$router->get('/admin/workshop/dashboard', [App\Controllers\WorkshopController::class, 'dashboard']);
$router->get('/admin/workshop/repairs', [App\Controllers\WorkshopController::class, 'index']);
$router->get('/admin/workshop/repairs/{id}', [App\Controllers\WorkshopController::class, 'show']);
$router->post('/admin/workshop/repairs/status/{id}', [App\Controllers\WorkshopController::class, 'updateStatus']);
// Admin technicians
$router->get('/admin/technicians', [TechnicianController::class, 'index']);
$router->get('/admin/technicians/create', [TechnicianController::class, 'create']);
$router->post('/admin/technicians/create', [TechnicianController::class, 'create']);
$router->get('/admin/technicians/edit/{id}', [TechnicianController::class, 'edit']);
$router->post('/admin/technicians/edit/{id}', [TechnicianController::class, 'edit']);
$router->get('/admin/technicians/tasks', [TechnicianController::class, 'tasks']);

// Technician panel
$router->get('/technician/dashboard', [TechnicianController::class, 'dashboard']);
$router->get('/technician/tasks', [TechnicianController::class, 'myTasks']);
$router->get('/technician/task/{id}', [TechnicianController::class, 'showTask']);
// Notifications and chat
$router->get('/notifications', [NotificationController::class, 'index']);
$router->post('/notifications/read/{id}', [NotificationController::class, 'read']);
$router->get('/notifications/read/{id}', [NotificationController::class, 'read']);

$router->get('/repair/chat/{id}', [ChatController::class, 'conversation']);
$router->post('/repair/chat/send', [ChatController::class, 'send']);

// Admin communication
$router->get('/admin/notifications', [AdminCommunicationController::class, 'notifications']);
$router->get('/admin/sms', [AdminCommunicationController::class, 'sms']);
// Admin API management
$router->get('/admin/api/devices', [App\Controllers\AdminAPIController::class, 'devices']);
$router->post('/admin/api/devices/revoke/{id}', [App\Controllers\AdminAPIController::class, 'revokeToken']);
$router->post('/admin/api/revoke-user/{id}', [App\Controllers\AdminAPIController::class, 'revokeUserTokens']);
$router->get('/admin/api/logs', [App\Controllers\AdminAPIController::class, 'logs']);
// Admin AI knowledge
$router->get('/admin/ai', [AdminAIController::class, 'index']);
$router->get('/admin/ai-knowledge', [AdminAIController::class, 'index']);
$router->get('/admin/ai-knowledge/create', [AdminAIController::class, 'create']);
$router->post('/admin/ai-knowledge/create', [AdminAIController::class, 'create']);
$router->get('/admin/ai-knowledge/edit/{id}', [AdminAIController::class, 'edit']);
$router->post('/admin/ai-knowledge/edit/{id}', [AdminAIController::class, 'edit']);
$router->get('/admin/brands', [AdminController::class, 'brands']);
$router->get('/account/vehicles', [VehicleController::class, 'index']);
$router->get('/account/vehicles/create', [VehicleController::class, 'create']);
$router->post('/account/vehicles/create', [VehicleController::class, 'store']);
$router->get('/account/vehicles/edit/{id}', [VehicleController::class, 'edit']);
$router->post('/account/vehicles/update/{id}', [VehicleController::class, 'update']);
$router->post('/account/vehicles/delete/{id}', [VehicleController::class, 'delete']);
$router->get('/account/vehicles/history/{id}', [VehicleController::class, 'history']);
$router->get('/account/vehicles/detail/{id}', [VehicleController::class, 'detail']);
$router->get('/account/vehicle-profile', [VehicleProfileController::class, 'index']);
$router->get('/account/vehicle-profile/maintenance', [VehicleProfileController::class, 'maintenance']);
$router->get('/account/vehicle-profile/history', [VehicleProfileController::class, 'history']);
$router->get('/account/vehicle-profile/diagnostics', [VehicleProfileController::class, 'diagnostics']);
// Dedicated vehicle detail page for customers (PATCH_52)
$router->get('/account/vehicle/{id}', [App\Controllers\AccountController::class, 'vehicleDetail']);
$router->get('/account/vehicle/health/{id}', [App\Controllers\AccountController::class, 'vehicleHealthReport']);
$router->post('/account/maintenance/create', [MaintenanceController::class, 'create']);
$router->post('/account/maintenance/status/{id}', [MaintenanceController::class, 'updateStatus']);
$router->get('/admin/articles', [ArticleController::class, 'adminIndex']);
$router->get('/admin/articles/create', [ArticleController::class, 'adminCreate']);
$router->post('/admin/articles/create', [ArticleController::class, 'adminStore']);
$router->get('/admin/articles/edit/{id}', [ArticleController::class, 'adminEdit']);
$router->post('/admin/articles/edit/{id}', [ArticleController::class, 'adminUpdate']);
$router->post('/admin/articles/delete/{id}', [ArticleController::class, 'adminDelete']);
$router->get('/admin/inquiries', [AdminController::class, 'inquiries']);
$router->get('/admin/appointments', [AdminController::class, 'appointments']);
$router->get('/admin/categories', [App\Controllers\ProductCategoryController::class, 'index']);
$router->get('/admin/categories/create', [App\Controllers\ProductCategoryController::class, 'create']);
$router->post('/admin/categories/create', [App\Controllers\ProductCategoryController::class, 'store']);
$router->get('/admin/categories/edit/{id}', [App\Controllers\ProductCategoryController::class, 'edit']);
$router->post('/admin/categories/edit/{id}', [App\Controllers\ProductCategoryController::class, 'update']);
$router->post('/admin/categories/delete/{id}', [App\Controllers\ProductCategoryController::class, 'delete']);
$router->get('/admin/products', [ProductController::class, 'adminIndex']);
$router->get('/admin/products/create', [ProductController::class, 'adminCreate']);
$router->post('/admin/products/create', [ProductController::class, 'adminStore']);
$router->get('/admin/products/edit/{id}', [ProductController::class, 'adminEdit']);
$router->post('/admin/products/edit/{id}', [ProductController::class, 'adminUpdate']);
$router->post('/admin/products/delete/{id}', [ProductController::class, 'adminDelete']);
$router->post('/admin/products/{id}/images/upload', [App\Controllers\AdminProductImagesController::class, 'upload']);
$router->post('/admin/products/{id}/images/delete', [App\Controllers\AdminProductImagesController::class, 'delete']);
$router->post('/admin/products/{id}/images/primary', [App\Controllers\AdminProductImagesController::class, 'setPrimary']);
$router->get('/admin/inventory', [InventoryController::class, 'index']);
$router->get('/admin/orders', [AdminOrderController::class, 'index']);
$router->get('/admin/orders/show/{id}', [AdminOrderController::class, 'show']);
$router->get('/admin/orders/edit/{id}', [AdminOrderController::class, 'edit']);
$router->post('/admin/orders/update-status/{id}', [AdminOrderController::class, 'updateStatus']);
$router->get('/admin/inventory/history', [InventoryController::class, 'history']);
$router->get('/admin/inventory/low-stock', [InventoryController::class, 'lowStock']);
$router->post('/admin/inventory/increase/{id}', [InventoryController::class, 'increase']);
$router->post('/admin/inventory/decrease/{id}', [InventoryController::class, 'decrease']);
$router->post('/admin/inventory/adjust/{id}', [InventoryController::class, 'adjust']);
$router->get('/admin/suppliers', [SupplierController::class, 'index']);
$router->get('/admin/suppliers/create', [SupplierController::class, 'create']);
$router->post('/admin/suppliers/create', [SupplierController::class, 'store']);
$router->get('/admin/suppliers/show/{id}', [SupplierController::class, 'show']);
$router->get('/admin/suppliers/edit/{id}', [SupplierController::class, 'edit']);
$router->post('/admin/suppliers/edit/{id}', [SupplierController::class, 'update']);
$router->post('/admin/suppliers/delete/{id}', [SupplierController::class, 'delete']);
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/ajax-update', [CartController::class, 'ajaxUpdate']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->get('/checkout', [OrderController::class, 'checkout']);
$router->get('/orders', [OrderController::class, 'index']);
$router->get('/orders/history', [OrderController::class, 'history']);
$router->get('/orders/show', [OrderController::class, 'history']);
$router->get('/orders/show/{id}', [OrderController::class, 'show']);
$router->get('/orders/checkout', [OrderController::class, 'checkout']);
$router->post('/orders/place', [OrderController::class, 'placeOrder']);
$router->get('/orders/success/{id}', [OrderController::class, 'success']);
$router->get('/payment/start', [App\Controllers\PaymentController::class, 'start']);
$router->get('/payment/start/{id}', [App\Controllers\PaymentController::class, 'start']);
$router->get('/payment/callback', [App\Controllers\PaymentController::class, 'callback']);
$router->get('/payment/result', [App\Controllers\PaymentController::class, 'result']);
$router->get('/invoice/{id}', [App\Controllers\InvoiceController::class, 'show']);
$router->get('/invoice/show/{id}', [App\Controllers\InvoiceController::class, 'show']);
$router->get('/invoice/{id}/pdf', [App\Controllers\InvoiceController::class, 'downloadPdf']);
$router->get('/checkout/success', [OrderController::class, 'successCheckout']);
$router->get('/checkout/failed', [OrderController::class, 'failedCheckout']);
$router->get('/checkout/success/{id}', [OrderController::class, 'successCheckout']);
$router->get('/checkout/failed/{id}', [OrderController::class, 'failedCheckout']);
$router->get('/admin/invoices', [App\Controllers\InvoiceController::class, 'adminIndex']);
$router->get('/admin/invoices/show/{id}', [App\Controllers\InvoiceController::class, 'adminShow']);
$router->get('/admin/homepage', [AdminController::class, 'homepage']);
$router->get('/admin/seo', [AdminController::class, 'seo']);
$router->get('/admin/media', [AdminController::class, 'media']);
$router->get('/sitemap.xml', function () {
    header('Content-Type: application/xml; charset=UTF-8');

    $siteUrl = rtrim(SITE_URL, '/');
    $articles = (new App\Models\Article())->getVisibleArticles();
    $services = (new App\Models\Service())->getVisibleServices();
    $products = (new App\Models\Product())->getVisibleProducts();

    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    echo "  <url>\n    <loc>{$siteUrl}/</loc>\n    <changefreq>weekly</changefreq>\n    <priority>1.0</priority>\n  </url>\n";
    echo "  <url>\n    <loc>{$siteUrl}/services</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.9</priority>\n  </url>\n";
    echo "  <url>\n    <loc>{$siteUrl}/articles</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n";
    echo "  <url>\n    <loc>{$siteUrl}/shop</loc>\n    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n";
    echo "  <url>\n    <loc>{$siteUrl}/booking</loc>\n    <changefreq>monthly</changefreq>\n    <priority>0.7</priority>\n  </url>\n";

    foreach ($articles as $article) {
        if (empty($article['slug'])) {
            continue;
        }
        $articleUrl = $siteUrl . '/articles/' . rawurlencode($article['slug']);
        $lastmod = !empty($article['updated_at']) ? date('c', strtotime($article['updated_at'])) : (!empty($article['created_at']) ? date('c', strtotime($article['created_at'])) : null);
        echo "  <url>\n    <loc>{$articleUrl}</loc>\n";
        if ($lastmod) { echo "    <lastmod>{$lastmod}</lastmod>\n"; }
        echo "    <changefreq>weekly</changefreq>\n    <priority>0.7</priority>\n  </url>\n";
    }

    foreach ($services as $service) {
        if (empty($service['slug'])) {
            continue;
        }
        $serviceUrl = $siteUrl . '/services/' . rawurlencode($service['slug']);
        $lastmod = !empty($service['updated_at']) ? date('c', strtotime($service['updated_at'])) : (!empty($service['created_at']) ? date('c', strtotime($service['created_at'])) : null);
        echo "  <url>\n    <loc>{$serviceUrl}</loc>\n";
        if ($lastmod) { echo "    <lastmod>{$lastmod}</lastmod>\n"; }
        echo "    <changefreq>weekly</changefreq>\n    <priority>0.8</priority>\n  </url>\n";
    }

    foreach ($products as $product) {
        if (empty($product['slug'])) {
            continue;
        }
        $productUrl = $siteUrl . '/products/' . rawurlencode($product['slug']);
        $lastmod = !empty($product['updated_at']) ? date('c', strtotime($product['updated_at'])) : (!empty($product['created_at']) ? date('c', strtotime($product['created_at'])) : null);
        echo "  <url>\n    <loc>{$productUrl}</loc>\n";
        if ($lastmod) { echo "    <lastmod>{$lastmod}</lastmod>\n"; }
        echo "    <changefreq>weekly</changefreq>\n    <priority>0.6</priority>\n  </url>\n";
    }

    echo '</urlset>';
});
