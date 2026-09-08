<?php
$title = 'سرویس دوره‌ای خودرو | ' . SITE_NAME;
$description = 'سرویس دوره‌ای خودرو، تعویض روغن موتور، فیلترها و چک‌اپ تخصصی برای نگهداری بهتر و کاهش هزینه‌های آینده.';
$canonical = SITE_URL . '/services/periodic-service';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'سرویس دوره‌ای', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">سرویس دوره‌ای</div>
    <h1>سرویس دوره‌ای خودرو</h1>
    <p>سرویس دوره‌ای، راهکار ساده و مؤثر برای حفظ عملکرد بهینه موتور، سیستم ترمز، تعلیق و سایر بخش‌های حیاتی است. با انجام سرویس طبق برنامه، عمر خودرو حفظ می‌شود و احتمال خرابی‌های ناگهانی کاهش پیدا می‌کند.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو سرویس</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>فهرست سرویس‌های معمول</h2>
            <ul>
                <li>تعویض روغن موتور و فیلتر آن</li>
                <li>بررسی فیلتر هوا و فیلتر کابین</li>
                <li>بازدید سیم‌کشی، باتری و ترمز</li>
                <li>کنترل سطح مایعات و عملکرد سیستم خنک‌کننده</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>چرا مهم است؟</h2>
            <p>در سرویس دوره‌ای، مشکلات کوچک در زمان مناسب شناسایی و اصلاح می‌شوند و از تبدیل‌شدن آن‌ها به خرابی‌های بزرگ جلوگیری می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="faq-list">
        <div class="faq-item">
            <h3>سرویس هر ۱۵ هزار کیلومتر چه مواردی را شامل می‌شود؟</h3>
            <p>تعویض روغن، بررسی سطح مایعات، بازدید تسمه‌ها، فیلترها و سیستم ترمز از مهم‌ترین موارد هستند.</p>
        </div>
        <div class="faq-item">
            <h3>آیا سرویس دوره‌ای برای خودروهای وارداتی هم انجام می‌شود؟</h3>
            <p>بله، با رعایت استانداردهای هر خودرو، خدمات دوره‌ای برای خودروهای داخلی و وارداتی انجام می‌شود.</p>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
