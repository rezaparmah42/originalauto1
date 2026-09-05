<?php
$title = 'فاکتورها | ' . SITE_NAME;
$description = 'لیست فاکتورها در پنل مدیریت.';
$canonical = SITE_URL . '/admin/invoices';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>فاکتورها</h1>
    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>شماره فاکتور</th>
                <th>سفارش</th>
                <th>مشتری</th>
                <th>مبلغ</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($invoices)): ?>
                <?php foreach ($invoices as $invoice): ?>
                    <tr>
                        <td><?= e($invoice['invoice_number'] ?? '-') ?></td>
                        <td>#<?= e($invoice['order_id'] ?? '-') ?></td>
                        <td><?= e($invoice['customer_name'] ?? '-') ?></td>
                        <td><?= e($invoice['amount'] ?? 0) ?></td>
                        <td><?= e($invoice['created_at'] ?? '-') ?></td>
                        <td><a href="<?= SITE_URL ?>/admin/invoices/show/<?= (int) ($invoice['id'] ?? 0) ?>" class="btn-outline">نمایش</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">فاکتوری ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
