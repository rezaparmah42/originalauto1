<?php
$title = 'داشبورد تکنسین | ' . SITE_NAME;
?>
<?php require __DIR__.'/../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>داشبورد تکنسین</h1>
    <p>نام: <?= e($technician['name'] ?? '—') ?></p>
    <p>امتیاز عملکرد: <?= e($perf['score'] ?? 0) ?>%</p>
    <h2>وظایف اخیر</h2>
    <?php if (!empty($tasks)): foreach ($tasks as $t): ?>
        <div style="border:1px solid #eee; padding:0.6rem; margin-bottom:0.4rem; background:#fff;">
            <p><strong><?= e($t['title']) ?></strong> — وضعیت: <?= e($t['status']) ?></p>
            <p>تعمیر: <?= e($t['repair_id']) ?></p>
        </div>
    <?php endforeach; else: ?>
        <p>وظیفی اختصاص نیافته است.</p>
    <?php endif; ?>
</div>
<?php require __DIR__.'/../layouts/footer.php'; ?>
