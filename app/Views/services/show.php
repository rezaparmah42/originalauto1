<?php
$serviceModel = new App\Models\Service();
$service = $serviceModel->findBySlug($slug ?? '');
if (!$service) {
    http_response_code(404);
    $title = 'خدمت یافت نشد | ' . SITE_NAME;
    $description = 'خدمت درخواستی در حال حاضر موجود نیست.';
    $canonical = SITE_URL . '/services';
    $robots = 'noindex, follow';
} else {
    $title = (!empty($service['seo_title_fa']) ? $service['seo_title_fa'] : ($service['title_fa'] ?? $service['title_en'] ?? 'خدمات')) . ' | ' . SITE_NAME;
    $description = !empty($service['seo_description_fa']) ? $service['seo_description_fa'] : ($service['description_fa'] ?? '');
    $canonical = SITE_URL . '/services/' . rawurlencode($service['slug'] ?? '');
    $robots = 'index, follow';
    $image = !empty($service['image']) ? SITE_URL . '/uploads/' . ltrim($service['image'], '/') : null;
}
?>
<?php
// Breadcrumb for service
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => $service['title_fa'] ?? ($service['title_en'] ?? 'خدمت'), 'url' => $canonical],
];
require __DIR__.'/../layouts/header.php';
?>

<?php if ($service): ?>
    <?php
    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service['title_fa'] ?? ($service['title_en'] ?? 'خدمت'),
        'description' => mb_substr(strip_tags($description ?? ''), 0, 160),
        'url' => $canonical,
        'provider' => [
            '@type' => 'AutoRepair',
            'name' => SITE_NAME,
            'url' => rtrim(SITE_URL, '/'),
            'telephone' => SITE_PHONE,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => SITE_ADDRESS,
            ],
        ],
        'areaServed' => 'تهران',
    ];
    ?>
    <?php
    // Set OG type and image for header meta
    $og_type = 'service';
    $image = !empty($service['image']) ? SITE_URL . '/uploads/' . ltrim($service['image'], '/') : null;
    $schemaData = $serviceSchema;
    require __DIR__ . '/../partials/schema.php';
    ?>
<?php endif; ?>

<?php if (!$service): ?>
    <section class="page-hero">
        <div class="hero-badge">خدمت یافت نشد</div>
        <h1>این صفحه در حال حاضر در دسترس نیست</h1>
        <p>برای مشاهده خدمات موجود، به صفحه خدمات مراجعه کنید.</p>
        <div class="hero-actions">
            <a class="btn-primary" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
        </div>
    </section>
