<?php
$title = 'وضعیت پرداخت | ' . SITE_NAME;
$description = 'نمایش نتیجه پرداخت سفارش.';
$canonical = SITE_URL . '/payment/result';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>وضعیت پرداخت</h1>
    <p>وضعیت پرداخت سفارش #<?= e($order['id'] ?? '-') ?>: <?= e($status ?? 'pending') ?></p>
    <a href="<?= SITE_URL ?>/orders/history" class="btn-outline">بازگشت به سفارش‌ها</a>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
