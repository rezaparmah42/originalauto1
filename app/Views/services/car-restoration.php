<?php
$serviceModel = new App\Models\Service();
$service = $serviceModel->findBySlug($slug ?? 'car-restoration');
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
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => $service['title_fa'] ?? ($service['title_en'] ?? 'بازسازی خودروهای فرسوده و تصادفی'), 'url' => $canonical],
];
require __DIR__.'/../layouts/header.php';
?>

<?php if ($service): ?>
    <?php
    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service['title_fa'] ?? ($service['title_en'] ?? 'بازسازی خودروهای فرسوده و تصادفی'),
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
        <div class="hero-badge">خدمات ویژه</div>
        <h1>بازسازی خودروهای فرسوده و تصادفی</h1>
        <p>بازگرداندن خودروهای آسیب دیده به شرایط ایمن، فنی و ظاهری مناسب با کارشناسی و تعمیرات تخصصی.</p>
        <div class="hero-actions">
            <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو کارشناسی</a>
            <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده سایر خدمات</a>
        </div>
    </section>

    <section class="section-shell">
        <div class="section-heading">
            <h2>چرا بازسازی خودرو اهمیت دارد؟</h2>
        </div>
        <div class="detail-card">
            <p>برای خودروهای تصادفی، فرسوده یا آسیب دیده، همیشه تعویض خودرو بهترین راه نیست. با کارشناسی صحیح می‌توان بخش‌های فنی و ظاهری خودرو را بازسازی کرد.</p>
        </div>
    </section>

    <section class="section-shell">
        <div class="section-heading">
            <h2>مراحل بازسازی</h2>
        </div>
        <div class="service-grid">
            <article class="service-card">
                <span class="meta-pill">مرحله ۱</span>
                <h3>کارشناسی اولیه و بررسی میزان آسیب</h3>
            </article>
            <article class="service-card">
                <span class="meta-pill">مرحله ۲</span>
                <h3>بررسی شاسی و بدنه</h3>
            </article>
            <article class="service-card">
                <span class="meta-pill">مرحله ۳</span>
                <h3>تعمیر موتور و گیربکس</h3>
            </article>
            <article class="service-card">
                <span class="meta-pill">مرحله ۴</span>
                <h3>تعمیر سیستم برق و ECU</h3>
            </article>
            <article class="service-card">
                <span class="meta-pill">مرحله ۵</span>
                <h3>صافکاری، رنگ و بازسازی ظاهری</h3>
            </article>
            <article class="service-card">
                <span class="meta-pill">مرحله ۶</span>
                <h3>تست نهایی و تحویل خودرو</h3>
            </article>
        </div>
    </section>

    <section class="section-shell">
        <div class="section-heading">
            <h2>خدمات قابل انجام</h2>
        </div>
        <div class="service-grid">
            <article class="service-card">
                <h3>تعمیر موتور</h3>
            </article>
            <article class="service-card">
                <h3>تعمیر گیربکس اتوماتیک</h3>
            </article>
            <article class="service-card">
                <h3>تعمیر برق و ECU</h3>
            </article>
            <article class="service-card">
                <h3>خدمات CNG</h3>
            </article>
            <article class="service-card">
                <h3>صافکاری و رنگ</h3>
            </article>
            <article class="service-card">
                <h3>تامین و تعویض قطعات</h3>
            </article>
        </div>
    </section>

    <section class="cta">
        <h2>برای بازسازی خودرو خودتان آماده اقدام هستید؟</h2>
        <p>با هماهنگی کارشناسی اولیه، مسیر تعمیر و بازسازی خودرو شما به‌صورت دقیق و شفاف تعیین می‌شود.</p>
        <div class="hero-actions">
            <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو کارشناسی</a>
            <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات دیگر</a>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__.'/../layouts/footer.php'; ?>
