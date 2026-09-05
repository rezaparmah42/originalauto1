<?php
$title = 'اسکن OBD2 | ' . SITE_NAME;
$description = 'نمایش نتایج اسکن و کدهای خطای شناسایی‌شده.';
$canonical = SITE_URL . '/diagnostic/scan';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<?php if (!empty($message)): ?><div class="alert alert-success" role="status"><?= e($message) ?></div><?php endif; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>نتایج اسکن</h1>
    <?php if (!empty($vehicle)): ?>
        <p><strong>خودرو:</strong> <?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></p>
    <?php endif; ?>
    <p><strong>جلسه:</strong> #<?= e($session_id ?? 0) ?></p>
    <p><strong>وضعیت اتصال:</strong> <?= e($connection['status'] ?? 'connected') ?></p>

    <?php if (!empty($results)): ?>
        <ul>
            <?php foreach ($results as $result): ?>
                <li>
                    <strong><?= e($result['code'] ?? '-') ?></strong> — <?= e($result['title_fa'] ?? '-') ?>
                    <br>
                    <span>شدت: <?= e($result['severity'] ?? '-') ?></span>
                    <br>
                    <span>پیشنهاد: <?= e($result['recommended_actions'] ?? '-') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>کد خطایی ثبت نشد.</p>
    <?php endif; ?>

    <a href="<?= SITE_URL ?>/diagnostic/result?session_id=<?= (int) ($session_id ?? 0) ?>" class="btn-outline">مشاهده جزئیات</a>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>