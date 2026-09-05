<?php
$brandLabel = $brand ?? 'برند خودرو';
$title = 'خودروهای ' . $brandLabel . ' | ' . SITE_NAME;
$description = 'مدل‌های ثبت‌شده برند ' . $brandLabel . ' و اطلاعات تعمیر و عیب‌یابی مرتبط.';
$canonical = SITE_URL . '/vehicles/' . rawurlencode($brandLabel);
$robots = 'index, follow';
require __DIR__ . '/../layouts/header.php';
?>
<section class="page-hero"><div class="hero-badge">کاتالوگ خودرو</div><h1>مدل‌های <?= e($brandLabel) ?></h1><p>مدل‌های موجود را انتخاب کنید تا اطلاعات فنی، ایرادهای رایج و خدمات پیشنهادی را ببینید.</p></section>
<section class="section-shell"><div class="service-grid"><?php foreach (($vehicles ?? []) as $item): ?><a class="service-card" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode($brandLabel) ?>/<?= rawurlencode($item['slug'] ?? $item['model'] ?? '') ?>"><h2><?= e($item['model'] ?? 'مدل خودرو') ?></h2><p><?= e($item['engine_type'] ?? 'اطلاعات موتور ثبت نشده است.') ?></p><span class="text-link">مشاهده جزئیات</span></a><?php endforeach; ?></div></section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
