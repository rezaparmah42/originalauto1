<?php
$title = 'اطلاعیه‌ها | ' . SITE_NAME;
?>
<?php require __DIR__.'/../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1rem;">
        <h1>اطلاعیه‌ها</h1>
        <a href="<?= SITE_URL ?>/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>
    <?php if (!empty($items)): foreach ($items as $it): ?>
        <div style="border:1px solid #eee; padding:0.9rem; margin-bottom:0.7rem; background:#fff; border-radius:10px;">
            <p><strong><?= e($it['title']) ?></strong> — <?= e($it['type']) ?> — <?= e($it['created_at']) ?></p>
            <p><?= e($it['message']) ?></p>
            <?php if (($it['status'] ?? 'unread') !== 'read'): ?>
                <form method="post" action="<?= SITE_URL ?>/notifications/read/<?= (int)$it['id'] ?>" style="margin-top:0.5rem;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-outline">علامت‌گذاری به‌عنوان خوانده شده</button>
                </form>
            <?php else: ?>
                <p style="color:#64748b; margin:0;">خوانده شده</p>
            <?php endif; ?>
        </div>
    <?php endforeach; else: ?>
        <p>اطلاعیه‌ای یافت نشد.</p>
    <?php endif; ?>
</div>
<?php require __DIR__.'/../layouts/footer.php'; ?>
