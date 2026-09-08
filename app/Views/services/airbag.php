<?php
$title = 'تعمیر ایربگ و سیستم‌های ایمنی خودرو | ' . SITE_NAME;
$description = 'تعمیر ایربگ و سیستم‌های ایمنی خودرو، بررسی سنسور ضربه و خطاهای ایمنی در اورجینال شرق.';
$canonical = SITE_URL . '/services/airbag';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'ایربگ', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">سیستم ایمنی</div>
    <h1>تعمیر ایربگ و سیستم‌های ایمنی خودرو</h1>
    <p>سیستم‌های ایمنی خودرو، از جمله ایربگ و سنسورهای ضربه، باید با دقت بالا بررسی شوند. خطای این بخش‌ها می‌تواند بر عملکرد کلی ایمنی خودرو اثر بگذارد و نباید نادیده گرفته شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو بررسی ایمنی</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ ایمنی</a>
    </div>
</section>

<section class="section-shell">
    <div class="faq-list">
        <div class="faq-item">
            <h3>چه نشانه‌هایی به خطای ایربگ اشاره می‌کند؟</h3>
            <p>روشن شدن چراغ ایربگ، هشدارهای ناگهانی یا خطاهای مربوط به سنسورها از موارد مهم هستند.</p>
        </div>
        <div class="faq-item">
            <h3>آیا باید این مشکل را جدی گرفت؟</h3>
            <p>بله، چون عملکرد ایمنی خودرو مستقیماً به این بخش‌ها وابسته است و بررسی دقیق در زمان مناسب اهمیت زیادی دارد.</p>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
