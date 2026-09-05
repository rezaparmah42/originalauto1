<?php
$title = 'جزئیات تعمیر | ' . SITE_NAME;
$description = 'نمایش جزئیات تعمیر و وضعیت در تعمیرگاه.';
$canonical = SITE_URL . '/admin/workshop/repairs/' . (int) ($repair['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 980px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <h1>جزئیات تعمیر</h1>
            <p>اطلاعات مشتری، خودرو، مشکل و وضعیت کار.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/workshop/repairs" class="btn-outline">بازگشت به لیست</a>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <div style="display:grid; gap:1rem;">
        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">اطلاعات مشتری و خودرو</h2>
            <p><strong>مشتری:</strong> <?= e($repair['customer_name'] ?? '-') ?></p>
            <p><strong>تلفن:</strong> <?= e($repair['customer_phone'] ?? '-') ?></p>
            <p><strong>خودرو:</strong> <?= e(($repair['vehicle_brand'] ?? '-') . ' ' . ($repair['vehicle_model'] ?? '-')) ?></p>
            <p><strong>سال:</strong> <?= e($repair['vehicle_year'] ?? '-') ?></p>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">مشکل و شرح</h2>
            <p><?= nl2br(e($repair['problem'] ?? $repair['diagnosis'] ?? '-')) ?></p>
            <p><strong>نوع خدمت:</strong> <?= e($repair['service_title'] ?? '-') ?></p>
            <p><strong>زمان پذیرش:</strong> <?= e($repair['booking_date'] ?? $repair['created_at'] ?? '-') ?></p>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">وضعیت تعمیر</h2>
            <p><strong>وضعیت فعلی:</strong> <?= e(repairStatusLabel($repair['status'] ?? '')) ?></p>
            <form method="post" action="<?= SITE_URL ?>/admin/workshop/repairs/status/<?= (int) ($repair['id'] ?? 0) ?>" style="display:flex; gap:0.7rem; flex-wrap:wrap; align-items:center; margin-top:0.8rem;">
                <?= csrf_field() ?>
                <select name="status" class="form-control">
                    <?php foreach (repairStatuses() as $statusKey => $statusLabel): ?>
                        <option value="<?= e($statusKey) ?>" <?= (($repair['status'] ?? '') === $statusKey) ? 'selected' : '' ?>><?= e($statusLabel) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-primary">به‌روزرسانی وضعیت</button>
            </form>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">تاریخچه وضعیت</h2>
            <?php if (!empty($timeline['updates'] ?? []) || !empty($timeline['notes'] ?? []) || !empty($timeline['tasks'] ?? [])): ?>
                <ul>
                    <?php foreach (($timeline['updates'] ?? []) as $update): ?>
                        <li><strong><?= e($update['status'] ?? '-') ?>:</strong> <?= e($update['title'] ?? '') ?> — <?= e($update['created_at'] ?? '') ?></li>
                    <?php endforeach; ?>
                    <?php foreach (($timeline['notes'] ?? []) as $note): ?>
                        <li><strong>یادداشت:</strong> <?= e($note['note'] ?? '') ?> — <?= e($note['created_at'] ?? '') ?></li>
                    <?php endforeach; ?>
                    <?php foreach (($timeline['tasks'] ?? []) as $task): ?>
                        <li><strong>وظیفه:</strong> <?= e($task['title'] ?? '') ?> (<?= e($task['status'] ?? '-') ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>تاریخچه‌ای ثبت نشده است.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
