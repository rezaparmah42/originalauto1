<?php
$title = 'عیب‌یابی OBD2 | ' . SITE_NAME;
$description = 'انتخاب خودرو و شروع جلسه تشخیصی OBD2.';
$canonical = SITE_URL . '/diagnostic';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>عیب‌یابی OBD2</h1>
    <p>برای شروع، خودرو خود را انتخاب کنید و جلسه تشخیصی را آغاز نمایید.</p>

    <?php if (!empty($vehicles)): ?>
        <form method="get" action="<?= SITE_URL ?>/diagnostic/connect">
            <label for="vehicle_id">خودرو</label>
            <select id="vehicle_id" name="vehicle_id" required style="display:block; width:100%; padding:0.7rem; margin:0.7rem 0 1rem;">
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?= (int) ($vehicle['id'] ?? 0) ?>"><?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-primary">اتصال به ELM327</button>
        </form>
    <?php else: ?>
        <p>هنوز خودرویی ثبت نشده است.</p>
    <?php endif; ?>

    <?php if (!empty($latest)): ?>
        <div style="margin-top:1.5rem; border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
            <h2>آخرین اسکن</h2>
            <p><strong>کد:</strong> <?= e($latest['code'] ?? '-') ?></p>
            <p><strong>شدت:</strong> <?= e($latest['severity'] ?? '-') ?></p>
            <p><strong>تاریخ:</strong> <?= e($latest['created_at'] ?? '-') ?></p>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
