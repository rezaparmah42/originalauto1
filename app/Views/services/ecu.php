<?php
$title = 'ریمپ ECU و تیونینگ تخصصی | ' . SITE_NAME;
$description = 'ریمپ ECU و تیونینگ تخصصی خودرو، بهینه‌سازی عملکرد موتور و رفع خطاهای سخت‌افزاری با ابزارهای حرفه‌ای.';
$canonical = SITE_URL . '/services/ecu';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'ریمپ ECU', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">ECU و تیونینگ</div>
    <h1>ریمپ ECU و تیونینگ تخصصی</h1>
    <p>در برخی خودروها، هدف از بهینه‌سازی، افزایش عملکرد و هماهنگی بهتر بین ECU و سیستم‌های موتور است. این نوع خدمات باید با دقت بالا، تشخیص درست و ابزارهای مناسب انجام شود تا نتیجه هم عملکردی بهتر بدهد و هم سلامت تجهیزات حفظ شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو ECU</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ تخصصی</a>
    </div>
</section>

<section class="section-shell">
    <div class="faq-list">
        <div class="faq-item">
            <h3>ریمپ ECU چه زمانی مناسب است؟</h3>
            <p>وقتی علاقه‌مندید عملکرد موتور بهتر و هماهنگی سیستم سوخت‌رسانی و احتراق بهینه‌تر شود، بررسی و برنامه‌ریزی ECU می‌تواند راه‌حل مناسبی باشد.</p>
        </div>
        <div class="faq-item">
            <h3>آیا انجام این خدمات بدون خطر است؟</h3>
            <p>اگر با ابزار و تخصص مناسب انجام شود، تشخیص دقیق و تنظیم اصولی باعث کاهش خطر و بهبود عملکرد می‌شود.</p>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
