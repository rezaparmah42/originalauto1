<?php
$title = 'تاریخچه خودرو | ' . SITE_NAME;
$description = 'نمایش تاریخچه تعمیر و خدمات خودرو.';
$canonical = SITE_URL . '/account/vehicle-profile/history';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <div>
            <nav aria-label="breadcrumb" style="font-size:0.9rem; color:#6b7280; margin-bottom:0.5rem;">
                <a href="<?= SITE_URL ?>/dashboard" style="color:#2563eb; text-decoration:none;">داشبورد</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/vehicles" style="color:#2563eb; text-decoration:none;">خودروهای من</a>
                <span> / </span>
                <a href="<?= SITE_URL ?>/account/vehicle-profile?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" style="color:#2563eb; text-decoration:none;">پروفایل خودرو</a>
                <span> / </span>
                <span>تاریخچه</span>
            </nav>
            <h1>تاریخچه خودرو</h1>
            <p><?= e(($vehicle['brand_name'] ?? '-') . ' ' . ($vehicle['model'] ?? '-')) ?> · سال <?= e($vehicle['year'] ?? '-') ?></p>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/account/vehicle-profile?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">بازگشت به پروفایل</a>
            <a href="<?= SITE_URL ?>/account/vehicle/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">جزئیات خودرو</a>
            <a href="<?= SITE_URL ?>/account/vehicle/health/<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">گزارش سلامت</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/maintenance?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">نگهداری</a>
            <a href="<?= SITE_URL ?>/account/vehicle-profile/diagnostics?vehicle_id=<?= (int) ($vehicle['id'] ?? 0) ?>" class="btn-outline">تشخیصی</a>
        </div>
    </div>
    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>تاریخ</th>
                <th>وضعیت</th>
                <th>مشکل</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($history)): ?>
                <?php foreach ($history as $item): ?>
                    <tr>
                        <td><?= e($item['booking_date'] ?? '-') ?></td>
                        <td><?= e($item['status'] ?? '-') ?></td>
                        <td><?= e($item['problem'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">تاریخچه ای ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
