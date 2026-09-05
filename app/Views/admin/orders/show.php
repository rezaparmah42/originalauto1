<?php
$title = 'جزئیات سفارش | ' . SITE_NAME;
$description = 'نمایش جزئیات سفارش در پنل مدیریت.';
$canonical = SITE_URL . '/admin/orders/show/' . (int) ($order['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>جزئیات سفارش</h1>
            <p>کد سفارش: #<?= e($order['id'] ?? '-') ?></p>
        </div>
        <a href="<?= SITE_URL ?>/admin/orders" class="btn-outline">بازگشت</a>
    </div>

    <div style="display:grid; gap:1rem;">
        <div style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem;">
            <p><strong>مشتری:</strong> <?= e($order['customer_name'] ?? '-') ?></p>
            <p><strong>تلفن:</strong> <?= e($order['customer_phone'] ?? '-') ?></p>
            <p><strong>ایمیل:</strong> <?= e($order['customer_email'] ?? '-') ?></p>
            <p><strong>وضعیت:</strong> <?= e($order['status'] ?? 'pending') ?></p>
            <p><strong>پرداخت:</strong> <?= e($order['payment_status'] ?? 'pending') ?></p>
            <p><strong>آدرس:</strong> <?= e($order['address'] ?? '-') ?></p>
        </div>

        <div style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem;">
            <h2>محصولات سفارش</h2>
            <table class="table table-striped" style="width:100%; border-collapse:collapse;">
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
                        <tr><td colspan="3">محصولی برای این سفارش ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
