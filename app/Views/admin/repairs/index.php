<?php
$title = 'تعمیرات و پذیرش Workshop | ' . SITE_NAME;
$description = 'مدیریت تعمیرات، وضعیت‌ها و پذیرش خودروها در تعمیرگاه.';
$canonical = SITE_URL . '/admin/workshop/repairs';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <h1>لیست تعمیرات</h1>
            <p>پذیرش، وضعیت و ردیابی تعمیرات خودرو در تعمیرگاه.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="get" action="<?= SITE_URL ?>/admin/workshop/repairs" style="display:flex; gap:0.7rem; flex-wrap:wrap; margin-bottom:1rem;">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="جست‌وجو: مشتری، خودرو، مشکل" style="padding:0.7rem; min-width:260px;">
        <select name="status" style="padding:0.7rem; min-width:180px;">
            <option value="" <?= (($status ?? '') === '') ? 'selected' : '' ?>>همه وضعیت‌ها</option>
            <?php foreach (repairStatuses() as $statusKey => $statusLabel): ?>
                <option value="<?= e($statusKey) ?>" <?= (($status ?? '') === $statusKey) ? 'selected' : '' ?>><?= e($statusLabel) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-primary">فیلتر</button>
    </form>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>مشتری</th>
                <th>خودرو</th>
                <th>مشکل</th>
                <th>تاریخ</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($repairs)): ?>
                <?php foreach ($repairs as $repair): ?>
                    <tr>
                        <td><?= e($repair['customer_name'] ?? '-') ?><br><small><?= e($repair['customer_phone'] ?? '') ?></small></td>
                        <td><?= e(($repair['vehicle_brand'] ?? '') . ' ' . ($repair['vehicle_model'] ?? '')) ?><br><small><?= e($repair['vehicle_year'] ?? '') ?></small></td>
                        <td><?= e($repair['problem'] ?? $repair['diagnosis'] ?? '-') ?></td>
                        <td><?= e($repair['created_at'] ?? $repair['booking_date'] ?? '-') ?></td>
                        <td><?= e(repairStatusLabel($repair['status'] ?? '')) ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/workshop/repairs/<?= (int) ($repair['id'] ?? 0) ?>" class="btn-outline">جزئیات</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">تعمیر جدیدی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (($pages ?? 1) > 1): ?>
        <div style="margin-top:1rem;">
            <?php if (($page ?? 1) > 1): ?>
                <a href="<?= SITE_URL ?>/admin/workshop/repairs?page=<?= (int) (($page ?? 1) - 1) ?>&search=<?= rawurlencode($search ?? '') ?>&status=<?= e($status ?? '') ?>" class="btn-outline">قبلی</a>
            <?php endif; ?>
            <span style="margin:0 0.7rem;">صفحه <?= e($page ?? 1) ?> از <?= e($pages ?? 1) ?></span>
            <?php if (($page ?? 1) < ($pages ?? 1)): ?>
                <a href="<?= SITE_URL ?>/admin/workshop/repairs?page=<?= (int) (($page ?? 1) + 1) ?>&search=<?= rawurlencode($search ?? '') ?>&status=<?= e($status ?? '') ?>" class="btn-outline">بعدی</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
