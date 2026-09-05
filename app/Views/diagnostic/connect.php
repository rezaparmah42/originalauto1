<?php
$title = 'اتصال دستگاه OBD2 | ' . SITE_NAME;
$description = 'اتصال به ELM327 و آماده‌سازی برای اسکن.';
$canonical = SITE_URL . '/diagnostic/connect';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>اتصال به دستگاه</h1>
    <p><strong>خودرو:</strong> <?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></p>
    <p><strong>وضعیت اتصال:</strong> <?= e($connection['status'] ?? 'connected') ?></p>
    <p><strong>دستگاه:</strong> <?= e($connection['device'] ?? 'ELM327') ?></p>

    <form method="post" action="<?= SITE_URL ?>/diagnostic/scan">
        <?= csrf_field() ?>
        <input type="hidden" name="vehicle_id" value="<?= (int) ($vehicle['id'] ?? 0) ?>">
        <input type="hidden" name="device_type" value="ELM327">
        <button type="submit" class="btn-primary">آغاز اسکن</button>
    </form>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
