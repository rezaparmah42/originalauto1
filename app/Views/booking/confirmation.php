<?php
$title = 'تأیید رزرو | ' . SITE_NAME;
$description = 'درخواست رزرو شما با موفقیت ثبت شد.';
$canonical = SITE_URL . '/booking/confirmation';
$robots = 'noindex, nofollow';
require __DIR__ . '/../layouts/header.php';
?>
<section class="page-hero">
    <div class="hero-badge">درخواست دریافت شد</div>
    <h1>رزرو شما با موفقیت ثبت شد</h1>
    <p>درخواست شما در سامانه ثبت شده است. تیم پذیرش در اولین فرصت برای هماهنگی زمان مراجعه با شما تماس می‌گیرد.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/dashboard">مشاهده حساب کاربری</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
    </div>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
