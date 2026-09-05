<?php
$title = 'جزئیات خودرو | ' . SITE_NAME;
$description = 'نمایش جزئیات خودرو، سابقه تعمیر و قطعات مصرفی.';
$canonical = SITE_URL . '/account/vehicles/detail/' . (int) ($vehicle['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem;">
        <div>
            <h1>جزئیات خودرو</h1>
            <p><?= e(($vehicle['brand_name'] ?? '-') . ' ' . ($vehicle['model'] ?? '-')) ?> · سال <?= e($vehicle['year'] ?? '-') ?></p>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/vehicles" class="btn-outline">بازگشت</a>
            <a href="<?= SITE_URL ?>/account/vehicles/history/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">تاریخچه تعمیرات</a>
        </div>
    </div>

    <div style="display:grid; gap:1rem;">
        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.1rem; background:#fff;">
            <h2 style="margin-top:0;">اطلاعات خودرو</h2>
            <p><strong>برند:</strong> <?= e($vehicle['brand_name'] ?? '-') ?></p>
            <p><strong>مدل:</strong> <?= e($vehicle['model'] ?? '-') ?></p>
            <p><strong>سال:</strong> <?= e($vehicle['year'] ?? '-') ?></p>
            <p><strong>موتور:</strong> <?= e($vehicle['engine'] ?? '-') ?></p>
            <p><strong>VIN:</strong> <?= e($vehicle['vin'] ?? '-') ?></p>
            <p><strong>کیلومتر:</strong> <?= e($vehicle['mileage'] ?? 0) ?></p>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.1rem; background:#fff;">
            <h2 style="margin-top:0;">تاریخچه تعمیرات</h2>
            <?php if (!empty($history)): ?>
                <?php foreach ($history as $item): ?>
                    <div style="border-top:1px solid #eef2f7; padding-top:0.8rem; margin-top:0.8rem;">
                        <p><strong>خدمت:</strong> <?= e($item['service_title'] ?? '-') ?></p>
                        <p><strong>تاریخ:</strong> <?= e($item['booking_date'] ?? '-') ?></p>
                        <p><strong>مشکل:</strong> <?= e($item['problem'] ?? '-') ?></p>
                        <p><strong>تشخیص:</strong> <?= e($item['diagnosis'] ?? '-') ?></p>
                        <p><strong>وضعیت:</strong> <?= e($item['repair_status'] ?? $item['status'] ?? '-') ?></p>
                        <?php $parts = $repairParts[$item['repair_id']] ?? []; ?>
                        <?php if (!empty($parts)): ?>
                            <p><strong>قطعات مصرفی:</strong></p>
                            <ul style="margin:0; padding-right:1.2rem;">
                                <?php foreach ($parts as $part): ?>
                                    <li><?= e($part['title_fa'] ?? $part['title_en'] ?? $part['part_name'] ?? $part['name'] ?? '-') ?> · تعداد: <?= e((int) ($part['quantity'] ?? 0)) ?> · قیمت: <?= e(number_format((float) ($part['unit_price'] ?? $part['total_price'] ?? 0), 0, '.', ',')) ?> تومان</li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>قطعه‌ای برای این تعمیر ثبت نشده است.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>برای این خودرو سابقه تعمیراتی ثبت نشده است.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
