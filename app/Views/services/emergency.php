<?php
$title = 'امداد خودرو و خدمات سیار | ' . SITE_NAME;
$description = 'امداد خودرو و خدمات سیار، روشن‌کردن خودرو، راه‌اندازی باتری، کمک در محل و خدمات اضطراری در اورجینال شرق.';
$canonical = SITE_URL . '/services/emergency';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'امداد خودرو', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">امداد و خدمات سیار</div>
    <h1>امداد خودرو و خدمات سیار</h1>
    <p>در شرایط اضطراری، زمان بسیار مهم است. خدمات سیار می‌تواند برای روشن‌کردن خودرو، کمک به باتری ضعیف، یا بررسی اولیه در محل، به شما کمک کند تا در سریع‌ترین زمان ممکن مسیر جاده‌ای خود را از سر بگیرید.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست امداد</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/contact">تماس سریع</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>خدمات قابل ارائه</h2>
            <ul>
                <li>روشن‌کردن خودرو در صورت خالی بودن باتری</li>
                <li>بررسی اولیه در محل</li>
                <li>کمک برای وضعیت‌های اضطراری ساده</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>نکته مهم</h2>
            <p>در شرایط پیچیده‌تر، بررسی کامل در تعمیرگاه برای پیدا کردن علت واقعی مشکل ضروری است.</p>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
