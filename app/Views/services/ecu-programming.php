<?php
$title = 'برنامه‌ریزی و کدنویسی ECU | ' . SITE_NAME;
$description = 'خدمات برنامه‌ریزی و کدنویسی ECU برای خودروهای مدرن با رویکرد تخصصی و ایمن در اورجینال شرق.';
$canonical = SITE_URL . '/services/ecu-programming';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">ECU Programming</div>
    <h1>کدنویسی و ارتقاء ECU با دقت و ایمنی بالا</h1>
    <p>برای خودروهای جدید و مدرن، برنامه‌ریزی ECU یکی از مهم‌ترین خدمات تخصصی است که در صورت نیاز به تعویض ماژول، نصب قطعه یا رفع خطاهای نرم‌افزاری انجام می‌شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو برنامه‌ریزی ECU</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">مشاوره دیاگ</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>مشکل مشتری</h2>
            <p>در صورت تعویض ECU، نصب سنسور جدید، خطای نرم‌افزاری یا نیاز به تنظیمات کارکرد، برنامه‌ریزی مجدد ECU ضروری می‌شود.</p>
        </div>
        <div class="detail-card">
            <h2>علائم شایع</h2>
            <ul>
                <li>خطای راه‌اندازی پس از تعویض ماژول</li>
                <li>اختلال در عملکرد موتور و گیربکس</li>
                <li>بروز خطاهای ECU و کدهای ناشناخته</li>
                <li>نیاز به کالیبراسیون پس از نصب قطعه</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>شرح خدمات</h2>
            <p>در این خدمت، ابتدا اطلاعات فنی خودرو بررسی می‌شود، سپس با استفاده از ابزارهای مناسب، برنامه و پارامترهای لازم به‌صورت دقیق روی ECU اعمال می‌شود. این فرآیند برای جلوگیری از خرابی‌های بعدی و بهبود عملکرد انجام می‌شود.</p>
        </div>
        <div class="detail-card">
            <h2>خودروهای پشتیبانی‌شده</h2>
            <ul>
                <li>خودروهای مدرن با سیستم کنترل الکترونیکی</li>
                <li>خودروهای ژاپنی و کره‌ای</li>
                <li>خودروهای آلمانی و چندپلتفرمی</li>
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
            <h3>آیا ECU programming برای همه خودروها قابل اجراست؟</h3>
            <p>خیر؛ بسته به مدل خودرو، نوع کنترلر و دسترسی ابزار، بعضی خودروها نیازمند بررسی تخصصی قبل از انجام خدمت هستند.</p>
        </div>
        <div class="faq-item">
            <h3>آیا این خدمت خطرناک است؟</h3>
            <p>در صورت انجام حرفه‌ای و با ابزار استاندارد، خطر بسیار کم است و خروجی‌های لازم با دقت پایش می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/services/diagnostic">دیاگ تخصصی</a>
        <a href="<?= SITE_URL ?>/articles">مقالات فنی</a>
        <a href="<?= SITE_URL ?>/vehicles">راهنمای خودروها</a>
    </div>
</section>

<section class="cta">
    <h2>برای برنامه‌ریزی ECU یا رفع خطاهای نرم‌افزاری، با ما در تماس باشید</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست خدمات ECU</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
