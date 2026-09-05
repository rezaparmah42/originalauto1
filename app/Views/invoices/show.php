<?php
$title = 'فاکتور | ' . SITE_NAME;
$description = 'نمایش فاکتور سفارش.';
$canonical = SITE_URL . '/invoice/show/' . (int) ($invoice['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>فاکتور</h1>
    <p><strong>شماره فاکتور:</strong> <?= e($invoice['invoice_number'] ?? '-') ?></p>
    <p><strong>سفارش:</strong> #<?= e($order['id'] ?? '-') ?></p>
    <p><strong>مشتری:</strong> <?= e($order['customer_name'] ?? '-') ?></p>
    <p><strong>وضعیت پرداخت:</strong> <?= e($order['payment_status'] ?? 'pending') ?></p>
    <p><strong>تاریخ:</strong> <?= e($invoice['created_at'] ?? '-') ?></p>

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
                <tr><td colspan="3">محصولی برای این سفارش ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p style="margin-top:1rem;"><strong>جمع کل:</strong> <?= e($invoice['amount'] ?? ($order['total_amount'] ?? 0)) ?></p>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