<?php else: ?>
    <section class="page-hero">
        <div class="hero-badge">خدمات تخصصی</div>
        <h1><?= e($service['title_fa'] ?? ($service['title_en'] ?? 'خدمت')) ?></h1>
        <p><?= e($service['description_fa'] ?? ($service['description_en'] ?? '')) ?></p>
            <?php $heroImg = SITE_URL . '/uploads/services/' . rawurlencode($service['slug'] ?? '') . '/' . rawurlencode(($service['slug'] ?? '')) . '-hero.jpg'; ?>
            <div class="hero-media">
                <img src="<?= $heroImg ?>" alt="<?= e($service['title_fa'] ?? '') ?>" width="1200" height="675" loading="eager" fetchpriority="high" onerror="this.style.display='none'" title="<?= e($service['title_fa'] ?? '') ?>">
            </div>
            <div class="hero-actions">
                <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو این خدمت</a>
                <a class="btn-outline" href="<?= SITE_URL ?>/services">همه خدمات</a>
            </div>
    </section>

    <section class="section-shell">
        <div class="detail-grid">
            <div class="detail-card">
                <h2>جزئیات خدمت</h2>
                <p><strong>مدت زمان:</strong> <?= e($service['duration'] ?? '-') ?></p>
                <p><strong>قیمت:</strong> <?= e($service['price'] ?? '-') ?></p>
                <p><strong>وضعیت:</strong> <?= !empty($service['status']) ? 'فعال' : 'غیر فعال' ?></p>
            </div>
            <div class="detail-card">
                <h2>پیشنهادهای مرتبط</h2>
                <ul>
                    <li><a href="<?= SITE_URL ?>/vehicles">راهنمای خودروها</a></li>
                    <li><a href="<?= SITE_URL ?>/articles">مقالات تخصصی</a></li>
                    <li><a href="<?= SITE_URL ?>/services/diagnostic">دیاگ تخصصی</a></li>
                </ul>
            </div>
        </div>
    </section>
    <?php
    $relatedVehicles = [];
    try {
        $catalog = new \App\Models\VehicleCatalog();
        $relatedVehicles = $catalog->getActiveMatrixModels();
    } catch (\Throwable $e) {
        $relatedVehicles = [];
    }
    ?>
    <?php if (!empty($relatedVehicles)): ?>
        <section class="section-shell">
            <div class="section-heading"><h2>این خدمت برای کدام خودروها؟</h2><p>مدل‌های فعال مناسب برای سرویس موردنظر.</p></div>
            <div class="resource-links">
                <?php foreach (array_slice($relatedVehicles, 0, 8) as $vehicle): ?>
                    <?php $brandName = $vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? ''; $modelName = $vehicle['name_fa'] ?? $vehicle['model'] ?? ''; ?>
                    <a href="<?= SITE_URL ?>/services/<?= rawurlencode($service['slug'] ?? '') ?>/<?= rawurlencode($vehicle['slug'] ?? '') ?>"><?= e($brandName . ' ' . $modelName) ?></a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php
    $subCategories = (new App\Models\ServiceSubcategory())->getByService($service['slug'] ?? '');
    if (!empty($subCategories)):
    ?>
        <section class="section-shell">
            <div class="section-heading"><h2>زیرخدمت‌های <?= e($service['title_fa'] ?? '') ?></h2><p>محبوب‌ترین مسیرهای عیب‌یابی و تعمیر برای این خدمت.</p></div>
                    <?php $midImg = SITE_URL . '/uploads/services/' . rawurlencode($service['slug'] ?? '') . '/' . rawurlencode(($service['slug'] ?? '')) . '-mid.jpg'; ?>
                    <div class="hero-media mid">
                        <img src="<?= $midImg ?>" alt="نمای خدمات <?= e($service['title_fa'] ?? '') ?>" width="800" height="600" loading="lazy" onerror="this.style.display='none'" title="<?= e($service['title_fa'] ?? '') ?>">
                    </div>
            <div class="service-grid">
                <?php foreach (array_slice($subCategories, 0, 8) as $sub): ?>
                    <a class="service-card" href="<?= SITE_URL ?>/services/<?= rawurlencode($service['slug'] ?? '') ?>/<?= rawurlencode($sub['slug'] ?? '') ?>">
                        <span class="meta-pill">زیرخدمت</span>
                        <h3><?= e($sub['title_fa'] ?? '') ?></h3>
                        <p><?= e($sub['intro'] ?? '') ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
    <?php
    $relatedServices = array_values(array_filter((new App\Models\Service())->getVisibleServices(), static function ($item) use ($service) {
        return (int) ($item['id'] ?? 0) !== (int) ($service['id'] ?? 0);
    }));
    ?>
    <?php if (!empty($relatedServices)): ?>
        <section class="section-shell">
            <div class="section-heading"><h2>خدمات مرتبط</h2><p>مسیرهای تکمیلی برای نگهداری و تعمیر خودرو.</p></div>
            <div class="service-grid">
                <?php foreach (array_slice($relatedServices, 0, 3) as $related): ?><a class="service-card" href="<?= SITE_URL ?>/services/<?= rawurlencode($related['slug'] ?? '') ?>"><h3><?= e($related['title_fa'] ?? '') ?></h3><p><?= e($related['description_fa'] ?? '') ?></p></a><?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__.'/../layouts/footer.php'; ?>