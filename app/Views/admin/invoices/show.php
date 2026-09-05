<?php
$title = 'جزئیات فاکتور | ' . SITE_NAME;
$description = 'جزئیات فاکتور در پنل مدیریت.';
$canonical = SITE_URL . '/admin/invoices/show/' . (int) ($invoice['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>جزئیات فاکتور</h1>
    <p><strong>شماره فاکتور:</strong> <?= e($invoice['invoice_number'] ?? '-') ?></p>
    <p><strong>سفارش:</strong> #<?= e($order['id'] ?? '-') ?></p>
    <p><strong>مشتری:</strong> <?= e($order['customer_name'] ?? '-') ?></p>
    <p><strong>تاریخ:</strong> <?= e($invoice['created_at'] ?? '-') ?></p>
    <p><strong>جمع کل:</strong> <?= e($invoice['amount'] ?? ($order['total_amount'] ?? 0)) ?></p>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
