<?php
$title = 'گزارش هوشمند | ' . SITE_NAME;
$description = 'گزارش سلامت خودرو بر پایه تحلیل هوشمند.';
$canonical = SITE_URL . '/ai-diagnostic/report';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>گزارش هوشمند</h1>

    <p><strong>خودرو:</strong> <?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></p>

    <?php if (!empty($report)): ?>
        <p><strong>امتیاز سلامت:</strong> <?= e($report['health_score'] ?? '-') ?>%</p>
        <p><strong>ایجاد شده در:</strong> <?= e($report['generated_at'] ?? '-') ?></p>

        <?php if (!empty($analysis)): ?>
            <h2>تحلیل موارد</h2>
            <?php foreach ($analysis as $item): ?>
                <div style="border:1px solid #eef2f7; padding:0.8rem; border-radius:8px; margin-bottom:0.6rem; background:#fff;">
                    <p><strong><?= e($item['code']) ?> — <?= e($item['title']) ?></strong></p>
                    <p><?= e($item['description']) ?></p>
                    <p><strong>پیشنهادات:</strong> <?= e($item['recommended_actions']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php else: ?>
        <p>گزارشی موجود نیست.</p>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
