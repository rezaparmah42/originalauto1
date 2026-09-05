<?php
$title = 'تاریخچه سفارش‌ها | ' . SITE_NAME;
$description = 'نمایش سفارش‌های ثبت‌شده مشتری.';
$canonical = SITE_URL . '/orders/history';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>تاریخچه سفارش‌ها</h1>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>کد سفارش</th>
                <th>مبلغ</th>
                <th>وضعیت</th>
                <th>پرداخت</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= e($order['id'] ?? '-') ?></td>
                        <td><?= e($order['total_amount'] ?? 0) ?></td>
                        <td><?= e($order['status'] ?? 'pending') ?></td>
                        <td><?= e($order['payment_status'] ?? 'pending') ?></td>
                        <td><?= e($order['created_at'] ?? '-') ?></td>
                        <td><a href="<?= SITE_URL ?>/orders/show/<?= (int) ($order['id'] ?? 0) ?>" class="btn-outline">مشاهده</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">سفارشی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
