<?php
$title = 'تعویض روغن موتور | ' . SITE_NAME;
$description = 'تعویض روغن موتور با روغن استاندارد و فیلتر اصلی برای کاهش سایش و حفظ عملکرد بهینه خودرو.';
$canonical = SITE_URL . '/services/oil-change';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'تعویض روغن موتور', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">روغن موتور</div>
    <h1>تعویض روغن موتور</h1>
    <p>روغن موتور یکی از مهم‌ترین عوامل حفظ عملکرد سالم موتور و کاهش سایش قطعات درون آن است. تعویض به‌موقع روغن با برند استاندارد و فیلتر مناسب، نقش زیادی در افزایش عمر موتور و کاهش هزینه‌های تعمیر دارد.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعویض روغن</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/periodic-service">سرویس دوره‌ای</a>
    </div>
</section>

<section class="section-shell">
    <div class="faq-list">
        <div class="faq-item">
            <h3>تعویض روغن چه تأثیری روی موتور دارد؟</h3>
            <p>روغن تمیز و مناسب باعث کاهش اصطکاک، جلوگیری از گرم‌شدن بیش از حد و حفظ روانی قطعات داخلی موتور می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>چه نوع روغنی بهتر است؟</h3>
            <p>انتخاب روغن باید با توجه به برند خودرو، شرایط استفاده و استاندارد سازنده انجام شود تا عملکرد موتور بهینه بماند.</p>
        </div>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
