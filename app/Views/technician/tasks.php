<?php
$title = 'وظایف من | ' . SITE_NAME;
?>
<?php require __DIR__.'/../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>وظایف من</h1>
    <?php if (!empty($tasks)): foreach ($tasks as $t): ?>
        <div style="border:1px solid #eee; padding:0.6rem; margin-bottom:0.4rem; background:#fff;">
            <p><a href="<?= SITE_URL ?>/technician/task/<?= (int)$t['id'] ?>"><?= e($t['title']) ?></a> — <?= e($t['status']) ?></p>
            <p>تعمیر: <?= e($t['repair_id']) ?></p>
        </div>
    <?php endforeach; else: ?>
        <p>وظیفی یافت نشد.</p>
    <?php endif; ?>
</div>
<?php require __DIR__.'/../layouts/footer.php'; ?>
