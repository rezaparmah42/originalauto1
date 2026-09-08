<?php
$title = 'سرویس و تعمیر سیستم ترمز | ' . SITE_NAME;
$description = 'سرویس ترمز خودرو، بررسی لنت و دیسک ترمز، روغن ترمز و کالیپر با ایمنی بالا در اورجینال شرق.';
$canonical = SITE_URL . '/services/brakes';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'ترمز', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">سیستم ترمز</div>
    <h1>سرویس و تعمیر سیستم ترمز</h1>
    <p>ترمز یکی از مهم‌ترین سیستم‌های ایمنی خودرو است. هر گونه کاهش قدرت ترمز، صدا، لرزش یا افزایش مسیر توقف باید جدی گرفته شود. در اورجینال شرق، بررسی لنت، دیسک، روغن ترمز و کالیپر با دقت انجام می‌شود تا ایمنی خودرو بهبود یابد.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو ترمز</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/suspension">مشاوره تعلیق</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>علائم خرابی ترمز</h2>
            <ul>
                <li>ترمز زدن نامنظم یا کشیدن خودرو به یک سمت</li>
                <li>صدای بلند هنگام فشار دادن پدال</li>
                <li>لرزش در پدال یا فرمان</li>
                <li>کاهش قدرت توقف یا افزایش مسیر ترمز</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>دلایل اصلی</h2>
            <ul>
                <li>فرسودگی لنت یا دیسک</li>
                <li>نشت روغن ترمز یا هوابردگی در مدار</li>
                <li>خرابی کالیپر یا محکم‌بودن نامناسب</li>
                <li>مشکل در سیستم ABS یا بوستر</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا تعویض لنت به‌تنهایی کافی است؟</h3>
            <p>در برخی موارد، بررسی دیسک، روغن ترمز و کالیپر هم لازم است. در صورت فرسودگی شدید، انجام همه این مراحل برای ایمنی بهتر ضروری است.</p>
        </div>
        <div class="faq-item">
            <h3>مشکل ترمز از کجا شناخته می‌شود؟</h3>
            <p>صدا، لرزش، پیچیدن، یا کاهش نیرو هنگام ترمزگیری از جمله نشانه‌های اصلی هستند که نباید نادیده گرفته شوند.</p>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
