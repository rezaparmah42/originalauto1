<?php
$vehicleName = trim(($vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '') . ' ' . ($vehicle['name_fa'] ?? $vehicle['model'] ?? ''));
$brandSlug = (string) ($vehicle['brand_slug'] ?? $vehicle['brand'] ?? '');
$modelSlug = (string) ($vehicle['model_slug'] ?? $vehicle['slug'] ?? $vehicle['model'] ?? '');
$variantData = is_array($variant ?? null) ? $variant : (is_array($vehicle['variant'] ?? null) ? $vehicle['variant'] : null);
$variantName = trim((string) ($variantData['name_fa'] ?? $vehicle['variant_name_fa'] ?? ''));
$variantSlug = trim((string) ($variantData['slug'] ?? $vehicle['variant_slug'] ?? ''));
$variantEngineCode = trim((string) ($variantData['engine_code'] ?? $vehicle['variant_engine_code'] ?? ''));
$variantEngineType = trim((string) ($variantData['engine_type'] ?? $vehicle['variant_engine_type'] ?? ''));
$variantFuelType = trim((string) ($variantData['fuel_type'] ?? $vehicle['variant_fuel_type'] ?? ''));
$variantTransmission = trim((string) ($variantData['transmission'] ?? $vehicle['variant_transmission'] ?? ''));
$displayYearFrom = $variantData['year_from'] ?? $vehicle['variant_year_from'] ?? ($vehicle['year_from'] ?? null);
$displayYearTo = $variantData['year_to'] ?? $vehicle['variant_year_to'] ?? ($vehicle['year_to'] ?? null);
$displayEngineType = $variantEngineType !== '' ? $variantEngineType : (string) ($vehicle['engine_type'] ?? '');
$baseVehicleName = $vehicleName;
if ($variantName !== '') {
    $vehicleName = trim($baseVehicleName . ' ' . $variantName);
}
$title = $vehicleName . '؛ مشخصات و ایرادهای رایج | ' . SITE_NAME;
$description = 'اطلاعات فنی، ایرادهای رایج، مسیر عیب‌یابی و خدمات پیشنهادی برای ' . $vehicleName . '.';
if (!empty($variantData['seo_title_fa'])) {
    $title = trim((string) $variantData['seo_title_fa']);
}
if (!empty($variantData['seo_description_fa'])) {
    $description = trim((string) $variantData['seo_description_fa']);
} elseif (!empty($variantData['description_fa'])) {
    $description = trim((string) $variantData['description_fa']);
}
$canonical = SITE_URL . '/vehicles/' . rawurlencode($brandSlug ?: ($vehicle['brand'] ?? '')) . '/' . rawurlencode($modelSlug ?: ($vehicle['slug'] ?? ''));
if ($variantSlug !== '') {
    $canonical .= '/' . rawurlencode($variantSlug);
}
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'کاتالوگ خودرو', 'url' => SITE_URL . '/vehicles'],
    ['name' => $vehicleName, 'url' => $canonical],
];
$og_type = 'website';
$image = !empty($vehicle['image']) ? SITE_URL . '/uploads/' . ltrim($vehicle['image'], '/') : null;
$generatedModel = $generatedModel ?? [];
$serviceLinks = $serviceLinks ?? [];
$vehicleFaq = $vehicleFaq ?? [];
require __DIR__ . '/../layouts/header.php';
$schema = ['@context' => 'https://schema.org', '@type' => 'Car', 'name' => $vehicleName, 'brand' => ['@type' => 'Brand', 'name' => $vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '']];
if ($variantName !== '') {
    $schema['vehicleConfiguration'] = $variantName;
}
if ($variantEngineType !== '' || $variantFuelType !== '') {
    $schema['vehicleEngine'] = ['@type' => 'EngineSpecification'];
    if ($variantEngineType !== '') {
        $schema['vehicleEngine']['name'] = $variantEngineType;
    }
    if ($variantFuelType !== '') {
        $schema['vehicleEngine']['fuelType'] = $variantFuelType;
    }
}
if ($variantEngineCode !== '') {
    $schema['additionalProperty'][] = [
        '@type' => 'PropertyValue',
        'name' => 'engineCode',
        'value' => $variantEngineCode,
    ];
}
if ($displayYearFrom !== null && $displayYearFrom !== '') {
    $schema['productionDate'] = (string) $displayYearFrom;
}
?>
<?php
$schemaData = $schema;
require __DIR__ . '/../partials/schema.php';
?>
<section class="page-hero"><div class="hero-badge"><a href="<?= SITE_URL ?>/vehicles">کاتالوگ خودرو</a> / <?= e($vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '') ?></div><h1><?= e($vehicleName) ?></h1><p>اطلاعات موجود در کاتالوگ را با گزارش علائم خودرو و تست‌های انجام‌شده تطبیق دهید.</p><div class="hero-actions"><a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو عیب‌یابی</a><a class="btn-outline" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode((string) ($vehicle['brand'] ?? '')) ?>">بازگشت به برند</a></div></section>
	<?php $heroImg = SITE_URL . '/uploads/vehicles/' . rawurlencode($brandSlug ?: ($vehicle['brand'] ?? '')) . '/' . rawurlencode($modelSlug ?: ($vehicle['slug'] ?? $vehicle['model'] ?? '')) . '.jpg'; ?>
	<div class="hero-media">
		<img src="<?= $heroImg ?>" alt="<?= e($vehicleName) ?>" width="1200" height="675" loading="eager" fetchpriority="high" onerror="this.style.display='none'" title="<?= e($vehicleName) ?>">
	</div>
