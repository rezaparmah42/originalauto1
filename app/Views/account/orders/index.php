<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:2rem;">
        <div>
            <h1>سابقه سفارش‌ها</h1>
            <p>تمام سفارش‌های ثبت شده شما را به همراه جزئیات آیتم‌ها مشاهده کنید.</p>
        </div>
        <a href="<?= SITE_URL ?>/account/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff; margin-bottom:1rem;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                    <div>
                        <p><strong>سفارش شماره:</strong> <?= e($order['id'] ?? '-') ?></p>
                        <p><strong>تاریخ:</strong> <?= e($order['created_at'] ?? '-') ?></p>
                    </div>
                    <div>
                        <p><strong>مبلغ کل:</strong> <?= e(number_format((float) ($order['total'] ?? 0), 0, '.', ',')) ?> تومان</p>
                        <p><strong>وضعیت:</strong> <?= e($order['status'] ?? '-') ?></p>
                    </div>
                </div>
                <?php if (!empty($order['items'])): ?>
                    <div style="margin-top:1rem;">
                        <h3 style="margin:0 0 0.5rem 0;">آیتم‌ها</h3>
                        <ul style="margin:0; padding-left:1.2rem;">
                            <?php foreach ($order['items'] as $item): ?>
                                <li>
                                    <?= e($item['product_name'] ?? 'محصول نامشخص') ?>
                                    (<?= e((int) ($item['quantity'] ?? 0)) ?> x <?= e(number_format((float) ($item['unit_price'] ?? 0), 0, '.', ',')) ?>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <p style="margin-top:1rem;">آیتمی در این سفارش ثبت نشده است.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="border:1px dashed #cbd5e1; border-radius:12px; padding:1rem; background:#f8fafc;">هیچ سفارشی برای نمایش وجود ندارد.</div>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
