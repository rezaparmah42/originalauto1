<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:2rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/garage" style="color:#2563eb; text-decoration:none;">گاراژ من</a>
                <span> / </span>
                <span>پیگیری تعمیرات</span>
            </nav>
            <h1>پیگیری تعمیرات</h1>
            <p><?= $vehicle_id > 0 ? 'نمایش تعمیرات خودرو انتخاب‌شده.' : 'وضعیت فعلی تعمیرات خود را در جدول زیر مشاهده کنید.' ?></p>
        </div>
        <a href="<?= SITE_URL ?>/account/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>

    <?php if (!empty($repairs)): ?>
        <?php foreach ($repairs as $repair): ?>
            <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff; margin-bottom:1rem;">
                <h2 style="margin-top:0;"><?= e(($repair['vehicle_brand'] ?? '-') . ' ' . ($repair['vehicle_model'] ?? '-')) ?></h2>
                <p><strong>خدمت:</strong> <?= e($repair['service_title'] ?? '-') ?></p>
                <p><strong>آخرین وضعیت:</strong> <a href="<?= SITE_URL ?>/account/repairs/<?= (int) ($repair['id'] ?? 0) ?>" style="color:#2563eb; text-decoration:none;"><?= e(repairStatusLabel($repair['status'] ?? '')) ?></a></p>
                <p><strong>آخرین بروزرسانی:</strong> <?= e($repair['created_at'] ?? '-') ?></p>
                <div style="margin-top:1rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <a href="<?= SITE_URL ?>/account/repairs/<?= (int) ($repair['id'] ?? 0) ?>" class="btn-outline">جزئیات تعمیر</a>
                    <?php $repairVehicleId = (int) ($repair['vehicle_id'] ?? $vehicle_id ?? 0); if ($repairVehicleId > 0): ?>
                        <a href="<?= SITE_URL ?>/account/vehicle/<?= $repairVehicleId ?>" class="btn-outline">خودرو</a>
                        <a href="<?= SITE_URL ?>/account/vehicle/health/<?= $repairVehicleId ?>" class="btn-outline">سلامت</a>
                    <?php endif; ?>
                </div>
                <div style="margin-top:1rem; padding:1rem; border:1px solid #f1f5f9; border-radius:10px; background:#f8fafc;">
                    <h3 style="margin-top:0;">خط زمان تعمیر</h3>
                    <ol style="padding-left:1.2rem; margin:0;">
                        <li <?= ($repair['status'] === 'pending' ? 'style="font-weight:bold;"' : '') ?>>درخواست ثبت شد</li>
                        <li <?= ($repair['status'] === 'inspection' ? 'style="font-weight:bold;"' : '') ?>>بازرسی خودرو</li>
                        <li <?= ($repair['status'] === 'approved' ? 'style="font-weight:bold;"' : '') ?>>تأیید شده</li>
                        <li <?= ($repair['status'] === 'in_progress' ? 'style="font-weight:bold;"' : '') ?>>در حال تعمیر</li>
                        <li <?= ($repair['status'] === 'waiting_parts' ? 'style="font-weight:bold;"' : '') ?>>انتظار قطعات</li>
                        <li <?= ($repair['status'] === 'completed' ? 'style="font-weight:bold;"' : '') ?>>تکمیل شده</li>
                        <li <?= ($repair['status'] === 'cancelled' ? 'style="font-weight:bold;"' : '') ?>>لغو شده</li>
                    </ol>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="border:1px dashed #cbd5e1; border-radius:12px; padding:1rem; background:#f8fafc;">هیچ تعمیراتی برای نمایش وجود ندارد.</div>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
