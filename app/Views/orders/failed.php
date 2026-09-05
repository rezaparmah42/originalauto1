<?php
$title = 'پرداخت ناموفق | ' . SITE_NAME;
$description = 'پرداخت سفارش ناموفق بود.';
$canonical = SITE_URL . '/checkout/failed';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>پرداخت ناموفق بود</h1>
    <p>در انجام پرداخت خطایی رخ داد. لطفاً دوباره تلاش کنید.</p>
    <?php if (!empty($order)): ?>
        <p>کد سفارش: #<?= e($order['id'] ?? '-') ?></p>
    <?php endif; ?>
    <a href="<?= SITE_URL ?>/orders/history" class="btn-outline">بازگشت به سفارش‌ها</a>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
