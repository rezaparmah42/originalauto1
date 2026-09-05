<?php
$title = 'تشخیص هوشمند | ' . SITE_NAME;
$description = 'ورود کدهای DTC و دریافت تحلیل هوشمند.';
$canonical = SITE_URL . '/ai-diagnostic';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>تشخیص هوشمند</h1>

    <?php if (!empty($vehicles)): ?>
        <form method="post" action="<?= SITE_URL ?>/ai-diagnostic/analyze">
            <?= csrf_field() ?>
            <label for="vehicle_id">خودرو</label>
            <select id="vehicle_id" name="vehicle_id" required style="display:block; width:100%; padding:0.7rem; margin:0.7rem 0 1rem;">
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?= (int) ($vehicle['id'] ?? 0) ?>"><?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></option>
                <?php endforeach; ?>
            </select>

            <label for="codes">کدهای DTC (با فاصله یا کاما جدا کنید)</label>
            <textarea id="codes" name="codes" rows="3" style="width:100%; padding:0.7rem; margin:0.7rem 0;"></textarea>

            <button type="submit" class="btn-primary">تحلیل</button>
        </form>
    <?php else: ?>
        <p>خودرویی ثبت نشده است.</p>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
