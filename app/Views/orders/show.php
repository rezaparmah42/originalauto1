<?php
$title = 'جزئیات سفارش | ' . SITE_NAME;
$description = 'نمایش جزئیات سفارش مشتری.';
$canonical = SITE_URL . '/orders/show/' . (int) ($order['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>جزئیات سفارش</h1>
    <p>کد سفارش: #<?= e($order['id'] ?? '-') ?></p>
    <p>وضعیت: <?= e($order['status'] ?? 'pending') ?></p>
    <p>وضعیت پرداخت: <?= e($order['payment_status'] ?? 'pending') ?></p>
    <p>آدرس: <?= e($order['address'] ?? '-') ?></p>

    <table class="table table-striped" style="width:100%; border-collapse:collapse; margin-top:1rem;">
        <thead>
            <tr>
                <th>محصول</th>
                <th>تعداد</th>
                <th>قیمت</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= e($item['title_fa'] ?? $item['title_en'] ?? '-') ?></td>
                        <td><?= e($item['quantity'] ?? 0) ?></td>
                        <td><?= e($item['price'] ?? 0) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">آیتمی برای این سفارش ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
