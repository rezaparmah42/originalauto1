<?php
$brandLabel = $brand ?? 'برند خودرو';
$brandSlug = $brandSlug ?? rawurlencode((string) $brandLabel);
$title = 'تعمیرگاه تخصصی ' . $brandLabel . ' | ' . SITE_NAME;
$description = 'مدل‌های ثبت‌شده برند ' . $brandLabel . ' و خدمات تخصصی تعمیر، دیاگ و نگهداری برای این خودروها.';
$canonical = SITE_URL . '/vehicles/' . rawurlencode((string) $brandSlug);
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'کاتالوگ خودرو', 'url' => SITE_URL . '/vehicles'],
    ['name' => (string) $brandLabel, 'url' => $canonical],
];
require __DIR__ . '/../layouts/header.php';
?>
<section class="page-hero">
    <div class="hero-badge">کاتالوگ خودرو</div>
    <h1>تعمیرگاه تخصصی <?= e($brandLabel) ?></h1>
    <p>خودروهای این برند را انتخاب کنید تا از خدمات دینامیک تعمیر، عیب‌یابی و نگهداری تخصصی برای هر مدل مطلع شوید.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/vehicles">بازگشت به کاتالوگ</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/booking">رزرو سرویس</a>
    </div>
</section>
<section class="section-shell">
    <div class="service-grid">
        <?php foreach (($vehicles ?? []) as $item): ?>
            <?php $modelSlug = $item['slug'] ?? $item['model'] ?? ''; ?>
            <a class="service-card" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode((string) $brandSlug) ?>/<?= rawurlencode((string) $modelSlug) ?>">
                <span class="meta-pill"><?= e($item['brand'] ?? $brandLabel) ?></span>
                <h2><?= e($item['model'] ?? $item['name_fa'] ?? 'مدل خودرو') ?></h2>
                <p><?= e($item['engine_type'] ?? 'اطلاعات موتور ثبت نشده است.') ?></p>
                <span class="text-link">مشاهده مدل و خدمات</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
