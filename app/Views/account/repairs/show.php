<?php
$title = 'جزئیات تعمیر | ' . SITE_NAME;
$description = 'مشاهدۀ جزئیات تعمیر، خط‌زمان و وضعیت پرداخت.';
$canonical = SITE_URL . '/account/repairs/' . (int) ($repair['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/garage" style="color:#2563eb; text-decoration:none;">گاراژ من</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/repairs" style="color:#2563eb; text-decoration:none;">تعمیرات</a>
                <span> / </span>
                <span>جزئیات تعمیر</span>
            </nav>
            <h1>جزئیات تعمیر</h1>
            <p>وضعیت، خط زمان و اطلاعات فنی این تعمیر را مشاهده کنید.</p>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/repairs" class="btn-outline">بازگشت</a>
            <?php if (!empty($vehicle_id)): ?>
                <a href="<?= SITE_URL ?>/account/vehicle/<?= (int) $vehicle_id ?>" class="btn-outline">جزئیات خودرو</a>
                <a href="<?= SITE_URL ?>/account/vehicle/health/<?= (int) $vehicle_id ?>" class="btn-outline">گزارش سلامت</a>
            <?php endif; ?>
        </div>
    </div>

    <div style="display:grid; gap:1.3rem;">
        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">اطلاعات تعمیر</h2>
            <p><strong>خودرو:</strong> <?= e(($repair['vehicle_brand'] ?? '-') . ' ' . ($repair['vehicle_model'] ?? '-')) ?></p>
            <p><strong>خدمت:</strong> <?= e($repair['service_title'] ?? '-') ?></p>
            <p><strong>وضعیت:</strong> <?= e(repairStatusLabel($repair['status'] ?? '')) ?></p>
            <p><strong>هزینه:</strong> <?= e(number_format((float) ($repair['cost'] ?? 0), 0, '.', ',')) ?> تومان</p>
            <p><strong>تاریخ ثبت:</strong> <?= e($repair['created_at'] ?? '-') ?></p>
            <p><strong>توضیح:</strong> <?= e($repair['diagnosis'] ?? $repair['repair_notes'] ?? '-') ?></p>
            <?php if (!empty($repair['technician_name'])): ?>
                <p><strong>تکنسین:</strong> <?= e($repair['technician_name']) ?><?= !empty($repair['technician_phone']) ? ' — ' . e($repair['technician_phone']) : '' ?></p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">خط زمان تعمیر</h2>
            <?php if (!empty($timeline['updates']) || !empty($timeline['notes']) || !empty($timeline['tasks'])): ?>
                <ul style="margin:0; padding-right:1.2rem;">
                    <?php foreach (($timeline['updates'] ?? []) as $item): ?>
                        <li style="margin-bottom:0.6rem;"><strong><?= e($item['title'] ?? '-') ?></strong> — <?= e($item['description'] ?? '-') ?> <span style="color:#64748b;">(<?= e($item['created_at'] ?? '-') ?>)</span></li>
                    <?php endforeach; ?>
                    <?php foreach (($timeline['notes'] ?? []) as $note): ?>
                        <li style="margin-bottom:0.6rem;"><strong>یادداشت:</strong> <?= e($note['note'] ?? '-') ?> <span style="color:#64748b;">(<?= e($note['created_at'] ?? '-') ?>)</span></li>
                    <?php endforeach; ?>
                    <?php foreach (($timeline['tasks'] ?? []) as $task): ?>
                        <li style="margin-bottom:0.6rem;"><strong>کار:</strong> <?= e($task['title'] ?? '-') ?> — <?= e($task['status'] ?? '-') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>هنوز رویدادی برای این تعمیر ثبت نشده است.</p>
            <?php endif; ?>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">تاریخچه پرداخت</h2>
            <?php if (!empty($paymentHistory)): ?>
                <ul style="margin:0; padding-right:1.2rem;">
                    <?php foreach ($paymentHistory as $payment): ?>
                        <li style="margin-bottom:0.6rem;"><strong>سفارش:</strong> <?= e((int) ($payment['order_id'] ?? 0)) ?> — <strong>وضعیت:</strong> <?= e($payment['payment_status'] ?? '-') ?> — <strong>تراکنش:</strong> <?= e($payment['transaction_id'] ?? '-') ?> — <span style="color:#64748b;">(<?= e($payment['created_at'] ?? '-') ?>)</span></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>برای این تعمیر پرداختی ثبت نشده است.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require __DIR__.'/../../../layouts/footer.php'; ?>
