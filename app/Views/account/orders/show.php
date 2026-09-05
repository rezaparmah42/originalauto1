<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:2rem;">
        <div>
            <h1>جزئیات سفارش</h1>
            <p>شماره سفارش: #<?= e($order['id'] ?? '-') ?></p>
        </div>
        <a href="<?= SITE_URL ?>/account/orders" class="btn-outline">بازگشت</a>
    </div>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; margin-bottom:1.5rem;">
        <p><strong>وضعیت:</strong> <?= e($order['status'] ?? 'pending') ?></p>
        <p><strong>وضعیت پرداخت:</strong> <?= e($order['payment_status'] ?? 'pending') ?></p>
        <p><strong>مبلغ کل:</strong> <?= e(number_format((float) ($order['total_amount'] ?? 0), 0, '.', ',')) ?> تومان</p>
        <p><strong>آدرس:</strong> <?= e($order['address'] ?? '-') ?></p>
    </div>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; margin-bottom:1.5rem;">
        <h3>آیتم‌های سفارش</h3>
        <?php if (!empty($items)): ?>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #eef2f7;">محصول</th>
                        <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #eef2f7;">تعداد</th>
                        <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #eef2f7;">قیمت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td style="padding:0.75rem; border-bottom:1px solid #f3f4f6;"><?= e($item['title_fa'] ?? $item['title_en'] ?? '-') ?></td>
                            <td style="padding:0.75rem; border-bottom:1px solid #f3f4f6;"><?= e((int) ($item['quantity'] ?? 0)) ?></td>
                            <td style="padding:0.75rem; border-bottom:1px solid #f3f4f6;"><?= e(number_format((float) ($item['price'] ?? 0), 0, '.', ',')) ?> تومان</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>آیتمی برای این سفارش ثبت نشده است.</p>
        <?php endif; ?>
    </div>

    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem;">
        <h3>تاریخچه وضعیت</h3>
        <?php if (!empty($statusHistory)): ?>
            <ul>
                <?php foreach ($statusHistory as $entry): ?>
                    <li><?= e($entry['old_status'] ?? '-') ?> → <?= e($entry['new_status'] ?? '-') ?> (<?= e($entry['created_at'] ?? '-') ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>تغییری ثبت نشده است.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
