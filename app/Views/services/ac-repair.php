<?php
$title = 'تعمیر کولر خودرو | ' . SITE_NAME;
$description = 'تعمیر سیستم تهویه و کولر خودرو با تشخیص دقیق علائم خرابی و خدمات تخصصی در اورجینال شرق.';
$canonical = SITE_URL . '/services/ac-repair';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">تعمیر کولر خودرو</div>
    <h1>بهبود عملکرد کولر و سیستم تهویه خودرو</h1>
    <p>کولر خودرو در فصول گرم اهمیت زیادی دارد. در این صفحه، علائم خرابی، دلیلی که مشتری‌ها مراجعه می‌کنند، روش تعمیر و خودروهای پشتیبانی‌شده توضیح داده می‌شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعمیر کولر</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ و بررسی</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>مشکل مشتری</h2>
            <p>درخواست تعمیر کولر معمولاً به دلیل عدم سرمایش کافی، بوی بد، صدای اضافی یا نشت گاز است.</p>
        </div>
        <div class="detail-card">
            <h2>علائم شایع</h2>
            <ul>
                <li>عدم خنک‌کاری مناسب</li>
                <li>صدای غیرعادی از کمپرسور</li>
                <li>بوی بد و رطوبت داخل کابین</li>
                <li>نشت گاز یا افت فشار</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>شرح خدمات</h2>
            <p>در این خدمت، سیستم تهویه خودرو از نظر کمپرسور، لوله‌ها، گاز و عملکرد فن بررسی می‌شود. در صورت لزوم، تعمیر، شارژ یا تعویض قطعه انجام می‌شود.</p>
        </div>
        <div class="detail-card">
            <h2>خودروهای پشتیبانی‌شده</h2>
            <ul>
                <li>خودروهای داخلی و وارداتی</li>
                <li>خودروهای با سیستم تهویه پیشرفته</li>
                <li>خودروهای دارای کولر و فن مرکزی</li>
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
            <h3>آیا کولر خودرو همیشه نیاز به شارژ دارد؟</h3>
            <p>نه؛ گاهی مشکل از کمپرسور، لوله‌ها یا فن است و شارژ به تنهایی کافی نیست.</p>
        </div>
        <div class="faq-item">
            <h3>چه زمانی کولر باید تعمیر شود؟</h3>
            <p>اگر سرمایش کاهش یافته یا صدای غیرعادی از سیستم تهویه شنیده می‌شود، بهتر است بررسی شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/services/electrical-repair">تعمیر برق خودرو</a>
        <a href="<?= SITE_URL ?>/articles">مقالات تهویه و کولر</a>
        <a href="<?= SITE_URL ?>/vehicles">راهنمای خودروها</a>
    </div>
</section>

<section class="cta">
    <h2>برای رفع مشکل کولر خودرو، آماده‌ی کمک هستیم</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست تعمیر کولر</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
