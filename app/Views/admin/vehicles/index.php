<?php
$title = 'مدیریت خودروها | ' . SITE_NAME;
$description = 'مشاهده خودروهای ثبت‌شده مشتریان.';
$canonical = SITE_URL . '/admin/vehicles';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <h1>مدیریت خودروها</h1>
            <p>لیست خودروهای ثبت‌شده مشتریان و تاریخچه آن‌ها.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>مشتری</th>
                <th>خودرو</th>
                <th>سال</th>
                <th>کیلومتر</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td><?= e($vehicle['customer_name'] ?? '-') ?></td>
                        <td><?= e(($vehicle['brand_name'] ?? '-') . ' ' . ($vehicle['model'] ?? '-')) ?></td>
                        <td><?= e($vehicle['year'] ?? '-') ?></td>
                        <td><?= e($vehicle['mileage'] ?? 0) ?></td>
                        <td><a href="<?= SITE_URL ?>/admin/vehicles/view/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">مشاهده</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">خودرویی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>