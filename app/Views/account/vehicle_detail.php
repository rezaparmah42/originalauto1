<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.2rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/garage" style="color:#2563eb; text-decoration:none;">گاراژ من</a>
                <span> / </span>
                <span>جزئیات خودرو</span>
            </nav>
            <h1>مشخصات خودرو</h1>
            <p>نمایش جزئیات، تاریخچه تعمیرات، سرویس‌ها و قطعات مصرف‌شده.</p>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/garage" class="btn-outline">بازگشت به گاراژ</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">پروفایل هوشمند</a>
            <a href="<?= SITE_URL ?>/account/vehicle/health/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">گزارش سلامت</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/maintenance?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">نگهداری</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/history?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">تاریخچه</a>
            <a href="<?= SITE_URL ?>/booking?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-primary">درخواست تعمیر/سرویس</a>
        </div>
    </div>

    <div style="display:grid; gap:1.2rem;">
        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>اطلاعات پایه</h2>
            <p><strong><?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></strong></p>
            <p>سال: <?= e($vehicle['year'] ?? '-') ?> · کیلومتر: <?= e($vehicle['mileage'] ?? '-') ?></p>
            <p>شماره شاسی: <?= e($vehicle['vin'] ?? '-') ?></p>
            <?php if (!empty($profile)): ?>
                <p>وضعیت سلامت: <?= e($profile['maintenance_status'] ?? '-') ?> · امتیاز سلامت: <?= e($profile['health_score'] ?? '-') ?></p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>سلامت خودرو</h2>
            <?php $healthData = $health ?? []; $lastRepair = $healthData['last_repair'] ?? null; $lastService = $healthData['last_service_date'] ?? null; $daysSince = $healthData['days_since_last_service'] ?? null; ?>
            <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:0.75rem; margin-top:0.75rem;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem;">
                    <div style="color:#6b7280; font-size:0.8rem;">آخرین تعمیر</div>
                    <div style="font-weight:700; margin-top:0.25rem;"><?= e($lastRepair['diagnosis'] ?? $lastRepair['repair_notes'] ?? '-') ?></div>
                    <div style="font-size:0.85rem; color:#374151; margin-top:0.2rem;"><?= e($lastRepair['created_at'] ?? '-') ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem;">
                    <div style="color:#6b7280; font-size:0.8rem;">فاصله زمانی از آخرین سرویس</div>
                    <div style="font-weight:700; margin-top:0.25rem;">
                        <?= $daysSince === null ? 'نامشخص' : abs((int) $daysSince) . ' روز' ?>
                    </div>
                    <div style="font-size:0.85rem; color:#374151; margin-top:0.2rem;"><?= e($lastService ?: '-') ?></div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:0.75rem;">
                    <div style="color:#6b7280; font-size:0.8rem;">وضعیت کلی</div>
                    <div style="font-weight:700; margin-top:0.25rem;"><?= e($profile['maintenance_status'] ?? 'unknown') ?></div>
                    <div style="font-size:0.85rem; color:#374151; margin-top:0.2rem;">امتیاز سلامت: <?= e($profile['health_score'] ?? 100) ?></div>
                </div>
            </div>
            <div style="margin-top:1rem;">
                <a href="<?= SITE_URL ?>/account/vehicle/health/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">گزارش کامل سلامت خودرو</a>
            </div>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>سرویس‌های پیشنهادی</h2>
            <?php $recommendedServices = $healthData['recommended_services'] ?? []; ?>
            <?php if (!empty($recommendedServices)): ?>
                <ul style="margin:0.5rem 0 0 1rem; padding:0; list-style:disc;">
                    <?php foreach ($recommendedServices as $service): ?>
                        <li style="margin-bottom:0.4rem;">
                            <strong><?= e($service['title'] ?? 'سرویس پیشنهادی') ?></strong>
                            <div style="font-size:0.95rem; color:#374151;">تاریخ: <?= e($service['service_date'] ?? '-') ?> · وضعیت: <?= e($service['status'] ?? '-') ?></div>
                            <div style="font-size:0.9rem; color:#4b5563;"><?= e($service['reason'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>هیچ سرویس پیشنهادی‌ای ثبت نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>قطعاتی که احتمال نیاز به تعویض دارند</h2>
            <?php $likelyParts = $healthData['likely_parts'] ?? []; ?>
            <?php if (!empty($likelyParts)): ?>
                <ul style="margin:0.5rem 0 0 1rem; padding:0; list-style:disc;">
                    <?php foreach ($likelyParts as $part): ?>
                        <li style="margin-bottom:0.4rem;">
                            <strong><?= e($part['name'] ?? '-') ?></strong>
                            <div style="font-size:0.9rem; color:#4b5563;"><?= e($part['reason'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>هیچ قطعه‌ای با احتمال نیاز به تعویض شناسایی نشد.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>تقویم نگهداری خودرو</h2>
            <?php $calendarItems = $healthData['calendar_items'] ?? []; ?>
            <?php if (!empty($calendarItems)): ?>
                <ul style="margin:0.5rem 0 0 1rem; padding:0; list-style:disc;">
                    <?php foreach ($calendarItems as $item): ?>
                        <li style="margin-bottom:0.4rem;">
                            <strong><?= e($item['title'] ?? '-') ?></strong>
                            <div style="font-size:0.95rem; color:#374151;">تاریخ: <?= e($item['date'] ?? '-') ?> · نوع: <?= e($item['type'] ?? '-') ?> · وضعیت: <?= e($item['status'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>هیچ رویداد نگهداری‌ای ثبت نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>تعمیرات (تاریخچه)</h2>
            <?php if (!empty($repairs)): ?>
                <ul style="margin:0.5rem 0 0 1rem; padding:0; list-style:disc;">
                    <?php foreach ($repairs as $r): ?>
                        <li style="margin-bottom:0.4rem;">
                            <strong><?= e($r['diagnosis'] ?? $r['repair_notes'] ?? 'تعمیر') ?></strong>
                            <div style="font-size:0.95rem; color:#374151;">وضعیت: <?= e($r['status'] ?? '-') ?> · هزینه: <?= e($r['cost'] ?? '-') ?> · تاریخ: <?= e($r['created_at'] ?? '-') ?></div>
                            <?php if (!empty($r['parts'])): ?>
                                <div style="margin-top:0.25rem;">قطعات:
                                    <ul style="margin:0.25rem 0 0 1rem; padding:0; list-style:disc;">
                                        <?php foreach ($r['parts'] as $p): ?>
                                            <li><?= e($p['title_fa'] ?: $p['title_en'] ?: $p['part_name'] ?? '-') ?> × <?= e((int) ($p['quantity'] ?? 0)) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>تعمیر ثبت‌شده‌ای وجود ندارد.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>سرویس‌ها و یادآورها</h2>
            <?php if (!empty($upcoming)): ?>
                <?php foreach ($upcoming as $u): ?>
                    <div style="border:1px solid #f1f5f9; border-radius:8px; padding:0.6rem; margin-bottom:0.6rem; background:#fafbfc;">
                        <strong><?= e($u['title'] ?? 'سرویس') ?></strong>
                        <div style="font-size:0.95rem; color:#374151;">تاریخ: <?= e($u['service_date'] ?? '-') ?> · وضعیت: <?= e($u['status'] ?? '-') ?></div>
                        <div style="margin-top:0.4rem;"><a href="<?= SITE_URL ?>/booking?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-primary">رزرو سرویس</a></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>هیچ سرویس نزدیکی ثبت نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>گزارش‌های عیب‌یابی</h2>
            <?php if (!empty($diagnostics)): ?>
                <ul>
                    <?php foreach ($diagnostics as $d): ?>
                        <li><?= e($d['title_fa'] ?? $d['title_en'] ?? ($d['code'] ?? '-')) ?> · <?= e($d['created_at'] ?? '-') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>گزارش عیب‌یابی موجود نیست.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>پیشنهاد قطعات سازگار</h2>
            <?php if (!empty($suggestions)): ?>
                <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:0.6rem;">
                    <?php foreach ($suggestions as $p): ?>
                        <div style="border:1px solid #f1f5f9; border-radius:8px; padding:0.6rem; background:#fff;">
                            <div><strong><?= e($p['title_fa'] ?? $p['title_en'] ?? '-') ?></strong></div>
                            <div style="font-size:0.95rem; color:#374151;">قیمت: <?= e($p['price'] ?? '-') ?></div>
                            <div style="margin-top:0.4rem;"><a href="<?= SITE_URL ?>/products/<?= rawurlencode($p['slug'] ?? '') ?>" class="btn-outline">مشاهده</a></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>محصول قابل‌تطبیقی یافت نشد.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
