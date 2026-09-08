<?php
$serviceMeta = $service ?? [];
$subservice = $subservice ?? [];
$relatedSubservices = $relatedSubservices ?? []; 
$relatedVehicles = $relatedVehicles ?? [];
$serviceSlug = (string) ($serviceMeta['slug'] ?? '');
$subSlug = (string) ($subservice['slug'] ?? '');
$title = !empty($subservice['seo_title']) ? $subservice['seo_title'] : (($subservice['title_fa'] ?? 'زیرخدمت') . ' | ' . SITE_NAME);
$description = !empty($subservice['seo_description']) ? $subservice['seo_description'] : ($subservice['intro'] ?? '');
$canonical = SITE_URL . '/services/' . rawurlencode($serviceSlug) . '/' . rawurlencode($subSlug);
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => $serviceMeta['title_fa'] ?? $serviceMeta['title_en'] ?? 'خدمت', 'url' => SITE_URL . '/services/' . rawurlencode($serviceSlug)],
    ['name' => $subservice['title_fa'] ?? $subservice['title_fa'] ?? 'زیرخدمت', 'url' => $canonical],
];
require __DIR__ . '/../layouts/header.php';
$schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'name' => $subservice['title_fa'] ?? 'خدمت تخصصی',
    'description' => $description,
    'provider' => [
        '@type' => 'AutoRepair',
        'name' => SITE_NAME,
        'url' => rtrim(SITE_URL, '/'),
        'telephone' => SITE_PHONE,
        'areaServed' => 'تهران',
    ],
    'url' => $canonical,
];
$schemaData = $schema;
require __DIR__ . '/../partials/schema.php';
?>
<section class="page-hero">
    <div class="hero-badge"><a href="<?= SITE_URL ?>/services">خدمات</a> / <?= e($serviceMeta['title_fa'] ?? $serviceMeta['title_en'] ?? 'خدمت') ?></div>
    <h1><?= e($subservice['h1'] ?? $subservice['title_fa'] ?? 'زیرخدمت') ?></h1>
    <p><?= e($subservice['intro'] ?? '') ?></p>
    <?php $heroImg = SITE_URL . '/uploads/services/' . rawurlencode($serviceSlug) . '/' . rawurlencode($subSlug ?: $serviceSlug) . '-hero.jpg'; ?>
    <div class="hero-media">
        <img src="<?= $heroImg ?>" alt="<?= e($subservice['title_fa'] ?? '') ?>" width="1200" height="675" loading="eager" fetchpriority="high" onerror="this.style.display='none'" title="<?= e($subservice['title_fa'] ?? '') ?>">
    </div>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو این خدمت</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/<?= rawurlencode($serviceSlug) ?>">بازگشت به خدمات <?= e($serviceMeta['title_fa'] ?? '') ?></a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <article class="detail-card">
            <h2>این علائم نشان می‌دهد که <?= e($subservice['title_fa'] ?? 'این خدمت') ?> لازم شده است</h2>
            <ul>
                <?php foreach (($subservice['signs'] ?? []) as $sign): ?>
                    <li><?= e($sign) ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
        <article class="detail-card">
            <h2>دلایل بروز مشکل</h2>
            <ul>
                <?php foreach (($subservice['causes'] ?? []) as $cause): ?>
                    <li><?= e($cause) ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>مراحل انجام کار در اورجینال شرق</h2>
    </div>
    <?php $midImg = SITE_URL . '/uploads/services/' . rawurlencode($serviceSlug) . '/' . rawurlencode($subSlug ?: $serviceSlug) . '-mid.jpg'; ?>
    <div class="hero-media mid">
        <img src="<?= $midImg ?>" alt="مراحل <?= e($subservice['title_fa'] ?? '') ?>" width="800" height="600" loading="lazy" onerror="this.style.display='none'" title="<?= e($subservice['title_fa'] ?? '') ?>">
    </div>
    <ol>
        <?php foreach (($subservice['steps'] ?? []) as $step): ?>
            <li><?= e($step) ?></li>
        <?php endforeach; ?>
    </ol>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>جدول تشخیص و اقدام</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>علامت / مشکل</th>
                    <th>اقدام فنی در تعمیرگاه</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($subservice['table_rows'] ?? []) as $row): ?>
                    <tr>
                        <td><?= e($row['issue'] ?? '') ?></td>
                        <td><?= e($row['solution'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>نکات تخصصی</h2>
    </div>
    <ul>
        <?php foreach (($subservice['tips'] ?? []) as $tip): ?>
            <li><?= e($tip) ?></li>
        <?php endforeach; ?>
    </ul>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>خودروهای تحت پوشش این خدمت</h2>
    </div>
    <div class="resource-links">
        <?php foreach (array_slice($relatedVehicles, 0, 8) as $vehicle): ?>
            <?php $brandLabel = $vehicle['brand_name_fa'] ?? $vehicle['brand'] ?? ''; $modelLabel = $vehicle['name_fa'] ?? $vehicle['model'] ?? ''; ?>
            <a href="<?= SITE_URL ?>/services/<?= rawurlencode($serviceSlug) ?>/<?= rawurlencode($vehicle['slug'] ?? '') ?>"><?= e($brandLabel . ' ' . $modelLabel) ?></a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="faq-list">
        <?php foreach (($subservice['faq'] ?? []) as $faq): ?>
            <div class="faq-item">
                <h3><?= e($faq['q'] ?? '') ?></h3>
                <p><?= e($faq['a'] ?? '') ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سرویس‌های مرتبط</h2>
    </div>
    <div class="resource-links">
        <?php foreach (array_slice($relatedSubservices, 0, 6) as $related): ?>
            <a href="<?= SITE_URL ?>/services/<?= rawurlencode($serviceSlug) ?>/<?= rawurlencode($related['slug'] ?? '') ?>"><?= e($related['title_fa'] ?? '') ?></a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section-shell">
    <div class="cta-box">
        <h2>رزرو مشاوره و بررسی دقیق</h2>
        <p>قبل از هر تعویض قطعه، برای اطمینان از تشخیص درست مشکل خودرو، با تیم فنی اورجینال شرق مشورت کنید.</p>
        <div class="hero-actions">
            <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو وقت</a>
            <a class="btn-outline" href="<?= SITE_URL ?>/contact">تماس با مشاور</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
