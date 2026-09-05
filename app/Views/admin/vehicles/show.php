<?php
$title = 'جزئیات خودرو | ' . SITE_NAME;
$description = 'مشاهده جزئیات خودرو و سابقه تعمیرات مشتری.';
$canonical = SITE_URL . '/admin/vehicles/view/' . (int) ($vehicle['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 900px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <h1>جزئیات خودرو</h1>
            <p>مشاهده مالک، مشخصات خودرو و سابقه تعمیرات.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/vehicles" class="btn-outline">بازگشت</a>
    </div>

    <div style="display:grid; gap:1rem;">
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>مشخصات خودرو</h2>
            <p><strong>مشتری:</strong> <?= e($vehicle['customer_name'] ?? '-') ?></p>
            <p><strong>برند:</strong> <?= e($vehicle['brand_name'] ?? '-') ?></p>
            <p><strong>مدل:</strong> <?= e($vehicle['model'] ?? '-') ?></p>
            <p><strong>سال:</strong> <?= e($vehicle['year'] ?? '-') ?></p>
            <p><strong>موتور:</strong> <?= e($vehicle['engine'] ?? '-') ?></p>
            <p><strong>VIN:</strong> <?= e($vehicle['vin'] ?? '-') ?></p>
            <p><strong>کیلومتر:</strong> <?= e($vehicle['mileage'] ?? 0) ?></p>
        </div>

        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>سابقه تعمیرات</h2>
            <?php if (!empty($history)): ?>
                <?php foreach ($history as $item): ?>
                    <div style="border-top:1px solid #eef2f7; padding-top:0.7rem; margin-top:0.7rem;">
                        <p><strong>خدمت:</strong> <?= e($item['service_title'] ?? '-') ?></p>
                        <p><strong>تاریخ:</strong> <?= e($item['booking_date'] ?? '-') ?></p>
                        <p><strong>شرح:</strong> <?= e($item['problem'] ?? $item['repair_notes'] ?? $item['diagnosis'] ?? '-') ?></p>
                        <p><strong>هزینه:</strong> <?= e($item['cost'] ?? 0) ?></p>
                        <p><strong>وضعیت:</strong> <?= e($item['repair_status'] ?? $item['status'] ?? '-') ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>سابقه تعمیراتی برای این خودرو ثبت نشده است.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>