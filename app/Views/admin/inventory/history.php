<?php
$title = 'تاریخچه موجودی | ' . SITE_NAME;
$description = 'تاریخچه تغییرات موجودی محصولات.';
$canonical = SITE_URL . '/admin/inventory/history';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>تاریخچه موجودی</h1>
            <p>نمایش رویدادهای افزایش، کاهش و تنظیمات موجودی.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/inventory" class="btn-outline">بازگشت به موجودی</a>
    </div>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>محصول</th>
                <th>موجودی قبلی</th>
                <th>موجودی جدید</th>
                <th>تغییر</th>
                <th>نوع</th>
                <th>مدیر</th>
                <th>تاریخ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($history)): ?>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?= e($item['title_fa'] ?? $item['title_en'] ?? '-') ?></td>
                        <td><?= e($item['old_stock'] ?? 0) ?></td>
                        <td><?= e($item['new_stock'] ?? 0) ?></td>
                        <td><?= e($item['change_amount'] ?? 0) ?></td>
                        <td><?= e($item['type'] ?? '-') ?></td>
                        <td><?= e($item['admin_name'] ?? '-') ?></td>
                        <td><?= e($item['created_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">هنوز رویدادی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
