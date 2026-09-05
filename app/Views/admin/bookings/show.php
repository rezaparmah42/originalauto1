<?php
$title = 'جزئیات رزرو | ' . SITE_NAME;
$description = 'نمایش جزئیات رزرو و وضعیت تعمیر.';
$canonical = SITE_URL . '/admin/bookings/view/' . (int) ($booking['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 900px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <h1>جزئیات رزرو</h1>
            <p>مشاهده اطلاعات مشتری، خودرو، خدمات و وضعیت تعمیر.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/bookings" class="btn-outline">بازگشت</a>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <div style="display:grid; gap:1rem;">
        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">اطلاعات مشتری</h2>
            <p><strong>نام:</strong> <?= e($booking['customer_name'] ?? '-') ?></p>
            <p><strong>تلفن:</strong> <?= e($booking['customer_phone'] ?? '-') ?></p>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">اطلاعات خودرو</h2>
            <p><strong>برند:</strong> <?= e($booking['vehicle_brand'] ?? '-') ?></p>
            <p><strong>مدل:</strong> <?= e($booking['vehicle_model'] ?? '-') ?></p>
            <p><strong>سال:</strong> <?= e($booking['vehicle_year'] ?? '-') ?></p>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">خدمت انتخابی</h2>
            <p><?= e($booking['service_title'] ?? '-') ?></p>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">یادداشت‌ها</h2>
            <p><?= nl2br(e($booking['notes'] ?? '-')) ?></p>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">وضعیت رزرو</h2>
            <form method="post" action="<?= SITE_URL ?>/admin/bookings/status/<?= (int) ($booking['id'] ?? 0) ?>" style="display:flex; gap:0.7rem; flex-wrap:wrap; align-items:center;">
                <?= csrf_field() ?>
                <select name="status" class="form-control">
                    <option value="pending" <?= (($booking['status'] ?? '') === 'pending') ? 'selected' : '' ?>>در انتظار</option>
                    <option value="confirmed" <?= (($booking['status'] ?? '') === 'confirmed') ? 'selected' : '' ?>>تأیید شده</option>
                    <option value="in_progress" <?= (($booking['status'] ?? '') === 'in_progress') ? 'selected' : '' ?>>در حال انجام</option>
                    <option value="completed" <?= (($booking['status'] ?? '') === 'completed') ? 'selected' : '' ?>>تکمیل شده</option>
                    <option value="cancelled" <?= (($booking['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>>لغو شده</option>
                </select>
                <button type="submit" class="btn-primary">به‌روزرسانی</button>
            </form>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem 1.2rem;">
            <h2 style="margin-top:0;">وضعیت تعمیر</h2>
            <?php if (!empty($repairs)): ?>
                <?php foreach ($repairs as $repair): ?>
                    <p><strong>شرح:</strong> <?= e($repair['diagnosis'] ?? $repair['repair_notes'] ?? '-') ?></p>
                    <p><strong>وضعیت:</strong> <?= e($repair['status'] ?? '-') ?></p>
                    <p><strong>هزینه:</strong> <?= e($repair['cost'] ?? 0) ?></p>
                <?php endforeach; ?>
            <?php else: ?>
                <p>در حال حاضر تعمیر مرتبطی ثبت نشده است.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>