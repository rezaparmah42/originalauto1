<?php
$title = 'مدیریت رزروها | ' . SITE_NAME;
$description = 'مدیریت رزروها و درخواست‌های تعمیرگاه.';
$canonical = SITE_URL . '/admin/bookings';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <h1>مدیریت رزروها</h1>
            <p>نمایش و مدیریت رزروهای ثبت‌شده در سیستم.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="get" action="<?= SITE_URL ?>/admin/bookings" style="display:flex; gap:0.7rem; flex-wrap:wrap; margin-bottom:1rem;">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="جست‌وجو بر اساس نام/تلفن/خدمت" style="padding:0.7rem; min-width:260px;">
        <select name="status" style="padding:0.7rem;">
            <option value="" <?= (($status ?? '') === '') ? 'selected' : '' ?>>همه وضعیت‌ها</option>
            <option value="pending" <?= (($status ?? '') === 'pending') ? 'selected' : '' ?>>در انتظار</option>
            <option value="confirmed" <?= (($status ?? '') === 'confirmed') ? 'selected' : '' ?>>تأیید شده</option>
            <option value="in_progress" <?= (($status ?? '') === 'in_progress') ? 'selected' : '' ?>>در حال انجام</option>
            <option value="completed" <?= (($status ?? '') === 'completed') ? 'selected' : '' ?>>تکمیل شده</option>
            <option value="cancelled" <?= (($status ?? '') === 'cancelled') ? 'selected' : '' ?>>لغو شده</option>
        </select>
        <button type="submit" class="btn-primary">فیلتر</button>
    </form>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>مشتری</th>
                <th>خودرو</th>
                <th>خدمت</th>
                <th>تاریخ</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= e($booking['customer_name'] ?? '-') ?><br><small><?= e($booking['customer_phone'] ?? '') ?></small></td>
                        <td><?= e(($booking['vehicle_brand'] ?? '') . ' ' . ($booking['vehicle_model'] ?? '')) ?></td>
                        <td><?= e($booking['service_title'] ?? '-') ?></td>
                        <td><?= e($booking['booking_date'] ?? $booking['created_at'] ?? '-') ?></td>
                        <td><?= e($booking['status'] ?? '-') ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/bookings/view/<?= (int) ($booking['id'] ?? 0) ?>" class="btn-outline">مشاهده</a>
                            <form method="post" action="<?= SITE_URL ?>/admin/bookings/delete/<?= (int) ($booking['id'] ?? 0) ?>" style="display:inline;" onsubmit="return confirm('آیا از حذف این رزرو اطمینان دارید؟');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-primary" style="background:#b91c1c; border-color:#b91c1c;">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">رزروی یافت نشد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
        <div style="margin-top:1rem;">
            <?php if (($page ?? 1) > 1): ?>
                <a href="<?= SITE_URL ?>/admin/bookings?page=<?= (int) (($page ?? 1) - 1) ?>&search=<?= rawurlencode($search ?? '') ?>&status=<?= e($status ?? '') ?>" class="btn-outline">قبلی</a>
            <?php endif; ?>
            <span style="margin:0 0.7rem;">صفحه <?= e($page ?? 1) ?> از <?= e($pages) ?></span>
            <?php if (($page ?? 1) < $pages): ?>
                <a href="<?= SITE_URL ?>/admin/bookings?page=<?= (int) (($page ?? 1) + 1) ?>&search=<?= rawurlencode($search ?? '') ?>&status=<?= e($status ?? '') ?>" class="btn-outline">بعدی</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>