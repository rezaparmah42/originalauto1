<?php
$title = 'موجودی کم | ' . SITE_NAME;
$description = 'محصولاتی که موجودی آنها در محدوده هشدار قرار دارد.';
$canonical = SITE_URL . '/admin/inventory/low-stock';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>هشدار موجودی کم</h1>
            <p>محصولاتی که موجودی آنها کمتر یا برابر <?= e($threshold ?? 10) ?> واحد است.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/inventory" class="btn-outline">بازگشت به موجودی</a>
    </div>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>موجودی</th>
                <th>قیمت</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= e($product['title_fa'] ?? $product['title_en'] ?? '-') ?></td>
                        <td><?= e($product['stock'] ?? 0) ?></td>
                        <td><?= e($product['price'] ?? 0) ?></td>
                        <td><?= !empty($product['status']) ? 'فعال' : 'غیر فعال' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">محصولی در وضعیت کمبود موجودی یافت نشد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
