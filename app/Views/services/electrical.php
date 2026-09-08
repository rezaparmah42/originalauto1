<?php
$title = 'تعمیر برق خودرو | ' . SITE_NAME;
$description = 'تعمیر برق خودرو و سیستم الکترونیک؛ بررسی باتری، دینام، استارت، سیم‌کشی، چراغ‌ها و قفل مرکزی با عیب‌یابی تخصصی.';
$canonical = SITE_URL . '/services/electrical';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'تعمیر برق خودرو', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'serviceType' => 'تعمیر برق خودرو',
    'provider' => ['@type' => 'AutoRepair', 'name' => SITE_NAME, 'telephone' => SITE_PHONE],
    'description' => 'تعمیر برق خودرو و سیستم الکترونیک؛ باتری، دینام، استارت، چراغ، قفل مرکزی و سیم‌کشی.',
    'areaServed' => 'تهران',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

<section class="page-hero">
    <div class="hero-badge">برق خودرو</div>
    <h1>تعمیر برق خودرو</h1>
    <p>مشکلات برق خودرو معمولاً با رفتار غیرمنتظره و گاهی خطرناک خود را نشان می‌دهند؛ روشن نشدن خودرو، خاموشی ناگهانی، چراغ‌های نامنظم، مشکل در شیشه برقی و قفل مرکزی از جمله نشانه‌های اصلی هستند. در اورجینال شرق، این مشکلات با عیب‌یابی دقیق و بررسی مدارهای الکترونیکی برطرف می‌شوند.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو خدمت</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">مشاوره دیاگ</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>علائم خرابی برق خودرو</h2>
            <ul>
                <li>خاموشی ناگهانی خودرو در حال حرکت یا هنگام روشن شدن</li>
                <li>مشکل در استارت و روشن شدن موتور</li>
                <li>روشن و خاموش شدن چراغ‌ها یا مصرف ناگهانی برق</li>
                <li>خراب شدن شیشه برقی، آینه‌ها یا قفل مرکزی</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>دلایل رایج</h2>
            <ul>
                <li>فرسودگی باتری یا اتصالات ضعیف</li>
                <li>خرابی دینام یا تنظیم ولتاژ نامناسب</li>
                <li>سیم‌کشی و ترمینال‌های خراب</li>
                <li>خراب بودن رله، سنسور یا مدار الکترونیکی</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>مراحل انجام کار</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>۱) پذیرش و بررسی اولیه</h3>
            <p>اولین قدم، شنیدن دقیق مشکل از مشتری و بررسی علائم فیزیکی مدارها، باتری و اتصالات است.</p>
        </div>
        <div class="faq-item">
            <h3>۲) عیب‌یابی تخصصی</h3>
            <p>با دستگاه‌های مناسب، ولتاژ، جریان و عملکرد هر بخش بررسی می‌شود تا علت اصلی مشخص شود.</p>
        </div>
        <div class="faq-item">
            <h3>۳) برآورد و تأیید</h3>
            <p>پیش از انجام کار، وضعیت خرابی و میزان تعمیر به شما اعلام می‌شود تا تصمیم نهایی گرفته شود.</p>
        </div>
        <div class="faq-item">
            <h3>۴) تعمیر و تست نهایی</h3>
            <p>پس از اصلاح مدار یا قطعه، تست عملکرد سیستم انجام می‌شود و در صورت نیاز، راهنمایی نگهداری هم ارائه می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>خودروهای تحت پوشش</h2>
    </div>
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/brands/iran-khodro">ایران‌خودرو</a>
        <a href="<?= SITE_URL ?>/brands/saipa">سایپا</a>
        <a href="<?= SITE_URL ?>/brands/toyota">تویوتا</a>
        <a href="<?= SITE_URL ?>/brands/hyundai">هیوندای</a>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا مشکل برق خودرو همیشه به باتری مربوط است؟</h3>
            <p>نه، گاهی مشکل از دینام، رله، سنسور یا سیم‌کشی می‌آید و فقط با عیب‌یابی دقیق مشخص می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>آیا استارت و دینام را هم بررسی می‌کنید؟</h3>
            <p>بله، این دو بخش از مهم‌ترین اجزای سیستم برق خودرو هستند و بررسی کامل آن‌ها برای سنجش عملکرد درست خودرو ضروری است.</p>
        </div>
    </div>
</section>

<section class="cta">
    <h2>اگر خودرو خاموش می‌شود یا چراغ‌ها نامنظم کار می‌کنند، همین حالا بررسی کنید</h2>
    <p>برق خودرو نیاز به تشخیص دقیق دارد؛ بدون بررسی درست، تعویض قطعه بدون علت ممکن است مشکل را حل نکند.</p>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعمیر برق</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
