<?php
$title = 'تعمیر موتور خودرو | ' . SITE_NAME;
$description = 'تعمیر موتور خودرو، عیب‌یابی سیستم احتراق و رفع مشکلات عملکردی با خدمات تخصصی در اورجینال شرق.';
$canonical = SITE_URL . '/services/engine-repair';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">تعمیر موتور</div>
    <h1>بازسازی و تعمیر موتور با رویکرد فنی دقیق</h1>
    <p>موتور از مهم‌ترین بخش‌های خودروست. کاهش قدرت، صداهای غیرعادی، روشن نشدن و دود خروجی همگی نشانه‌هایی از نیاز به بررسی تخصصی موتور هستند.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعمیر موتور</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ موتور</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>مشکل مشتری</h2>
            <p>درخواست تعمیر موتور معمولاً به دلیل افت قدرت، دود زیاد، لرزش، روشن نشدن یا خرابی قطعات داخلی موتور است.</p>
        </div>
        <div class="detail-card">
            <h2>علائم شایع</h2>
            <ul>
                <li>افت قدرت و شتاب</li>
                <li>دود سفید/آبی/سیاه</li>
                <li>لرزش در دور آرام</li>
                <li>نشت روغن و صداهای غیرعادی</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>شرح خدمات</h2>
            <p>تشخیص اولیه، بررسی قطعات داخلی، تعویض یا تعمیر اجزای معیوب و بازسازی بخش‌های آسیب‌دیده از جمله مراحل اصلی تعمیر موتور است.</p>
        </div>
        <div class="detail-card">
            <h2>خودروهای پشتیبانی‌شده</h2>
            <ul>
                <li>خودروهای داخلی و وارداتی</li>
                <li>موتورهای بنزینی و دیزلی</li>
                <li>خودروهای با نیاز به تعمیر تخصصی موتور</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا همه مشکلات موتور نیاز به بازسازی کامل دارند؟</h3>
            <p>خیر؛ بسیاری از خرابی‌ها با تعمیر جزئی، سرویس یا تعویض قطعه قابل حل‌اند.</p>
        </div>
        <div class="faq-item">
            <h3>چه زمانی باید برای موتور اقدام فوری کرد؟</h3>
            <p>در صورت دود شدید، صداهای غیرعادی یا خاموش شدن ناگهانی، فوراً اقدام کنید.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/services/gearbox-repair">تعمیر گیربکس</a>
        <a href="<?= SITE_URL ?>/articles">مقالات موتور</a>
        <a href="<?= SITE_URL ?>/vehicles">راهنمای خودروها</a>
    </div>
</section>

<section class="cta">
    <h2>برای تعمیر موتور و جلوگیری از آسیب بیشتر، همین حالا مشاوره بگیرید</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست تعمیر موتور</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
