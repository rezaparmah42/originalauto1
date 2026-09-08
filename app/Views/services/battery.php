<?php
$title = 'تست، شارژ و تعویض باتری خودرو | ' . SITE_NAME;
$description = 'تست و تعویض باتری خودرو، بررسی دینام و اتصالات برای جلوگیری از خاموشی و افت عملکرد سیستم برق.';
$canonical = SITE_URL . '/services/battery';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'باتری', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">باتری و شارژ</div>
    <h1>تست، شارژ و تعویض باتری خودرو</h1>
    <p>باتری ضعیف یا دینام خراب می‌تواند باعث خاموشی خودرو، مشکل در روشن کردن و کاهش عملکرد سیستم‌های الکترونیکی شود. بررسی دقیق ولتاژ، سلامت باتری و مدار شارژ، از مهم‌ترین مراحل نگهداری سیستم برق است.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو باتری</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/electrical">تعمیر برق خودرو</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>نشانه‌های خرابی</h2>
            <ul>
                <li>روشن نشدن خودرو یا کندی در استارت</li>
                <li>خاموشی ناگهانی در حین رانندگی</li>
                <li>مشکل در عملکرد چراغ‌ها و سیستم‌های برقی</li>
                <li>افت ولتاژ یا شارژ نامنظم</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>بررسی‌ها</h2>
            <ul>
                <li>تست سلامت باتری و کابل‌ها</li>
                <li>بررسی دینام و وضعیت شارژ</li>
                <li>کنترل اتصالات و ترمینال‌ها</li>
                <li>توصیه برای تعویض مناسب در زمان درست</li>
            </ul>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
