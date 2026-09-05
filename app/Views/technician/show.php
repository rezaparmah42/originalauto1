<?php
$title = 'نمایش وظیفه | ' . SITE_NAME;
?>
<?php require __DIR__.'/../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>نمایش وظیفه</h1>
    <?php if (!empty($task)): foreach ($task as $t): ?>
        <h2><?= e($t['title']) ?></h2>
        <p>تعمیر: <?= e($t['repair_id']) ?></p>
        <p>شرح: <?= e($t['description']) ?></p>
        <p>وضعیت: <?= e($t['status']) ?></p>
    <?php endforeach; else: ?>
        <p>موردی یافت نشد.</p>
    <?php endif; ?>
</div>
<?php require __DIR__.'/../layouts/footer.php'; ?>
