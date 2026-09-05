<?php
$title = 'تعمیر گیربکس خودرو | ' . SITE_NAME;
$description = 'تعمیر گیربکس اتوماتیک و دنده‌ای با تشخیص دقیق علائم خرابی و راهکارهای فنی در اورجینال شرق.';
$canonical = SITE_URL . '/services/gearbox-repair';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">تعمیر گیربکس</div>
    <h1>تشخیص و تعمیر گیربکس قبل از بروز خرابی جدی</h1>
    <p>گیربکس یکی از حساس‌ترین بخش‌های خودروست. اگر تعویض دنده، تاخیر در حرکت یا صدای غیرعادی را تجربه می‌کنید، بررسی تخصصی اهمیت زیادی دارد.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو بررسی گیربکس</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ گیربکس</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>مشکل مشتری</h2>
            <p>علت مراجعه اغلب شامل تاخیر در تعویض دنده، لرزش، صدای غیرعادی و عدم حرکت روان خودرو است.</p>
        </div>
        <div class="detail-card">
            <h2>علائم شایع</h2>
            <ul>
                <li>تأخیر در تعویض دنده</li>
                <li>لرزش یا تکان در حرکت</li>
                <li>صدای تقتق یا دنده‌خوردن</li>
                <li>نشت روغن گیربکس</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>شرح خدمات</h2>
            <p>در این خدمت، وضعیت گیربکس از نظر روغن، سطح فشار، قطعات داخلی و عملکرد دنده‌ها بررسی می‌شود. در صورت نیاز، سرویس، تعمیر یا تعویض قطعه انجام می‌شود.</p>
        </div>
        <div class="detail-card">
            <h2>خودروهای پشتیبانی‌شده</h2>
            <ul>
                <li>خودروهای اتوماتیک و دنده‌ای</li>
                <li>خودروهای داخلی و وارداتی</li>
                <li>خودروهای با گیربکس پیشرفته و چندسرعته</li>
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
            <h3>آیا خرابی گیربکس همیشه نیاز به تعویض قطعه دارد؟</h3>
            <p>نه؛ گاهی با سرویس، تشخیص دقیق و تعویض روغن یا قطعات جزئی، مشکل قابل حل است.</p>
        </div>
        <div class="faq-item">
            <h3>چه زمانی باید فوری به تعمیرگاه مراجعه کرد؟</h3>
            <p>اگر دنده‌خوردن، لرزش شدید یا ناتوانی در حرکت مشاهده شد، مراجعه فوری توصیه می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/services/engine-repair">تعمیر موتور</a>
        <a href="<?= SITE_URL ?>/articles">مقالات گیربکس</a>
        <a href="<?= SITE_URL ?>/vehicles">مشاهده خودروهای پشتیبانی‌شده</a>
    </div>
</section>

<section class="cta">
    <h2>اگر گیربکس خودروتان دچار مشکل شده، از تأخیر پرهیز کنید</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست تعمیر گیربکس</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
