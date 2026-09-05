<?php
$title = 'سبد خرید | ' . SITE_NAME;
$description = 'مشاهده و مدیریت محصولات سبد خرید.';
$canonical = SITE_URL . '/cart';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>سبد خرید</h1>
    <p>محصولات انتخاب‌شده را بررسی و سفارش خود را تکمیل کنید.</p>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
        <form method="post" action="<?= SITE_URL ?>/cart/update">
            <?= csrf_field() ?>
            <table class="table table-striped" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>محصول</th>
                        <th>تعداد</th>
                        <th>قیمت واحد</th>
                        <th>جمع</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= e($item['title'] ?? '-') ?></td>
                            <td><input type="number" name="quantities[<?= e($item['key']) ?>]" value="<?= (int) ($item['quantity'] ?? 1) ?>" min="1" max="<?= (int) ($item['stock'] ?? 99) ?>" style="width:80px; padding:0.45rem;"></td>
                            <td><?= e($item['price'] ?? 0) ?></td>
                            <td><?= e(((float) ($item['price'] ?? 0)) * (int) ($item['quantity'] ?? 1)) ?></td>
                            <td>
                                <button type="submit" class="btn-outline" formaction="<?= SITE_URL ?>/cart/remove" formmethod="post" name="item_key" value="<?= e($item['key']) ?>">حذف</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-top:1rem; flex-wrap:wrap;">
                <div><strong>جمع کل:</strong> <?= e($total ?? 0) ?></div>
                <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
                    <button type="submit" class="btn-primary">به‌روزرسانی سبد</button>
                    <a href="<?= SITE_URL ?>/orders/checkout" class="btn-outline">تکمیل خرید</a>
                </div>
            </div>
        </form>
    <?php else: ?>
        <form method="post" action="<?= SITE_URL ?>/cart/add" style="display:none;">
            <?= csrf_field() ?>
        </form>
        <p>سبد خرید شما خالی است.</p>
        <a class="btn-primary" href="<?= SITE_URL ?>/shop">مشاهده فروشگاه</a>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
