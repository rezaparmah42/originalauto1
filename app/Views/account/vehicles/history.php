<?php
$title = 'سابقه تعمیرات | ' . SITE_NAME;
$description = 'مشاهده سابقه تعمیرات خودرو در پروفایل مشتری.';
$canonical = SITE_URL . '/account/vehicles/history/' . (int) ($vehicle['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <h1>سابقه تعمیرات</h1>
            <p>خودرو: <?= e(($vehicle['brand_name'] ?? '-') . ' ' . ($vehicle['model'] ?? '-')) ?></p>
        </div>
        <a href="<?= SITE_URL ?>/account/vehicles" class="btn-outline">بازگشت</a>
    </div>

    <div style="display:grid; gap:1rem;">
        <?php if (!empty($history)): ?>
            <?php foreach ($history as $item): ?>
                <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
                    <p><strong>خدمت:</strong> <?= e($item['service_title'] ?? '-') ?></p>
                    <p><strong>تاریخ:</strong> <?= e($item['booking_date'] ?? '-') ?></p>
                    <p><strong>شرح:</strong> <?= e($item['problem'] ?? $item['repair_notes'] ?? $item['diagnosis'] ?? '-') ?></p>
                    <p><strong>هزینه:</strong> <?= e($item['cost'] ?? 0) ?></p>
                    <p><strong>وضعیت:</strong> <?= e($item['repair_status'] ?? $item['status'] ?? '-') ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="border:1px dashed #cbd5e1; border-radius:12px; padding:1rem; background:#f8fafc;">تعمیر سابقه‌ای برای این خودرو ثبت نشده است.</div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>