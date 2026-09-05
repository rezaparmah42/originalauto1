<?php
$title = 'سفارش ثبت شد | ' . SITE_NAME;
$description = 'سفارش شما با موفقیت ثبت شد.';
$canonical = SITE_URL . '/orders/success/' . (int) ($order['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>سفارش شما ثبت شد</h1>
    <p>سفارش شما با موفقیت در سیستم ثبت شد. کد سفارش: #<?= e($order['id'] ?? '-') ?></p>
    <p>وضعیت فعلی: <?= e($order['status'] ?? 'pending') ?></p>
    <a href="<?= SITE_URL ?>/orders/history" class="btn-outline">مشاهده سفارش‌های من</a>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