<?php $midImg = SITE_URL . '/uploads/vehicles/' . rawurlencode($brandSlug ?: ($vehicle['brand'] ?? '')) . '/' . rawurlencode($modelSlug ?: ($vehicle['slug'] ?? $vehicle['model'] ?? '')) . '-mid.jpg'; ?>
<?php if ($variantData): ?>
<section class="section-shell">
    <div class="detail-card">
        <?php if ($variantName !== ''): ?><h2><?= e($variantName) ?></h2><?php endif; ?>
        <?php if ($variantEngineCode !== ''): ?><p><strong>کد موتور:</strong> <?= e($variantEngineCode) ?></p><?php endif; ?>
        <?php if ($displayYearFrom !== null && $displayYearFrom !== ''): ?><p><strong>سال:</strong> <?= e($displayYearFrom) ?><?= ($displayYearTo !== null && $displayYearTo !== '') ? ' تا ' . e($displayYearTo) : '' ?></p><?php endif; ?>
        <?php if ($variantEngineType !== ''): ?><p><strong>موتور:</strong> <?= e($variantEngineType) ?></p><?php endif; ?>
        <?php if ($variantFuelType !== ''): ?><p><strong>سوخت:</strong> <?= e($variantFuelType) ?></p><?php endif; ?>
        <?php if ($variantTransmission !== ''): ?><p><strong>گیربکس:</strong> <?= e($variantTransmission) ?></p><?php endif; ?>
        <?php if (!empty($variantData['description_fa'])): ?><p><?= e($variantData['description_fa']) ?></p><?php endif; ?>
    </div>
</section>
<?php endif; ?>
<div class="section-shell">
	<div class="hero-media mid"><img src="<?= $midImg ?>" alt="نمای سرویس <?= e($vehicleName) ?>" width="800" height="600" loading="lazy" onerror="this.style.display='none'" title="<?= e($vehicleName) ?>"></div>
