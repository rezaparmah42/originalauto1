<?php
$title = 'سفارش‌های مشتریان | ' . SITE_NAME;
$description = 'مدیریت سفارش‌های مشتریان در پنل مدیریت.';
$canonical = SITE_URL . '/admin/orders';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>سفارش‌های مشتریان</h1>
            <p>نمایش و مدیریت سفارش‌ها.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>

    <form method="get" action="<?= SITE_URL ?>/admin/orders" style="display:flex; gap:0.7rem; flex-wrap:wrap; margin-bottom:1rem;">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="جست‌وجو بر اساس کد یا نام" style="padding:0.7rem; min-width:260px;">
        <select name="status" style="padding:0.7rem;">
            <option value="all" <?= (($status ?? 'all') === 'all') ? 'selected' : '' ?>>همه</option>
            <option value="pending" <?= (($status ?? 'all') === 'pending') ? 'selected' : '' ?>>در انتظار</option>
            <option value="confirmed" <?= (($status ?? 'all') === 'confirmed') ? 'selected' : '' ?>>تأیید شده</option>
            <option value="processing" <?= (($status ?? 'all') === 'processing') ? 'selected' : '' ?>>در حال پردازش</option>
            <option value="shipped" <?= (($status ?? 'all') === 'shipped') ? 'selected' : '' ?>>ارسال شده</option>
            <option value="completed" <?= (($status ?? 'all') === 'completed') ? 'selected' : '' ?>>تکمیل شده</option>
            <option value="cancelled" <?= (($status ?? 'all') === 'cancelled') ? 'selected' : '' ?>>لغو شده</option>
        </select>
        <button type="submit" class="btn-primary">فیلتر</button>
    </form>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>کد سفارش</th>
                <th>مشتری</th>
                <th>مبلغ</th>
                <th>وضعیت</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= e($order['id'] ?? '-') ?></td>
                        <td><?= e($order['customer_name'] ?? '-') ?></td>
                        <td><?= e($order['total_amount'] ?? 0) ?></td>
                        <td><?= e($order['status'] ?? 'pending') ?></td>
                        <td><?= e($order['created_at'] ?? '-') ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/orders/show/<?= (int) ($order['id'] ?? 0) ?>" class="btn-outline">نمایش</a>
                            <a href="<?= SITE_URL ?>/admin/orders/edit/<?= (int) ($order['id'] ?? 0) ?>" class="btn-outline">ویرایش</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">سفارشی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
