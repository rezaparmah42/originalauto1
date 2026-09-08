<?php
$title = 'تعمیر و اورهال تخصصی موتور خودرو | ' . SITE_NAME;
$description = 'تعمیر موتور خودرو، بررسی روغن‌سوزی، سرسیلندر، تسمه تایم و اورهال تخصصی در اورجینال شرق.';
$canonical = SITE_URL . '/services/engine';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'تعمیر موتور', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'serviceType' => 'تعمیر موتور خودرو',
    'provider' => ['@type' => 'AutoRepair', 'name' => SITE_NAME, 'telephone' => SITE_PHONE],
    'description' => 'تعمیر و اورهال موتور خودرو، بررسی روغن‌سوزی، سرسیلندر و عملکرد موتور.',
    'areaServed' => 'تهران',
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

<section class="page-hero">
    <div class="hero-badge">موتور و اورهال</div>
    <h1>تعمیر و اورهال تخصصی موتور خودرو</h1>
    <p>موتور خودرو قلب عملکردی ماشین است و هر نوع نشانه‌ای مانند دود آبی، افت قدرت، ضربه در موتور یا افزایش مصرف سوخت نشان می‌دهد که باید بررسی دقیق انجام شود. در اورجینال شرق، بررسی موتور با روش‌های فنی و ابزار دقیق انجام می‌شود تا علت اصلی مشکل پیدا شود و در صورت نیاز، تعمیر یا اورهال با دقت انجام گردد.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعمیر موتور</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ موتور</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>نشانه‌های نیاز به تعمیر موتور</h2>
            <ul>
                <li>دود آبی از اگزوز و مصرف روغن بالا</li>
                <li>افت شدید شتاب، لرزش یا ضربه در دورهای بالا</li>
                <li>صدای غیرعادی از موتور یا ضربه زدن در زمان گرم شدن</li>
                <li>اشکال در روشن شدن، خاموشی یا عملکرد نامنظم</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>دلایل رایج خرابی</h2>
            <ul>
                <li>سایش قطعات داخلی مثل رینگ و پیستون</li>
                <li>خرابی تسمه تایم یا زمان‌بندی نامناسب</li>
                <li>نشت روغن، سرسیلندر یا واشر سرسیلندر</li>
                <li>مشکل در سیستم سوخت‌رسانی و احتراق</li>
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
            <h3>۱) بررسی اولیه و شنیدن مشکل</h3>
            <p>در ابتدا، علائم مشکل، سابقه خودرو و عملکرد موتور در زمان‌های مختلف بررسی می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۲) دیاگ و تست عملکرد</h3>
            <p>با دستگاه‌های مناسب، عملکرد موتور، کدهای خطا و حالت‌های کاری آن ارزیابی می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۳) اعلام برآورد</h3>
            <p>پس از تشخیص علت، برآورد دقیق تعمیر یا اورهال به شما ارائه می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۴) اجرا و تست نهایی</h3>
            <p>کار تعمیر انجام شده و با تست جاده‌ای، عملکرد موتور دوباره کنترل می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>خودروهای تحت پوشش</h2>
    </div>
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/brands/saipa">سایپا</a>
        <a href="<?= SITE_URL ?>/brands/iran-khodro">ایران‌خودرو</a>
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
            <h3>آیا مصرف روغن بالا همیشه نشانه خرابی موتور است؟</h3>
            <p>معمولاً بله، اما علت دقیق آن می‌تواند از نشت روغن، استهلاک رینگ و پیستون یا مشکل در سیستمهای جانبی باشد که باید بررسی شود.</p>
        </div>
        <div class="faq-item">
            <h3>آیا اورهال موتور لازم است؟</h3>
            <p>در بعضی موارد این تصمیم بر اساس وضعیت قطعات و شدت سایش گرفته می‌شود. ما با بررسی فنی دقیق، بهترین مسیر را به شما پیشنهاد می‌کنیم.</p>
        </div>
    </div>
</section>

<section class="cta">
    <h2>اگر موتور خودرو شما دود می‌دهد یا افت قدرت دارد، بررسی دقیق را جدی بگیرید</h2>
    <p>تأخیر در تعمیر موتور می‌تواند منجر به هزینه‌های بیشتر و آسیب به قطعات جانبی شود.</p>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">ثبت درخواست تعمیر</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
