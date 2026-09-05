<?php
$title = $title ?? ('خودرو یافت نشد | ' . SITE_NAME);
$description = 'خودروی موردنظر در کاتالوگ موجود نیست.';
$canonical = SITE_URL . '/vehicles';
$robots = 'noindex, follow';
require __DIR__ . '/../layouts/header.php';
?>
<section class="page-hero"><div class="hero-badge">کاتالوگ خودرو</div><h1><?= e($title) ?></h1><p><?= e($message ?? 'این مسیر خودرو معتبر نیست.') ?></p><div class="hero-actions"><a class="btn-primary" href="<?= SITE_URL ?>/vehicles">بازگشت به کاتالوگ</a><a class="btn-outline" href="<?= SITE_URL ?>/booking">رزرو مشاوره</a></div></section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
