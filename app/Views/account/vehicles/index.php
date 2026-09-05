<?php
$title = 'خودروهای من | ' . SITE_NAME;
$description = 'مدیریت خودروهای ثبت‌شده شما در اورجینال شرق.';
$canonical = SITE_URL . '/account/vehicles';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <span>خودروهای من</span>
            </nav>
            <h1>خودروهای من</h1>
            <p>مدیریت خودروهای ثبت‌شده و مشاهده سابقه تعمیرات.</p>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/account/vehicles/create" class="btn-primary">افزودن خودرو</a>
            <a href="<?= SITE_URL ?>/dashboard" class="btn-outline">بازگشت به داشبورد</a>
        </div>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <div style="display:grid; gap:1rem;">
        <?php if (!empty($vehicles)): ?>
            <?php foreach ($vehicles as $vehicle): ?>
                <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
                    <div style="display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                        <div>
                            <h3><?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></h3>
                            <p>سال: <?= e($vehicle['year'] ?? '-') ?> | موتور: <?= e($vehicle['engine'] ?? '-') ?></p>
                            <p>کیلومتر: <?= e($vehicle['mileage'] ?? 0) ?></p>
                        </div>
                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                            <a href="<?= SITE_URL ?>/account/vehicle-profile?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">پروفایل هوشمند</a>
                            <a href="<?= SITE_URL ?>/account/vehicle/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">جزئیات خودرو</a>
                            <a href="<?= SITE_URL ?>/account/vehicle/health/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">گزارش سلامت</a>
                            <a href="<?= SITE_URL ?>/account/vehicle-profile/maintenance?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">نگهداری</a>
                            <a href="<?= SITE_URL ?>/account/vehicle-profile/history?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">تاریخچه</a>
                            <a href="<?= SITE_URL ?>/account/vehicles/history/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">سابقه تعمیر</a>
                            <a href="<?= SITE_URL ?>/account/vehicles/edit/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">ویرایش</a>
                            <form method="post" action="<?= SITE_URL ?>/account/vehicles/delete/<?= (int) ($vehicle['id'] ?? 0) ?>" onsubmit="return confirm('آیا از حذف این خودرو اطمینان دارید؟');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-primary" style="background:#b91c1c; border-color:#b91c1c;">حذف</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="border:1px dashed #cbd5e1; border-radius:12px; padding:1rem; background:#f8fafc;">هنوز خودرویی به حساب شما اضافه نشده است.</div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>