<?php
$vehicleName = trim(($vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '') . ' ' . ($vehicle['name_fa'] ?? $vehicle['model'] ?? ''));
$title = $vehicleName . '؛ مشخصات و ایرادهای رایج | ' . SITE_NAME;
$description = 'اطلاعات فنی، ایرادهای رایج، مسیر عیب‌یابی و خدمات پیشنهادی برای ' . $vehicleName . '.';
$canonical = SITE_URL . '/vehicles/' . rawurlencode($vehicle['brand'] ?? '') . '/' . rawurlencode($vehicle['slug'] ?? '');
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'کاتالوگ خودرو', 'url' => SITE_URL . '/vehicles'],
    ['name' => $vehicleName, 'url' => $canonical],
];
$og_type = 'website';
$image = !empty($vehicle['image']) ? SITE_URL . '/uploads/' . ltrim($vehicle['image'], '/') : null;
require __DIR__ . '/../layouts/header.php';
$schema = ['@context' => 'https://schema.org', '@type' => 'Car', 'name' => $vehicleName, 'brand' => ['@type' => 'Brand', 'name' => $vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '']];
?>
<?php
$schemaData = $schema;
require __DIR__ . '/../partials/schema.php';
?>
<section class="page-hero"><div class="hero-badge"><a href="<?= SITE_URL ?>/vehicles">کاتالوگ خودرو</a> / <?= e($vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '') ?></div><h1><?= e($vehicleName) ?></h1><p>اطلاعات موجود در کاتالوگ را با گزارش علائم خودرو و تست‌های انجام‌شده تطبیق دهید.</p><div class="hero-actions"><a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو عیب‌یابی</a><a class="btn-outline" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode((string) ($vehicle['brand'] ?? '')) ?>">بازگشت به برند</a></div></section>
	<?php $heroImg = SITE_URL . '/uploads/vehicles/' . rawurlencode($vehicle['brand'] ?? '') . '/' . rawurlencode($vehicle['slug'] ?? $vehicle['model'] ?? '') . '.jpg'; ?>
	<div class="hero-media">
		<img src="<?= $heroImg ?>" alt="<?= e($vehicleName) ?>" width="1200" height="675" loading="eager" fetchpriority="high" onerror="this.style.display='none'" title="<?= e($vehicleName) ?>">
	</div>
<?php $midImg = SITE_URL . '/uploads/vehicles/' . rawurlencode($vehicle['brand'] ?? '') . '/' . rawurlencode($vehicle['slug'] ?? $vehicle['model'] ?? '') . '-mid.jpg'; ?>
<div class="section-shell">
	<div class="hero-media mid"><img src="<?= $midImg ?>" alt="نمای سرویس <?= e($vehicleName) ?>" width="800" height="600" loading="lazy" onerror="this.style.display='none'" title="<?= e($vehicleName) ?>"></div>
</div>
<section class="section-shell"><div class="detail-grid"><article class="detail-card"><h2>مشخصات پایه</h2><p><strong>برند:</strong> <?= e($vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '-') ?></p><p><strong>سال تولید:</strong> <?= e($vehicle['year_from'] ?? '-') ?><?= !empty($vehicle['year_to']) ? ' تا ' . e($vehicle['year_to']) : '' ?></p><p><strong>موتور:</strong> <?= e($vehicle['engine_type'] ?? 'ثبت نشده') ?></p><p><strong>بدنه:</strong> <?= e($vehicle['body_type'] ?? 'ثبت نشده') ?></p></article><article class="detail-card"><h2>راهنمای تشخیص</h2><p>برای این مدل، ابتدا علائم، ولتاژ باتری و خطاهای ECU ثبت می‌شود. نتیجه اسکن باید با داده زنده و تست مکانیکی مقایسه شود؛ یک کد خطا به‌تنهایی علت قطعی خرابی نیست.</p></article></div></section>
<section class="section-shell"><div class="section-heading"><h2>مشکلات و نشانه‌های رایج</h2></div><div class="service-grid"><?php if (!empty($symptoms)): ?><?php foreach ($symptoms as $symptom): ?><article class="service-card"><h3><?= e($symptom['name'] ?? 'نشانه خرابی') ?></h3><p><?= e($symptom['description'] ?? '') ?></p></article><?php endforeach; ?><?php else: ?><div class="info-card"><strong>هنوز ایراد اختصاصی برای این مدل ثبت نشده است.</strong><span>در صورت مشاهده مشکل، برای بررسی دقیق رزرو کنید.</span></div><?php endif; ?></div></section>
<section class="section-shell"><div class="section-heading"><h2>خدمات پیشنهادی</h2></div><div class="resource-links"><?php foreach (($services ?? []) as $service): ?><a href="<?= SITE_URL ?>/services/<?= rawurlencode($service['slug'] ?? '') ?>"><?= e($service['title_fa'] ?? '') ?></a><?php endforeach; ?></div></section>
<section class="section-shell"><div class="section-heading"><h2>خدمات مرتبط با این مدل</h2></div><div class="resource-links"><?php $serviceSlugs = ['diagnostic','electrical','engine','gearbox','periodic-service','ac-repair','suspension','brakes','battery','ecu','pre-purchase-inspection','emergency','airbag','car-air-filter-and-filters','oil-change']; foreach ($serviceSlugs as $serviceSlug): ?><a href="<?= SITE_URL ?>/services/<?= rawurlencode($serviceSlug) ?>/<?= rawurlencode((string) ($vehicle['slug'] ?? '')) ?>"><?= e($serviceSlug === 'diagnostic' ? 'دیاگ تخصصی' : ($serviceSlug === 'electrical' ? 'تعمیر برق' : ($serviceSlug === 'engine' ? 'تعمیر موتور' : ($serviceSlug === 'periodic-service' ? 'سرویس دوره‌ای' : ($serviceSlug === 'oil-change' ? 'تعویض روغن' : 'خدمت مرتبط'))))) ?></a><?php endforeach; ?></div></section>
<?php if (!empty($articles)): ?><section class="section-shell"><div class="section-heading"><h2>مقالات مرتبط</h2></div><div class="service-grid"><?php foreach ($articles as $article): ?><a class="service-card" href="<?= SITE_URL ?>/articles/<?= rawurlencode($article['slug'] ?? '') ?>"><h3><?= e($article['title_fa'] ?? '') ?></h3><p><?= e($article['meta_description_fa'] ?? '') ?></p></a><?php endforeach; ?></div></section><?php endif; ?>
<?php if (!empty($products)): ?>
<section class="section-shell"><div class="section-heading"><h2>محصولات سازگار</h2></div><div class="service-grid">
	<?php foreach ($products as $p): ?>
		<a class="service-card" href="<?= SITE_URL ?>/products/<?= rawurlencode($p['slug'] ?? '') ?>">
			<h3><?= e($p['title_fa'] ?? $p['title_en'] ?? $p['title'] ?? '') ?></h3>
			<p><?= e($p['price'] ?? '-') ?> — موجودی: <?= e((int) ($p['stock'] ?? 0)) ?></p>
			<span class="text-link">مشاهده محصول</span>
		</a>
	<?php endforeach; ?>
</div></section>
<?php endif; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
