<?php
$title = 'تشخیصی‌های مدیریتی | ' . SITE_NAME;
$description = 'نمایش تمام جلسات تشخیصی و آمار.';
$canonical = SITE_URL . '/admin/diagnostics';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>پنل تشخیصی</h1>
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-bottom:1rem;">
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>کل اسکن‌ها</h2>
            <p><?= e($stats['total_scans'] ?? 0) ?></p>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>خطاهای فعال</h2>
            <p><?= e($stats['active_faults'] ?? 0) ?></p>
        </div>
    </div>

    <table style="width:100%; border-collapse:collapse; background:#fff;">
        <thead>
            <tr>
                <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #e5e7eb;">جلسه</th>
                <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #e5e7eb;">خودرو</th>
                <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #e5e7eb;">مشتری</th>
                <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #e5e7eb;">تاریخ</th>
                <th style="text-align:right; padding:0.75rem; border-bottom:1px solid #e5e7eb;">عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sessions)): ?>
                <?php foreach ($sessions as $session): ?>
                    <tr>
                        <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;">#<?= e($session['id'] ?? 0) ?></td>
                        <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><?= e($session['vehicle_model'] ?? '-') ?></td>
                        <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><?= e($session['customer_name'] ?? '-') ?></td>
                        <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><?= e($session['created_at'] ?? '-') ?></td>
                        <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><a href="<?= SITE_URL ?>/admin/diagnostics/show/<?= (int) ($session['id'] ?? 0) ?>">نمایش</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="padding:0.75rem;">جلسه تشخیصی ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>