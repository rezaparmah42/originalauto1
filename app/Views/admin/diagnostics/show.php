<?php
$title = 'جزئیات تشخیصی | ' . SITE_NAME;
$description = 'نمایش جزئیات جلسه تشخیصی و کدهای خطا.';
$canonical = SITE_URL . '/admin/diagnostics/show/' . (int) ($session['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>جزئیات جلسه تشخیصی</h1>
    <p><strong>خودرو:</strong> <?= e($session['vehicle_model'] ?? '-') ?></p>
    <p><strong>مشتری:</strong> <?= e($session['customer_name'] ?? '-') ?></p>
    <p><strong>وضعیت اتصال:</strong> <?= e($session['connection_status'] ?? '-') ?></p>

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
        <p>نتیجه‌ای ثبت نشده است.</p>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
