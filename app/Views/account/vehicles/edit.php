<?php
$title = 'ویرایش خودرو | ' . SITE_NAME;
$description = 'ویرایش اطلاعات خودرو در پروفایل مشتری.';
$canonical = SITE_URL . '/account/vehicles/edit/' . (int) ($vehicle['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 800px;">
    <h1>ویرایش خودرو</h1>
    <p>تغییر اطلاعات خودرو برای نگهداری و تعمیرات.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= SITE_URL ?>/account/vehicles/update/<?= (int) ($vehicle['id'] ?? 0) ?>">
        <?= csrf_field() ?>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>برند</label>
            <select name="brand_id" class="form-control" required>
                <option value="">انتخاب کنید</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?= (int) ($brand['id'] ?? 0) ?>" <?= ((string) ($vehicle['brand_id'] ?? '') === (string) ($brand['id'] ?? '')) ? 'selected' : '' ?>><?= e($brand['name'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>مدل</label>
            <input type="text" name="model" value="<?= e($vehicle['model'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>سال</label>
            <input type="text" name="year" value="<?= e($vehicle['year'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>موتور</label>
            <input type="text" name="engine" value="<?= e($vehicle['engine'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>VIN</label>
            <input type="text" name="vin" value="<?= e($vehicle['vin'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>کیلومتر</label>
            <input type="number" name="mileage" value="<?= e($vehicle['mileage'] ?? 0) ?>" class="form-control" min="0" required>
        </div>
        <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
            <button type="submit" class="btn-primary">به‌روزرسانی خودرو</button>
            <a href="<?= SITE_URL ?>/account/vehicles" class="btn-outline">انصراف</a>
        </div>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>