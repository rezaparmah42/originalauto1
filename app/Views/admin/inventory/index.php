<?php
$title = 'مدیریت موجودی | ' . SITE_NAME;
$description = 'مدیریت موجودی محصولات و انجام عملیات انبار در پنل مدیریت.';
$canonical = SITE_URL . '/admin/inventory';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>مدیریت موجودی</h1>
            <p>نمایش محصولات، کنترل موجودی و ثبت رویدادهای انبار.</p>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/admin/inventory/history" class="btn-outline">تاریخچه موجودی</a>
            <a href="<?= SITE_URL ?>/admin/inventory/low-stock" class="btn-outline">کمبود موجودی</a>
            <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
        </div>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>موجودی فعلی</th>
                <th>قیمت</th>
                <th>وضعیت</th>
                <th>عملیات</th>
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
                        <td>
                            <div style="display:flex; flex-direction:column; gap:0.5rem; min-width:280px;">
                                <form method="post" action="<?= SITE_URL ?>/admin/inventory/increase/<?= (int) ($product['id'] ?? 0) ?>" style="display:flex; gap:0.4rem; align-items:center;">
                                    <?= csrf_field() ?>
                                    <input type="number" name="amount" min="1" value="1" style="width:80px; padding:0.45rem;">
                                    <input type="text" name="note" placeholder="توضیح" style="padding:0.45rem; min-width:140px;">
                                    <button type="submit" class="btn-outline">افزایش</button>
                                </form>
                                <form method="post" action="<?= SITE_URL ?>/admin/inventory/decrease/<?= (int) ($product['id'] ?? 0) ?>" style="display:flex; gap:0.4rem; align-items:center;">
                                    <?= csrf_field() ?>
                                    <input type="number" name="amount" min="1" value="1" style="width:80px; padding:0.45rem;">
                                    <input type="text" name="note" placeholder="توضیح" style="padding:0.45rem; min-width:140px;">
                                    <button type="submit" class="btn-outline">کاهش</button>
                                </form>
                                <form method="post" action="<?= SITE_URL ?>/admin/inventory/adjust/<?= (int) ($product['id'] ?? 0) ?>" style="display:flex; gap:0.4rem; align-items:center;">
                                    <?= csrf_field() ?>
                                    <input type="number" name="quantity" value="0" style="width:80px; padding:0.45rem;">
                                    <input type="text" name="note" placeholder="توضیح" style="padding:0.45rem; min-width:140px;">
                                    <button type="submit" class="btn-outline">تعدیل</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">محصولی برای نمایش وجود ندارد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
