<?php
$title = 'برندهای خودرو | ' . SITE_NAME;
$description = 'لیست برندهای فعال خودرو در کاتالوگ اورجینال شرق با مدل‌های ثبت‌شده و خدمات مرتبط.';
require __DIR__ . '/../layouts/header.php';
?>
<section class="section-hero">
    <div class="page-intro">
        <h1>برندهای خودرو</h1>
        <p>برندهای فعال در کاتالوگ به‌صورت زنده از پایگاه داده بارگذاری می‌شوند تا همیشه با داده‌های واقعی و به‌روز هماهنگ باشند.</p>
    </div>

    <div class="cards">
        <?php foreach (($brands ?? []) as $brand): ?>
            <?php $name = trim((string) ($brand['name_fa'] ?: ($brand['name'] ?: ($brand['name_en'] ?? '')))); ?>
            <?php $linkValue = $name !== '' ? $name : ('brand-' . ($brand['id'] ?? '')); ?>
            <a href="<?= SITE_URL ?>/brands/<?= rawurlencode($linkValue) ?>"><?= e($name ?: 'برند خودرو') ?></a>
        <?php endforeach; ?>
        <?php if (empty($brands)): ?><div class="info-card"><strong>برندی یافت نشد.</strong><span>داده‌های برندها در پایگاه داده فعلی فعال نیست.</span></div><?php endif; ?>
    </div>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
