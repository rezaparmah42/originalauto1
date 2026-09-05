<?php
$title = 'ویرایش سفارش | ' . SITE_NAME;
$description = 'به‌روزرسانی وضعیت سفارش در پنل مدیریت.';
$canonical = SITE_URL . '/admin/orders/edit/' . (int) ($order['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>ویرایش سفارش</h1>
    <form method="post" action="<?= SITE_URL ?>/admin/orders/update-status/<?= (int) ($order['id'] ?? 0) ?>" style="max-width:700px; display:grid; gap:1rem;">
        <?= csrf_field() ?>
        <div>
            <label>وضعیت سفارش</label>
            <select name="status" style="width:100%; padding:0.7rem;">
                <?php foreach (['pending','confirmed','processing','shipped','completed','cancelled'] as $value): ?>
                    <option value="<?= e($value) ?>" <?= (($order['status'] ?? 'pending') === $value) ? 'selected' : '' ?>><?= e($value) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>وضعیت پرداخت</label>
            <select name="payment_status" style="width:100%; padding:0.7rem;">
                <?php foreach (['pending','paid','failed'] as $value): ?>
                    <option value="<?= e($value) ?>" <?= (($order['payment_status'] ?? 'pending') === $value) ? 'selected' : '' ?>><?= e($value) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn-primary">به‌روزرسانی</button>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