</div>
<section class="section-shell">
    <div class="detail-grid">
        <article class="detail-card">
            <h2>مشخصات پایه</h2>
            <p><strong>برند:</strong> <?= e($vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? '-') ?></p>
            <p><strong>سال تولید:</strong> <?= e($vehicle['year_from'] ?? '-') ?><?= !empty($vehicle['year_to']) ? ' تا ' . e($vehicle['year_to']) : '' ?></p>
            <p><strong>موتور:</strong> <?= e($vehicle['engine_type'] ?? 'ثبت نشده') ?></p>
            <p><strong>بدنه:</strong> <?= e($vehicle['body_type'] ?? 'ثبت نشده') ?></p>
        </article>
        <article class="detail-card">
            <h2>راهنمای تشخیص</h2>
            <p>برای این مدل، ابتدا علائم، ولتاژ باتری و خطاهای ECU ثبت می‌شود. نتیجه اسکن باید با داده زنده و تست مکانیکی مقایسه شود؛ یک کد خطا به‌تنهایی علت قطعی خرابی نیست.</p>
        </article>
    </div>
</section>

<?php if (!empty($generatedModel['html'])): ?>
<section class="section-shell">
    <div class="section-heading"><h2>معرفی کوتاه <?= e($vehicleName) ?></h2></div>
    <div class="detail-card"><?= $generatedModel['html'] ?></div>
</section>
<?php endif; ?>

<section class="section-shell">
    <div class="section-heading"><h2>مشکلات و نشانه‌های رایج</h2></div>
    <div class="service-grid">
        <?php if (!empty($symptoms)): ?>
            <?php foreach ($symptoms as $symptom): ?>
                <article class="service-card">
                    <h3><?= e($symptom['name'] ?? 'نشانه خرابی') ?></h3>
                    <p><?= e($symptom['description'] ?? '') ?></p>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="info-card"><strong>هنوز ایراد اختصاصی برای این مدل ثبت نشده است.</strong><span>در صورت مشاهده مشکل، برای بررسی دقیق رزرو کنید.</span></div>
        <?php endif; ?>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading"><h2>خدمات تخصصی برای این مدل</h2></div>
    <div class="resource-links">
        <?php foreach (($serviceLinks ?? []) as $serviceLink): ?>
            <a href="<?= e($serviceLink['service_url'] ?? '#') ?>"><?= e($serviceLink['title_fa'] ?? '') ?></a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading"><h2>سرویس‌ها و زیرخدمت‌های مرتبط</h2></div>
    <div class="resource-links">
        <?php foreach (($serviceLinks ?? []) as $serviceLink): ?>
            <a href="<?= e($serviceLink['service_url'] ?? '#') ?>"><?= e($serviceLink['title_fa'] ?? '') ?></a>
            <?php if (!empty($serviceLink['sub_url'])): ?>
                <a href="<?= e($serviceLink['sub_url']) ?>" class="text-link">زیرخدمت</a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php if (!empty($articles)): ?><section class="section-shell"><div class="section-heading"><h2>مقالات مرتبط</h2></div><div class="service-grid"><?php foreach ($articles as $article): ?><a class="service-card" href="<?= SITE_URL ?>/articles/<?= rawurlencode($article['slug'] ?? '') ?>"><h3><?= e($article['title_fa'] ?? '') ?></h3><p><?= e($article['meta_description_fa'] ?? '') ?></p></a><?php endforeach; ?></div></section><?php endif; ?>

<?php if (!empty($vehicleFaq)): ?>
<section class="section-shell">
    <div class="section-heading"><h2>پرسش‌های متداول</h2></div>
    <div class="faq-list">
        <?php foreach ($vehicleFaq as $faq): ?>
            <?php $faqQuestion = $faq['name'] ?? $faq['question'] ?? ''; $faqAnswer = $faq['acceptedAnswer']['text'] ?? $faq['acceptedAnswer']['answer'] ?? ''; ?>
            <?php if ($faqQuestion !== ''): ?>
                <div class="faq-item">
                    <h3><?= e($faqQuestion) ?></h3>
                    <?php if ($faqAnswer !== ''): ?><p><?= e($faqAnswer) ?></p><?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

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

<?php
if (!empty($generatedModel['faqSchema'])) {
    $faqLd = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $generatedModel['faqSchema']];
    echo "<script type=\"application/ld+json\">" . json_encode($faqLd, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) . "</script>";
}
?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
