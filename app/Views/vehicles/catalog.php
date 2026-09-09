<?php
$title = 'کاتالوگ خودرو و راهنمای تعمیر | ' . SITE_NAME;
$description = 'جست‌وجو و انتخاب خودرو برای مشاهده اطلاعات فنی، ایرادهای رایج و خدمات پیشنهادی تعمیرگاه.';
$canonical = SITE_URL . '/vehicles';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'کاتالوگ خودرو', 'url' => $canonical],
];
require __DIR__ . '/../layouts/header.php';
$brandGroups = [
    'داخلی' => [],
    'فرانسوی (تولید داخل)' => [],
    'کره‌ای' => [],
    'ژاپنی' => [],
    'چینی' => [],
];
foreach (($brands ?? []) as $brandItem) {
    $brandName = is_array($brandItem) ? ($brandItem['name_fa'] ?? $brandItem['name_en'] ?? $brandItem['slug'] ?? '') : (string) $brandItem;
    $brandSlug = is_array($brandItem) ? ($brandItem['slug'] ?? rawurlencode((string) $brandName)) : rawurlencode((string) $brandName);
    $brandKey = is_array($brandItem) ? ($brandItem['category'] ?? '') : '';
    if ($brandKey === 'iranian' || $brandKey === 'internal' || $brandKey === 'local') {
        $brandGroups['داخلی'][] = ['name' => $brandName, 'slug' => $brandSlug, 'count' => (int) ($brandItem['model_count'] ?? 0)];
    } elseif (stripos((string) $brandKey, 'french') !== false || stripos((string) $brandKey, 'france') !== false) {
        $brandGroups['فرانسوی (تولید داخل)'][] = ['name' => $brandName, 'slug' => $brandSlug, 'count' => (int) ($brandItem['model_count'] ?? 0)];
    } elseif (stripos((string) $brandKey, 'korean') !== false || stripos((string) $brandKey, 'کره') !== false) {
        $brandGroups['کره‌ای'][] = ['name' => $brandName, 'slug' => $brandSlug, 'count' => (int) ($brandItem['model_count'] ?? 0)];
    } elseif (stripos((string) $brandKey, 'japan') !== false || stripos((string) $brandKey, 'ژاپن') !== false) {
        $brandGroups['ژاپنی'][] = ['name' => $brandName, 'slug' => $brandSlug, 'count' => (int) ($brandItem['model_count'] ?? 0)];
    } elseif (stripos((string) $brandKey, 'china') !== false || stripos((string) $brandKey, 'چین') !== false) {
        $brandGroups['چینی'][] = ['name' => $brandName, 'slug' => $brandSlug, 'count' => (int) ($brandItem['model_count'] ?? 0)];
    } else {
        $brandGroups['داخلی'][] = ['name' => $brandName, 'slug' => $brandSlug, 'count' => (int) ($brandItem['model_count'] ?? 0)];
    }
}
?>
<section class="page-hero">
    <div class="hero-badge">کاتالوگ خودروهای بازار ایران</div>
    <h1>کاتالوگ خودروهای تحت پوشش تعمیرگاه اورجینال شرق</h1>
    <p>از خودروهای داخلی و وارداتی تا برندهای پرطرفدار بازار ایران، در این کاتالوگ می‌توانید برند، مدل و خدمات مرتبط را برای هر خودرو به‌صورت دقیق پیدا کنید.</p>
</section>
<section class="section-shell">
    <form class="modern-form vehicle-filter" method="get" action="<?= SITE_URL ?>/vehicles">
        <div class="form-group"><label class="form-label" for="q">جست‌وجوی خودرو</label><input class="form-input" id="q" name="q" value="<?= e($query ?? '') ?>" placeholder="مثلاً پژو 206 یا Corolla"></div>
        <div class="form-group"><label class="form-label" for="brand">برند</label><select class="form-input" id="brand" name="brand"><option value="">همه برندها</option><?php foreach (($brands ?? []) as $brand): $brandValue = is_array($brand) ? ($brand['slug'] ?? $brand['name_fa'] ?? $brand['name_en'] ?? '') : (string) $brand; ?><option value="<?= e($brandValue) ?>" <?= (($filters['brand'] ?? '') === $brandValue) ? 'selected' : '' ?>><?= e(is_array($brand) ? ($brand['name_fa'] ?? $brand['name_en'] ?? '') : $brand) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label class="form-label" for="model">مدل</label><select class="form-input" id="model" name="model"><option value="">همه مدل‌ها</option><?php foreach (($models ?? []) as $model): ?><option value="<?= e($model) ?>" <?= (($filters['model'] ?? '') === $model) ? 'selected' : '' ?>><?= e($model) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label class="form-label" for="engine">موتور</label><select class="form-input" id="engine" name="engine"><option value="">همه موتورها</option><?php foreach (($engines ?? []) as $engine): ?><option value="<?= e($engine) ?>" <?= (($filters['engine'] ?? '') === $engine) ? 'selected' : '' ?>><?= e($engine) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label class="form-label" for="year">سال</label><input class="form-input" id="year" name="year" inputmode="numeric" value="<?= e($filters['year'] ?? '') ?>" placeholder="مثلاً 1398"></div>
        <div class="form-actions"><button class="btn-primary" type="submit">جست‌وجو</button><a class="btn-outline" href="<?= SITE_URL ?>/vehicles">پاک کردن فیلتر</a></div>
    </form>
