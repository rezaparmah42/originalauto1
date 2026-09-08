<?php
$title = 'بازرسی فنی خودرو قبل از خرید | ' . SITE_NAME;
$description = 'بازرسی فنی خودرو قبل از خرید، بررسی موتور، ترمز، تعلیق و سیستم الکتریکی برای انتخاب خودروى دقیق و کم‌ریسک.';
$canonical = SITE_URL . '/services/pre-purchase-inspection';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'بازرسی قبل خرید', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">بازرسی فنی</div>
    <h1>بازرسی فنی خودرو قبل از خرید</h1>
    <p>خرید خودرو دست‌دوم، بدون بررسی کامل فنی، ریسک بالایی دارد. در این سرویس، وضعیت موتور، ترمز، گیربکس، تعلیق، برق و ظاهر خودرو به‌صورت دقیق بررسی می‌شود تا تصمیم خرید با آگاهی کامل گرفته شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو بازرسی</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ خودرو</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>موارد بررسی</h2>
            <ul>
                <li>بررسی موتور، روغن و عملکرد کلی</li>
                <li>ترمز، تعلیق و فرمان</li>
                <li>بررسی سیستم برق و سنسورها</li>
                <li>تست کت چراغ‌ها و وضعیت بدنه</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>مزیت اصلی</h2>
            <p>با یک بازرسی دقیق، می‌توانید قبل از خرید، احتمال خرابی‌های پنهان و هزینه‌های بعدی را کاهش دهید.</p>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
