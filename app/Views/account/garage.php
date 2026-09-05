<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:2rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <span>گاراژ من</span>
            </nav>
            <h1>گاراژ من</h1>
            <p>نمایش سریع خودروها، تعمیرات و نگهداری‌های آینده شما.</p>
        </div>
        <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/vehicles/create" class="btn-primary">افزودن خودرو</a>
            <a href="<?= SITE_URL ?>/booking" class="btn-outline">درخواست تعمیر</a>
            <a href="<?= SITE_URL ?>/account/profile" class="btn-outline">پروفایل</a>
        </div>
    </div>

    <div style="display:grid; gap:1.5rem;">
        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin:0 0 0.5rem 0;">خودروهای من</h2>
            <?php if (!empty($vehicles)): ?>
                <div style="display:grid; gap:1rem;">
                    <?php foreach ($vehicles as $vehicle): ?>
                        <?php $vid = (int) ($vehicle['id'] ?? 0); $summary = $vehicleSummaries[$vid] ?? []; $parts = $partsByVehicle[$vid] ?? []; $details = $vehicleDetails[$vid] ?? []; ?>
                        <div style="border:1px solid #f1f5f9; border-radius:10px; padding:1rem; background:#fafbfc; display:flex; gap:1rem; flex-wrap:wrap;">
                            <div style="flex:1; min-width:220px;">
                                <p><strong style="font-size:1.05rem"><?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></strong></p>
                                <p style="margin:0.25rem 0;">سال: <?= e($vehicle['year'] ?? '-') ?> · وضعیت: <?= e($summary['status'] ?? $vehicle['status'] ?? 'unknown') ?></p>
                                <p style="margin:0.25rem 0;">تعداد تعمیرات: <?= e((int) ($summary['repair_count'] ?? 0)) ?> · آخرین تعمیر: <?= e($summary['latest_repair_date'] ?? '-') ?></p>
                                <p style="margin:0.25rem 0;">آخرین سرویس: <?= e($summary['latest_service_date'] ?? '-') ?> · قطعات مصرف‌شده: <?= e((int) ($summary['parts_used'] ?? 0)) ?></p>
                                <?php if (!empty($details)): ?>
                                    <p style="margin:0.25rem 0; color:#b91c1c;">سرویس‌های عقب‌افتاده: <?= e((int) ($details['overdue_services_count'] ?? 0)) ?></p>
                                <?php endif; ?>
                            </div>
                            <div style="flex:0 0 220px; min-width:180px;">
                                <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                    <a href="<?= SITE_URL ?>/account/vehicle/<?= $vid ?>" class="btn-outline">جزئیات خودرو</a>
                                    <a href="<?= SITE_URL ?>/account/vehicle/health/<?= $vid ?>" class="btn-outline">گزارش سلامت</a>
                                    <a href="<?= SITE_URL ?>/account/vehicle-profile?vehicle_id=<?= $vid ?>" class="btn-outline">پروفایل خودرو</a>
                                    <a href="<?= SITE_URL ?>/account/vehicle-profile/maintenance?vehicle_id=<?= $vid ?>" class="btn-outline">نگهداری</a>
                                    <a href="<?= SITE_URL ?>/account/repairs?vehicle_id=<?= $vid ?>" class="btn-outline">تعمیرات</a>
                                </div>
                            </div>
                            <div style="flex-basis:100%;"></div>
                            <?php if (!empty($parts)): ?>
                                <div style="width:100%; margin-top:0.5rem;">
                                    <strong>قطعات مصرف‌شده (اخیر):</strong>
                                    <ul style="margin:0.3rem 0 0 1rem; padding:0; list-style:disc;">
                                        <?php foreach ($parts as $p): ?>
                                            <li><?= e($p['title'] ?? '-') ?> × <?= e((int) ($p['quantity'] ?? 0)) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($details)): ?>
                                <div style="width:100%; margin-top:0.75rem; border-top:1px dashed #eceff4; padding-top:0.75rem;">
                                    <strong>وضعیت سلامت و تاریخچه</strong>
                                    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-top:0.5rem;">
                                        <div style="flex:1; min-width:220px;">
                                            <p style="margin:0">آخرین تعمیر: <?= e($summary['latest_repair_date'] ?? '-') ?></p>
                                            <p style="margin:0">تعداد تعمیرات: <?= e((int) ($summary['repair_count'] ?? 0)) ?></p>
                                            <p style="margin:0">آخرین سرویس: <?= e($summary['latest_service_date'] ?? '-') ?></p>
                                        </div>
                                        <div style="flex:1; min-width:220px;">
                                            <p style="margin:0">سرویس‌های آینده: <?= e(count($details['upcoming'] ?? [])) ?></p>
                                            <p style="margin:0">سرویس‌های عقب‌افتاده: <?= e((int) ($details['overdue_services_count'] ?? 0)) ?></p>
                                            <p style="margin:0">هشدارها: <?= e((count($details['diagnostics'] ?? []) > 0) ? 'خطاهای تشخیص‌داده‌شده' : 'هیچ هشدار فعالی') ?></p>
                                        </div>
                                    </div>
                                    <div style="margin-top:0.6rem;">
                                        <a href="<?= SITE_URL ?>/account/vehicle/<?= $vid ?>" class="btn-outline">مشاهده جزئیات کامل</a>
                                        <a href="<?= SITE_URL ?>/account/vehicle/health/<?= $vid ?>" class="btn-outline">گزارش سلامت</a>
                                        <a href="<?= SITE_URL ?>/booking?vehicle_id=<?= $vid ?>" class="btn-primary">درخواست تعمیر</a>
                                        <a href="<?= SITE_URL ?>/products?brand=<?= rawurlencode($vehicle['brand_name'] ?? '') ?>&model=<?= rawurlencode($vehicle['model'] ?? '') ?>&year=<?= rawurlencode($vehicle['year'] ?? '') ?>" class="btn-outline">نمایش قطعات سازگار</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>هنوز خودرویی ثبت نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin:0 0 0.5rem 0;">تعمیرات اخیر</h2>
            <?php if (!empty($repairs)): ?>
                <table style="width:100%; border-collapse:collapse; margin-top:1rem;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">خودرو</th>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">خدمت</th>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">وضعیت</th>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repairs as $repair): ?>
                            <?php $repairVehicleId = (int) ($repair['vehicle_id'] ?? 0); ?>
                            <tr>
                                <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;">
                                    <a href="<?= SITE_URL ?>/account/repairs/<?= (int) ($repair['id'] ?? 0) ?>" style="color:#2563eb; text-decoration:none;">
                                        <?= e(($repair['vehicle_brand'] ?? '-') . ' ' . ($repair['vehicle_model'] ?? '-')) ?>
                                    </a>
                                </td>
                                <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;">
                                    <a href="<?= SITE_URL ?>/account/repairs/<?= (int) ($repair['id'] ?? 0) ?>" style="color:#111827; text-decoration:none;">
                                        <?= e($repair['service_title'] ?? '-') ?>
                                    </a>
                                </td>
                                <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;">
                                    <a href="<?= SITE_URL ?>/account/repairs/<?= (int) ($repair['id'] ?? 0) ?>" style="color:#2563eb; text-decoration:none;">
                                        <?= e($repair['status'] ?? '-') ?>
                                    </a>
                                </td>
                                <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;">
                                    <?= e($repair['created_at'] ?? '-') ?>
                                    <?php if ($repairVehicleId > 0): ?>
                                        <div style="margin-top:0.35rem;">
                                            <a href="<?= SITE_URL ?>/account/vehicle/<?= $repairVehicleId ?>" style="color:#2563eb; text-decoration:none;">خودرو</a>
                                            <span> · </span>
                                            <a href="<?= SITE_URL ?>/account/vehicle/health/<?= $repairVehicleId ?>" style="color:#2563eb; text-decoration:none;">سلامت</a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>تعمیری ثبت نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin:0 0 0.5rem 0;">نگهداری‌های آینده</h2>
            <?php if (!empty($maintenanceReminders)): ?>
                <div style="display:grid; gap:1rem; margin-top:1rem;">
                    <?php foreach ($maintenanceReminders as $rem): ?>
                        <div style="border:1px solid #f1f5f9; border-radius:10px; padding:1rem; background:#fafbfc;">
                            <p><strong><?= e($rem['title'] ?? '-') ?></strong> · <?= e($rem['brand'] ?? '') ?> <?= e($rem['model'] ?? '') ?></p>
                            <p>تاریخ سرویس: <?= e($rem['service_date'] ?? '-') ?> | کیلومتر: <?= e((int) ($rem['mileage'] ?? 0)) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>هیچ نگهداری آینده‌ای ثبت نشده است.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
