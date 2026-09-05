<?php
$title = 'صفحه خودروها | ' . SITE_NAME;
$description = 'انتخاب دسته خودرو و راهنمای تعمیرات خودروهای داخلی، چینی، کره‌ای، ژاپنی و آلمانی در اورجینال شرق.';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">راهنمای خودرو و تعمیرات تخصصی</div>
    <h1>شناخت بهتر خودرو، راهی برای تشخیص دقیق‌تر و تعمیر مطمئن‌تر</h1>
    <p>در این بخش خودروهای رایج بازار ایران بر اساس برند و نوع استفاده دسته‌بندی شده‌اند تا شما بتوانید سریع‌تر به خدمات مرتبط دسترسی پیدا کنید.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">مشاوره تخصصی خودرو</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>دسته‌های خودرو</h2>
        <p>هر دسته برای نوع خاصی از تعمیر و نگهداری، مسیر خدمات اختصاصی دارد.</p>
    </div>
    <div class="service-grid">
        <article class="service-card">
            <span class="meta-pill">داخلی</span>
            <h3>خودروهای داخلی</h3>
            <p>پژو، سمند، دنا، تیبا و پراید با مشکلات رایج مانند درجا زدن، روشن نشدن و مصرف بالا.</p>
            <a class="text-link" href="<?= SITE_URL ?>/brands/iranian">مشاهده خدمات داخلی</a>
        </article>

        <article class="service-card">
            <span class="meta-pill">چینی</span>
            <h3>خودروهای چینی</h3>
            <p>چری، ام‌وی‌ام، جک و فونیکس با عیوب برقی، سنسورهای دما و خرابی‌های گیربکس نیمه‌اتومات.</p>
            <a class="text-link" href="<?= SITE_URL ?>/brands/chinese">مشاهده خدمات چینی</a>
        </article>

        <article class="service-card">
            <span class="meta-pill">کره‌ای</span>
            <h3>خودروهای کره‌ای</h3>
            <p>کیا و هیوندای؛ سیستم جلوبندی، برق، دیاگ و نگهداری گیربکس اتوماتیک.</p>
            <a class="text-link" href="<?= SITE_URL ?>/brands/korean">مشاهده خدمات کره‌ای</a>
        </article>

        <article class="service-card">
            <span class="meta-pill">ژاپنی</span>
            <h3>خودروهای ژاپنی</h3>
            <p>تویوتا، نیسان، لکسوس؛ با تمرکز بر سیستم‌های موتور، برق و سرویس دوره‌ای تخصصی.</p>
            <a class="text-link" href="<?= SITE_URL ?>/brands/japanese">مشاهده خدمات ژاپنی</a>
        </article>

        <article class="service-card">
            <span class="meta-pill">آلمانی</span>
            <h3>خودروهای آلمانی</h3>
            <p>بنز، بی‌ام‌و؛ تعمیر تخصصی موتور، گیربکس و سیستم‌های الکترونیکی پیشرفته.</p>
            <a class="text-link" href="<?= SITE_URL ?>/brands/german">مشاهده خدمات آلمانی</a>
        </article>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>خرابی‌های رایج بر اساس برند</h2>
            <ul>
                <li>داخلی: مصرف سوخت بالا، داغ شدن موتور، سیستم برق و سنسورها</li>
                <li>چینی: مشکلات ECU، سنسور دمای آب، گیربکس نیمه‌اتومات و سیستم‌های الکتریکی</li>
                <li>ژاپنی: خرابی سنسور اکسیژن، باتری و قطعات سیستم سوخت‌رسانی</li>
                <li>آلمانی: حساسیت سنسورهای موتور، گیربکس و سیستم تهویه</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>هدف ما</h2>
            <p>ایجاد مرجع اطلاعاتی برای خودروهای مختلف و تبدیل کردن تجربه فنی ما به محتوای کاربردی برای کاربران، به‌‌گونه‌ای که هم برای جست‌وجو و هم برای تصمیم‌گیری در تعمیر، ارزش واقعی داشته باشد.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>پیشنهادهای مرتبط</h2>
        <p>از این لینک‌ها برای حرکت سریع‌تر بین خدمات، مقالات و راهنمای خودرو استفاده کنید.</p>
    </div>
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/services/diagnostic">دیاگ تخصصی</a>
        <a href="<?= SITE_URL ?>/services/electrical-repair">تعمیر برق</a>
        <a href="<?= SITE_URL ?>/articles">مقالات فنی</a>
        <a href="<?= SITE_URL ?>/booking">رزرو فوری</a>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
