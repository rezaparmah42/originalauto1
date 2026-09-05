<?php
$title = 'نتیجه تحلیل | ' . SITE_NAME;
$description = 'نتیجه تحلیل هوشمند کدهای DTC.';
$canonical = SITE_URL . '/ai-diagnostic/analyze';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>نتیجه تحلیل</h1>

    <p><strong>خودرو:</strong> <?= e($vehicle['brand_name'] ?? '-') ?> <?= e($vehicle['model'] ?? '-') ?></p>

    <?php if (!empty($analysis)): ?>
        <?php foreach ($analysis as $item): ?>
            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:1rem; background:#fff; margin-bottom:0.8rem;">
                <h3><?= e($item['code']) ?> — <?= e($item['title']) ?></h3>
                <p><strong>شرح:</strong> <?= e($item['description']) ?></p>
                <p><strong>شدت:</strong> <?= e($item['severity']) ?></p>
                <p><strong>دلایل احتمالی:</strong> <?= e($item['possible_causes']) ?></p>
                <p><strong>اقدامات پیشنهادی:</strong> <?= e($item['recommended_actions']) ?></p>
                <?php if (!empty($item['recommendations'])): ?>
                    <p><strong>قطعات پیشنهادی:</strong></p>
                    <ul>
                        <?php foreach ($item['recommendations'] as $rec): ?>
                            <li><?= e($rec['part_name']) ?> — <?= e($rec['action']) ?> (اولویت: <?= e($rec['priority']) ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>تحلیلی یافت نشد.</p>
    <?php endif; ?>

    <?php if (!empty($report)): ?>
        <div style="margin-top:1rem; border:1px solid #e5e7eb; border-radius:10px; padding:1rem; background:#fff;">
            <h2>گزارش سلامت خودرو</h2>
            <p><strong>امتیاز سلامت:</strong> <?= e($report['health_score'] ?? '-') ?>%</p>
            <p><strong>تاریخ:</strong> <?= e($report['generated_at'] ?? '-') ?></p>
        </div>
    <?php endif; ?>

</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
