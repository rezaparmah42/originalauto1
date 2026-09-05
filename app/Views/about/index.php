<?php
$title = $title ?? ('درباره اورجینال شرق | ' . SITE_NAME);
$description = $description ?? 'آشنایی با رویکرد فنی اورجینال شرق در عیب‌یابی، تعمیر و پشتیبانی خودرو.';
$canonical = $canonical ?? (SITE_URL . '/about');
$robots = $robots ?? 'index, follow';
require __DIR__ . '/../layouts/header.php';
?>
<section class="page-hero">
    <div class="hero-badge">تعمیرگاه تخصصی خودرو</div>
    <h1>تعمیر خودرو با تشخیص دقیق و توضیح روشن</h1>
    <p>اورجینال شرق برای خودروهایی ساخته شده که صاحبشان می‌خواهد قبل از تعویض قطعه، علت واقعی خرابی را بداند. مسیر کار از شرح حال و اندازه‌گیری شروع می‌شود و با گزارش قابل فهم و راهکار مشخص ادامه پیدا می‌کند.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو وقت</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
    </div>
</section>
<section class="section-shell">
    <div class="detail-grid">
        <article class="detail-card"><h2>رویکرد فنی ما</h2><p>دیاگ، تست برق، بررسی مکانیکی و تجربه کارگاهی کنار هم قرار می‌گیرند تا تصمیم تعمیر بر پایه نشانه و داده باشد، نه حدس.</p></article>
        <article class="detail-card"><h2>برای چه خودروهایی؟</h2><p>از خودروهای داخلی تا مدل‌های چینی، کره‌ای، ژاپنی و اروپایی؛ تجهیزات و مسیر بررسی با نوع خودرو و ایراد آن تطبیق داده می‌شود.</p></article>
    </div>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
