<?php
$title = 'تعمیر جلوبندی و تعلیق خودرو | ' . SITE_NAME;
$description = 'تعمیر جلوبندی و تعلیق خودرو؛ بررسی سیبک، کمک‌فنر، طبق، بوش و تنظیم زوایا برای فرمان بهتر و آرامش بیشتر.';
$canonical = SITE_URL . '/services/suspension';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'تعلیق و جلوبندی', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">تعلیق و جلوبندی</div>
    <h1>تعمیر جلوبندی و تعلیق خودرو</h1>
    <p>سیستم تعلیق و جلوبندی نقش مهمی در راحتی سرنشینان و ایمنی خودرو دارد. اگر در عبور از دستانداز، ترمز یا پیچ‌ها لرزش یا فرمان‌پذیری نامنظم احساس می‌کنید، بهتر است وضعیت تعلیق خودرو بررسی شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعلیق</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/brakes">بررسی ترمز</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>نشانه‌های خرابی</h2>
            <ul>
                <li>لرزش بیش از حد در سر پیچ‌ها یا هنگام عبور از دستانداز</li>
                <li>صدای تقتق یا کوبیدن از قسمت چرخ‌ها</li>
                <li>لغزش یا انحراف خودرو هنگام ترمزگیری</li>
                <li>فرسودگی یا ترک خوردگی در کمک‌فنر یا بوش‌ها</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>قطعات اصلی</h2>
            <ul>
                <li>سیبک، طبق، بوش و کمک‌فنر</li>
                <li>میل‌گاردان و قطعات فرمان</li>
                <li>بلبرینگ و مجموعه‌های تعلیق</li>
            </ul>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>