<?php
$title = 'تعویض فیلترهای خودرو | ' . SITE_NAME;
$description = 'تعویض فیلترهای خودرو، فیلتر روغن، هوا و کابین برای عملکرد بهتر موتور و کاهش مصرف سوخت.';
$canonical = SITE_URL . '/services/car-air-filter-and-filters';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'تعویض فیلترها', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">فیلترها</div>
    <h1>تعویض فیلترهای خودرو</h1>
    <p>فیلترهای روغن، هوا، سوخت و کابین اگر به‌طور منظم تعویض نشوند، عملکرد موتور و کیفیت هوای داخل خودرو تحت تأثیر قرار می‌گیرد. این سرویس یکی از ساده‌ترین راه‌ها برای حفظ سلامت خودرو و بهبود کارایی آن است.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعویض فیلتر</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/periodic-service">سرویس دوره‌ای</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>فیلترهای مهم</h2>
            <ul>
                <li>فیلتر روغن موتور</li>
                <li>فیلتر هوا</li>
                <li>فیلتر کابین</li>
                <li>فیلتر سوخت</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>نشانه‌های نیاز به تعویض</h2>
            <ul>
                <li>کاهش قدرت موتور</li>
                <li>افزایش مصرف سوخت</li>
                <li>بوی نامطبوع داخل کابین</li>
                <li>کثیفی یا رسوب در فیلترها</li>
            </ul>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
