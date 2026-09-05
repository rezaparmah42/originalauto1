<?php
$title = 'گفت‌وگو کارگاه | ' . SITE_NAME;
?>
<?php require __DIR__.'/../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>گفت‌وگو کارگاه</h1>
    <?php if (!empty($messages)): foreach ($messages as $m): ?>
        <div style="border:1px solid #eee; padding:0.6rem; margin-bottom:0.4rem; background:#fff;">
            <p><strong><?= e($m['sender_type']) ?> #<?= e($m['sender_id']) ?></strong> — <?= e($m['created_at']) ?></p>
            <p><?= e($m['message']) ?></p>
        </div>
    <?php endforeach; else: ?>
        <p>هیچ پیامی وجود ندارد.</p>
    <?php endif; ?>

    <form method="post" action="<?= SITE_URL ?>/repair/chat/send">
        <?= csrf_field() ?>
        <input type="hidden" name="repair_id" value="<?= (int)($repair_id ?? 0) ?>">
        <textarea name="message" rows="4" style="width:100%;"></textarea>
        <button class="btn-primary">ارسال</button>
    </form>
</div>
<?php require __DIR__.'/../layouts/footer.php'; ?>
