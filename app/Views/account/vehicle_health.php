<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.2rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/garage" style="color:#2563eb; text-decoration:none;">گاراژ من</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/vehicle/<?= (int) ($vehicle['id'] ?? 0) ?>" style="color:#2563eb; text-decoration:none;">جزئیات خودرو</a>
                <span> / </span>
                <span>گزارش سلامت</span>
            </nav>
            <h1>گزارش سلامت خودرو</h1>
            <p>وضعیت کلی خودرو، تاریخچه تعمیرات، نگهداری‌های لازم و پیشنهادهای سرویس.</p>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/vehicle/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">بازگشت به جزئیات</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">پروفایل هوشمند</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/maintenance?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">نگهداری</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/history?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">تاریخچه</a>
            <a href="<?= SITE_URL ?>/account/garage" class="btn-primary">گاراژ من</a>
        </div>
    </div>

    <?php $healthData = $health ?? []; ?>
    <div style="display:grid; gap:1.2rem;">
        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>وضعیت کلی خودرو</h2>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:0.75rem; margin-top:0.75rem;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem;">
                    <div style="color:#6b7280; font-size:0.8rem;">نام خودرو</div>
                    <div style="font-weight:700; margin-top:0.25rem;"><?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem;">
                    <div style="color:#6b7280; font-size:0.8rem;">وضعیت سلامت</div>
                    <div style="font-weight:700; margin-top:0.25rem;"><?= e($profile['maintenance_status'] ?? 'unknown') ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem;">
                    <div style="color:#6b7280; font-size:0.8rem;">امتیاز سلامت</div>
                    <div style="font-weight:700; margin-top:0.25rem;"><?= e($profile['health_score'] ?? 100) ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem;">
                    <div style="color:#6b7280; font-size:0.8rem;">آخرین سرویس</div>
                    <div style="font-weight:700; margin-top:0.25rem;"><?= e($healthData['last_service_date'] ?? '-') ?></div>
                </div>
            </div>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>تاریخچه تعمیرات</h2>
            <?php if (!empty($repairs)): ?>
                <ul style="margin:0.5rem 0 0 1rem; padding:0; list-style:disc;">
                    <?php foreach ($repairs as $repair): ?>
                        <li style="margin-bottom:0.6rem;">
                            <strong><?= e($repair['diagnosis'] ?? $repair['repair_notes'] ?? 'تعمیر') ?></strong>
                            <div style="font-size:0.95rem; color:#374151;">وضعیت: <?= e($repair['status'] ?? '-') ?> · تاریخ: <?= e($repair['created_at'] ?? '-') ?> · هزینه: <?= e($repair['cost'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>تعمیر ثبت‌شده‌ای وجود ندارد.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>نگهداری‌های لازم</h2>
            <?php $recommendedServices = $healthData['recommended_services'] ?? []; ?>
            <?php if (!empty($recommendedServices)): ?>
                <ul style="margin:0.5rem 0 0 1rem; padding:0; list-style:disc;">
                    <?php foreach ($recommendedServices as $item): ?>
                        <li style="margin-bottom:0.5rem;">
                            <strong><?= e($item['title'] ?? 'سرویس پیشنهادی') ?></strong>
                            <div style="font-size:0.95rem; color:#374151;">تاریخ: <?= e($item['service_date'] ?? '-') ?> · وضعیت: <?= e($item['status'] ?? '-') ?></div>
                            <div style="font-size:0.9rem; color:#4b5563;"><?= e($item['reason'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>در حال حاضر نگهداری ضروری‌ای ثبت نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>پیشنهادهای سرویس</h2>
            <?php if (!empty($suggestions)): ?>
                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:0.6rem;">
                    <?php foreach ($suggestions as $product): ?>
                        <div style="border:1px solid #f1f5f9; border-radius:8px; padding:0.6rem; background:#fff;">
                            <div><strong><?= e($product['title_fa'] ?? $product['title_en'] ?? '-') ?></strong></div>
                            <div style="font-size:0.95rem; color:#374151;">قیمت: <?= e($product['price'] ?? '-') ?></div>
                            <div style="margin-top:0.4rem;"><a href="<?= SITE_URL ?>/products/<?= rawurlencode($product['slug'] ?? '') ?>" class="btn-outline">مشاهده</a></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>پیشنهاد سرویس سازگار یافت نشد.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