</section>
<?php foreach ($brandGroups as $groupName => $groupItems): ?>
<?php if (empty($groupItems)) continue; ?>
<section class="section-shell">
    <div class="section-heading"><h2><?= e($groupName) ?></h2></div>
    <div class="service-grid">
        <?php foreach ($groupItems as $brand): ?>
            <?php $brandRoute = (string) ($brand['slug'] ?? $brand['brand_slug'] ?? ''); ?>
            <a class="service-card" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode($brandRoute) ?>">
                <span class="meta-pill"><?= (int) ($brand['count'] ?? 0) ?> مدل</span>
                <h3><?= e((string) ($brand['name'] ?? 'برند')) ?></h3>
                <p>مشاهده خودروهای این برند و خدمات تخصصی مرتبط.</p>
                <span class="text-link">مشاهده مدل‌ها</span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endforeach; ?>
<section class="section-shell">
    <div class="section-heading"><h2>خودروهای پیشنهادی</h2><p><?= (int) ($total ?? 0) ?> خودرو در کاتالوگ فعال است.</p></div>
    <div class="service-grid vehicle-grid">
        <?php if (!empty($vehicles)): ?><?php foreach ($vehicles as $item): $brandRoute = (string) ($item['brand_slug'] ?? $item['brand'] ?? ''); $modelRoute = (string) ($item['model_slug'] ?? $item['slug'] ?? $item['model'] ?? ''); ?><a class="service-card" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode($brandRoute) ?>/<?= rawurlencode($modelRoute) ?>"><span class="meta-pill"><?= e($item['brand'] ?? 'برند خودرو') ?></span><h3><?= e($item['model'] ?? $item['name_fa'] ?? 'مدل خودرو') ?></h3><p><?= e($item['engine_type'] ?? 'اطلاعات موتور در کاتالوگ ثبت نشده است.') ?></p><div class="service-meta"><span><?= e($item['year_start'] ?? '-') ?><?= !empty($item['year_end']) ? ' تا ' . e($item['year_end']) : '' ?></span></div></a><?php endforeach; ?><?php else: ?><div class="info-card"><strong>خودرویی با این فیلتر پیدا نشد.</strong><span>نام فارسی یا انگلیسی خودرو را ساده‌تر جست‌وجو کنید.</span></div><?php endif; ?>
    </div>
</section>
<?php
$variantLinks = [];
foreach (($vehicles ?? []) as $item) {
    if (!is_array($item) || empty($item['variants']) || !is_array($item['variants'])) {
        continue;
    }
    $brandRoute = (string) ($item['brand_slug'] ?? $item['brand'] ?? '');
    $modelRoute = (string) ($item['model_slug'] ?? $item['slug'] ?? $item['model'] ?? '');
    foreach ($item['variants'] as $variantItem) {
        if (!is_array($variantItem) || empty($variantItem['slug'])) {
            continue;
        }
        $variantLinks[] = [
            'brand' => $item['brand'] ?? '',
            'model' => $item['model'] ?? $item['name_fa'] ?? '',
            'brand_route' => $brandRoute,
            'model_route' => $modelRoute,
            'variant' => $variantItem,
        ];
    }
}
?>
<?php if (!empty($variantLinks)): ?>
<section class="section-shell">
    <div class="section-heading"><h2>تیپ‌های خودرو</h2></div>
    <div class="service-grid vehicle-grid">
        <?php foreach ($variantLinks as $link): $variantItem = $link['variant']; ?>
            <a class="service-card" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode((string) $link['brand_route']) ?>/<?= rawurlencode((string) $link['model_route']) ?>/<?= rawurlencode((string) ($variantItem['slug'] ?? '')) ?>">
                <span class="meta-pill"><?= e((string) ($link['brand'] ?? '')) ?></span>
                <h3><?= e(trim(($link['model'] ?? '') . ' ' . ($variantItem['name_fa'] ?? $variantItem['name_en'] ?? ''))) ?></h3>
                <?php if (!empty($variantItem['engine_code'])): ?><p><?= e($variantItem['engine_code']) ?></p><?php endif; ?>
                <?php if (isset($variantItem['year_from']) && $variantItem['year_from'] !== null): ?><div class="service-meta"><span><?= e($variantItem['year_from']) ?><?= !empty($variantItem['year_to']) ? ' تا ' . e($variantItem['year_to']) : '' ?></span></div><?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
<?php if (!empty($popular)): ?><section class="section-shell"><div class="section-heading"><h2>مدل‌های تازه کاتالوگ</h2></div><div class="resource-links"><?php foreach ($popular as $item): $brandRoute = (string) ($item['brand_slug'] ?? $item['brand'] ?? ''); $modelRoute = (string) ($item['model_slug'] ?? $item['slug'] ?? ''); ?><a href="<?= SITE_URL ?>/vehicles/<?= rawurlencode($brandRoute) ?>/<?= rawurlencode($modelRoute) ?>"><?= e(($item['brand'] ?? '') . ' ' . ($item['model'] ?? '')) ?></a><?php endforeach; ?></div></section><?php endif; ?>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
