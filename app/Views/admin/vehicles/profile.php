<?php
$title = 'پروفایل هوش خودرو | ' . SITE_NAME;
$description = 'نمایش جزئیات و داده‌های نگهداری خودرو برای مدیر.';
$canonical = SITE_URL . '/admin/vehicles/intelligence/profile/' . (int) ($vehicle['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>پروفایل هوش خودرو</h1>
    <p><strong>مدل:</strong> <?= e($vehicle['model'] ?? '-') ?></p>
    <p><strong>کیلومتر:</strong> <?= e($vehicle['mileage'] ?? 0) ?></p>
    <p><strong>وضعیت:</strong> <?= e($vehicle['maintenance_status'] ?? 'unknown') ?></p>

    <div style="margin-top:1rem;">
        <h2>سابقه نگهداری</h2>
        <?php if (!empty($records)): ?>
            <ul>
                <?php foreach ($records as $record): ?>
                    <li><?= e($record['title'] ?? '-') ?> - <?= e($record['status'] ?? '-') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>سابقه نگهداری ثبت نشده است.</p>
        <?php endif; ?>
    </div>

    <div style="margin-top:1rem;">
        <h2>گزارش‌های تشخیصی</h2>
        <?php if (!empty($diagnostics)): ?>
            <ul>
                <?php foreach ($diagnostics as $report): ?>
                    <li><?= e($report['analysis'] ?? '-') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>گزارش تشخیصی ثبت نشده است.</p>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
