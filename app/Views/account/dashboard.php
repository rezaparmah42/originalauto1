<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:2rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <span>داشبورد</span>
            </nav>
            <h1>داشبورد مشتری</h1>
            <p>در این بخش می‌توانید خودروها، تعمیرات و سفارش‌های خود را مدیریت کنید.</p>
        </div>
        <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/vehicles/create" class="btn-primary">افزودن خودرو</a>
            <a href="<?= SITE_URL ?>/booking" class="btn-outline">درخواست تعمیر</a>
            <a href="<?= SITE_URL ?>/account/profile" class="btn-outline">پروفایل</a>
            <a href="<?= SITE_URL ?>/account/repairs" class="btn-outline">مشاهده تاریخچه</a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem; margin-bottom:2rem;">
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">کل خودروها</h2>
            <p style="font-size:2rem; margin:0.5rem 0;"><?= e($stats['total_vehicles'] ?? 0) ?></p>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">کل تعمیرات</h2>
            <p style="font-size:2rem; margin:0.5rem 0;"><?= e($stats['total_repairs'] ?? 0) ?></p>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">تعمیرات فعال</h2>
            <p style="font-size:2rem; margin:0.5rem 0;"><?= e($stats['active_repairs'] ?? 0) ?></p>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">تعمیرات تکمیل‌شده</h2>
            <p style="font-size:2rem; margin:0.5rem 0;"><?= e($stats['completed_repairs'] ?? 0) ?></p>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">نگهداری آینده</h2>
            <p style="font-size:2rem; margin:0.5rem 0;"><?= e($stats['upcoming_maintenance'] ?? 0) ?></p>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">امتیاز سلامت خودرو</h2>
            <p style="font-size:2rem; margin:0.5rem 0;"><?= e($healthScore ?? 100) ?>%</p>
            <p style="font-size:0.9rem; margin:0.25rem 0; color:#6b7280;">آخرین اسکن: <?= e($latestDiagnostic['code'] ?? '-') ?></p>
        </div>

        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">پرداخت‌های معلق</h2>
            <p style="font-size:2rem; margin:0.5rem 0;"><?= e($stats['pending_payments'] ?? 0) ?></p>
        </div>
        <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">آخرین اسکن</h2>
            <p style="font-size:1rem; margin:0.5rem 0;"><?= e($latestDiagnostic['code'] ?? '-') ?></p>
        </div>
    </div>

    <?php $dashboardFirstVehicleId = (int) (($vehicles[0]['id'] ?? 0)); ?>
    <div style="display:grid; gap:1.5rem;">
        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <h2 style="margin:0;">خودروهای من</h2>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <a href="<?= SITE_URL ?>/account/vehicles" class="btn-outline">مشاهده همه</a>
                    <a href="<?= SITE_URL ?><?= $dashboardFirstVehicleId > 0 ? '/account/vehicle-profile?vehicle_id=' . $dashboardFirstVehicleId : '/account/vehicles' ?>" class="btn-outline">پروفایل هوشمند</a>
                </div>
            </div>
            <?php if (!empty($vehicles)): ?>
                <div style="display:grid; gap:1rem; margin-top:1rem;">
                    <?php foreach ($vehicles as $vehicle): ?>
                        <?php $vehicleId = (int) ($vehicle['id'] ?? 0); $summary = $vehicleSummaries[$vehicleId] ?? []; ?>
                        <div style="border:1px solid #f1f5f9; border-radius:10px; padding:1rem; background:#fafbfc;">
                            <div style="display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap; align-items:flex-start;">
                                <div>
                                    <p><strong><?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></strong></p>
                                    <p>سال: <?= e($vehicle['year'] ?? '-') ?> | وضعیت: <?= e($summary['status'] ?? $vehicle['status'] ?? 'unknown') ?></p>
                                    <p>آخرین تعمیر: <?= e($summary['latest_repair_date'] ?? '-') ?> | آخرین سرویس: <?= e($summary['latest_service_date'] ?? '-') ?></p>
                                    <p>یادآوری: <?= e(($summary['next_maintenance'] ?? '-') ?: '-') ?> | قطعات سازگار: <?= e((int) ($summary['compatible_count'] ?? 0)) ?></p>
                                </div>
                                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                                    <a href="<?= SITE_URL ?>/account/vehicle-profile?vehicle_id=<?= $vehicleId ?>" class="btn-outline">پروفایل خودرو</a>
                                    <a href="<?= SITE_URL ?>/account/vehicle/<?= $vehicleId ?>" class="btn-outline">جزئیات</a>
                                    <a href="<?= SITE_URL ?>/account/vehicle/health/<?= $vehicleId ?>" class="btn-outline">سلامت</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="margin-top:1rem;">هنوز خودرویی به حساب شما اضافه نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <h2 style="margin:0;">یادآوری‌های نگهداری</h2>
                <a href="<?= SITE_URL ?><?= $dashboardFirstVehicleId > 0 ? '/account/vehicle-profile?vehicle_id=' . $dashboardFirstVehicleId : '/account/vehicles' ?>" class="btn-outline">مشاهده خودروها</a>
            </div>
            <?php if (!empty($maintenanceReminders)): ?>
                <div style="margin-top:1rem; display:grid; gap:1rem;">
                    <?php foreach ($maintenanceReminders as $reminder): ?>
                        <div style="border:1px solid #f1f5f9; border-radius:10px; padding:1rem; background:#fafbfc;">
                            <p><strong><?= e($reminder['title'] ?? '-') ?></strong> · <?= e($reminder['brand'] ?? '-') ?> <?= e($reminder['model'] ?? '-') ?></p>
                            <p>تاریخ سرویس: <?= e($reminder['service_date'] ?? '-') ?> | کیلومتر: <?= e((int) ($reminder['mileage'] ?? 0)) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="margin-top:1rem;">یادآوری نگهداری برای خودرو شما ثبت نشده است.</p>
            <?php endif; ?>
            <div style="margin-top:1rem;">
                <a href="<?= SITE_URL ?><?= $dashboardFirstVehicleId > 0 ? '/account/vehicle-profile?vehicle_id=' . $dashboardFirstVehicleId : '/account/vehicles' ?>" class="btn-outline">مشاهده خودروها</a>
            </div>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <h2 style="margin:0;">پیشنهادهای قطعات متناسب با خودرو</h2>
                <a href="<?= SITE_URL ?>/shop" class="btn-outline">مشاهده فروشگاه</a>
            </div>
            <?php if (!empty($recommendedProducts)): ?>
                <div style="margin-top:1rem; display:grid; gap:1rem;">
                    <?php foreach ($recommendedProducts as $product): ?>
                        <div style="border:1px solid #f1f5f9; border-radius:10px; padding:1rem; background:#fafbfc;">
                            <p><strong><?= e($product['title_fa'] ?? $product['title_en'] ?? '-') ?></strong></p>
                            <p>قیمت: <?= e(number_format((float) ($product['price'] ?? 0), 0, '.', ',')) ?> تومان</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="margin-top:1rem;">هیچ قطعه‌ای برای خودرو شما با سازگاری ثبت‌شده پیدا نشد.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <h2 style="margin:0;">تعمیرات فعال</h2>
                <a href="<?= SITE_URL ?>/account/repairs" class="btn-outline">مشاهده همه</a>
            </div>
            <?php if (!empty($repairs)): ?>
                <table style="width:100%; border-collapse:collapse; margin-top:1rem;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">خودرو</th>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">خدمت</th>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">وضعیت</th>
                            <th style="text-align:left; padding:0.75rem; border-bottom:1px solid #e5e7eb;">آخرین بروزرسانی</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repairs as $repair): ?>
                            <?php if (!in_array($repair['status'] ?? '', ['completed', 'cancelled'], true)): ?>
                                <tr>
                                    <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><?= e(($repair['vehicle_brand'] ?? '-') . ' ' . ($repair['vehicle_model'] ?? '-')) ?></td>
                                    <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><?= e($repair['service_title'] ?? '-') ?></td>
                                    <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><a href="<?= SITE_URL ?>/account/repairs/<?= (int) ($repair['id'] ?? 0) ?>" style="color:inherit; text-decoration:none;"><?= e($repair['status'] ?? '-') ?></a></td>
                                    <td style="padding:0.75rem; border-bottom:1px solid #f1f5f9;"><?= e($repair['created_at'] ?? '-') ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="margin-top:1rem;">تعمیر فعالی وجود ندارد.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap;">
                <h2 style="margin:0;">سابقه تعمیرات</h2>
                <a href="<?= SITE_URL ?>/account/repairs" class="btn-outline">جزئیات بیشتر</a>
            </div>
            <?php if (!empty($history)): ?>
                <div style="margin-top:1rem; display:grid; gap:1rem;">
                    <?php foreach ($history as $item): ?>
                        <div style="border:1px solid #f1f5f9; border-radius:10px; padding:1rem; background:#fafbfc;">
                            <p><strong>تاریخ:</strong> <?= e($item['booking_date'] ?? '-') ?></p>
                            <p><strong>خدمت:</strong> <?= e($item['service_title'] ?? '-') ?></p>
                            <p><strong>هزینه:</strong> <?= e(number_format((float) ($item['cost'] ?? 0), 0, '.', ',')) ?> تومان</p>
                            <p><strong>وضعیت:</strong> <?= e($item['repair_status'] ?? $item['status'] ?? '-') ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="margin-top:1rem;">سابقه تعمیراتی ثبت نشده است.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
