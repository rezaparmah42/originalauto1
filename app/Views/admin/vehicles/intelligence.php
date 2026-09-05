<?php
$title = 'هوش خودرو | ' . SITE_NAME;
$description = 'نمایش نمای کلی خودروها و وضعیت نگهداری.';
$canonical = SITE_URL . '/admin/vehicles/intelligence';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>هوش خودرو</h1>
    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>مشتری</th>
                <th>مدل</th>
                <th>کیلومتر</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td><?= e($vehicle['customer_name'] ?? '-') ?></td>
                        <td><?= e($vehicle['model'] ?? '-') ?></td>
                        <td><?= e($vehicle['mileage'] ?? 0) ?></td>
                        <td><?= e($vehicle['maintenance_status'] ?? 'unknown') ?></td>
                        <td><a href="<?= SITE_URL ?>/admin/vehicles/intelligence/profile/<?= (int) ($vehicle['id'] ?? 0) ?>">نمایش</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">خودرویی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
