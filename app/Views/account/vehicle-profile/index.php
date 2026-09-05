<?php
$title = 'پروفایل خودرو | ' . SITE_NAME;
$description = 'نمایش اطلاعات و وضعیت خودرو.';
$canonical = SITE_URL . '/account/vehicle-profile';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/vehicles" style="color:#2563eb; text-decoration:none;">خودروهای من</a>
                <span> / </span>
                <span>پروفایل خودرو</span>
            </nav>
            <h1>پروفایل خودرو</h1>
            <p><?= e(($vehicle['brand_name'] ?? '-') . ' ' . ($vehicle['model'] ?? '-')) ?> · سال <?= e($vehicle['year'] ?? '-') ?></p>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/vehicles" class="btn-outline">خودروهای من</a>
            <a href="<?= SITE_URL ?>/account/vehicle/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">جزئیات خودرو</a>
            <a href="<?= SITE_URL ?>/account/vehicle/health/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">گزارش سلامت</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/maintenance?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">نگهداری</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/history?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">تاریخچه</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/diagnostics?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">تشخیصی</a>
        </div>
    </div>

    <div style="display:grid; gap:1rem; margin-bottom:1.5rem;">
        <div style="background:#fff; border:1px solid #e6ebf1; padding:1rem; border-radius:12px;">
            <h2>اطلاعات خودرو</h2>
            <p><strong>برند:</strong> <?= e($vehicle['brand_name'] ?? '-') ?: 'در دسترس نیست' ?></p>
            <p><strong>مدل:</strong> <?= e($vehicle['model'] ?? '-') ?: 'در دسترس نیست' ?></p>
            <p><strong>سال:</strong> <?= e($vehicle['year'] ?? '-') ?: 'در دسترس نیست' ?></p>
            <p><strong>وضعیت:</strong> <?= e($vehicle['status'] ?? $stats['status'] ?? 'unknown') ?: 'نامشخص' ?></p>
            <p><strong>پلاک:</strong> <?= e($vehicle['plate_number'] ?? $stats['plate_number'] ?? '-') ?: 'در دسترس نیست' ?></p>
            <p><strong>VIN:</strong> <?= e($vehicle['vin'] ?? '-') ?: 'در دسترس نیست' ?></p>
            <p><strong>کیلومتر:</strong> <?= e($vehicle['mileage'] ?? $stats['last_known_mileage'] ?? 0) ?: 'در دسترس نیست' ?></p>
            <p><strong>آخرین سرویس:</strong> <?= e($stats['latest_service_date'] ?? $vehicle['last_service'] ?? '-') ?: 'ثبت نشده' ?></p>
            <p><strong>نگهداری بعدی:</strong> <?= e($stats['next_maintenance'] ?? $vehicle['next_maintenance'] ?? '-') ?: 'ثبت نشده' ?></p>
        </div>

        <div style="background:#fff; border:1px solid #e6ebf1; padding:1rem; border-radius:12px;">
            <h2>آمار خودرو</h2>
            <p><strong>تعداد تعمیرات:</strong> <?= e((int) ($stats['repair_count'] ?? $vehicle['repair_count'] ?? 0)) ?></p>
            <p><strong>تعمیرات تکمیل‌شده:</strong> <?= e((int) ($stats['completed_repairs'] ?? $vehicle['completed_repairs'] ?? 0)) ?></p>
            <p><strong>تعمیرات در انتظار:</strong> <?= e((int) ($stats['pending_repairs'] ?? $vehicle['pending_repairs'] ?? 0)) ?></p>
            <p><strong>مجموع هزینه تعمیر:</strong> <?= e(number_format((float) ($stats['total_repair_cost'] ?? $vehicle['total_repair_cost'] ?? 0), 0, '.', ',')) ?> تومان</p>
            <p><strong>تعداد سرویس‌ها:</strong> <?= e((int) ($stats['maintenance_count'] ?? $vehicle['maintenance_count'] ?? 0)) ?></p>
            <p><strong>آخرین وضعیت تعمیر:</strong> <?= e($stats['repair_status'] ?? $vehicle['repair_status'] ?? '-') ?: 'ثبت نشده' ?></p>
        </div>
    </div>

    <div style="margin-top:1.5rem; display:grid; gap:1rem;">
        <div style="background:#fff; border:1px solid #e6ebf1; padding:1rem; border-radius:12px;">
            <h2>رویدادهای نزدیک</h2>
            <?php if (!empty($upcoming)): ?>
                <ul>
                    <?php foreach ($upcoming as $item): ?>
                        <li><?= e($item['title'] ?? '-') ?> - <?= e($item['service_date'] ?? '-') ?> (<?= e($item['due_state'] ?? 'upcoming') ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>سرویس دوره‌ای ثبت‌شده‌ای وجود ندارد.</p>
            <?php endif; ?>
        </div>

        <div style="background:#fff; border:1px solid #e6ebf1; padding:1rem; border-radius:12px;">
            <h2>پیشنهادهای قطعات سازگار</h2>
            <?php if (!empty($recommendedProducts)): ?>
                <ul>
                    <?php foreach ($recommendedProducts as $product): ?>
                        <li><?= e($product['title_fa'] ?? $product['title_en'] ?? '-') ?> — <?= e(number_format((float) ($product['price'] ?? 0), 0, '.', ',')) ?> تومان</li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>محصول سازگاری برای این خودرو پیدا نشد.</p>
            <?php endif; ?>
        </div>

        <div style="background:#fff; border:1px solid #e6ebf1; padding:1rem; border-radius:12px;">
            <h2>گزارش‌های تشخیصی</h2>
            <?php if (!empty($diagnostics)): ?>
                <ul>
                    <?php foreach ($diagnostics as $report): ?>
                        <li><?= e($report['analysis'] ?? '-') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>گزارش تشخیصی موجود نیست.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
