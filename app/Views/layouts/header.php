<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#0d1724">
<?php
$metaDescriptionRaw = $description ?? (SITE_NAME . ' تعمیرگاه تخصصی خودرو در تهران؛ ارائه خدمات دیاگ، تعمیر موتور، گیربکس، برق و سرویس دوره‌ای برای خودروهای داخلی و وارداتی.');
$metaDescription = mb_substr(strip_tags($metaDescriptionRaw), 0, 160);
?>
<meta name="description" content="<?= e($metaDescription) ?>">
<meta name="keywords" content="تعمیرگاه خودرو, تعمیرگاه تهران, تعمیر پژو, تعمیر گیربکس, دیاگ خودرو, سرویس دوره ای, تعمیر موتور, تعمیر برق خودرو, تعمیرگاه تخصصی, تعمیرگاه خودرو ایرانی, تعمیر خودرو چینی, تعمیر خودرو کره ای, تعمیر خودرو ژاپنی, تعمیر خودرو آلمانی">
<meta name="robots" content="<?= e($robots ?? 'index, follow') ?>">
<meta name="geo.region" content="IR-TE">
<meta name="geo.placename" content="تهران">
<meta name="geo.position" content="35.6892;51.3890">
<meta name="ICBM" content="35.6892, 51.3890">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:locale" content="fa_IR">
<?php
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';
?>
<link rel="canonical" href="<?= e($canonical ?? (rtrim(SITE_URL, '/') . rtrim($requestPath, '/'))) ?>">
<meta property="og:title" content="<?= e($title ?? SITE_NAME) ?>">
<meta property="og:description" content="<?= e($metaDescription) ?>">
<?php
// Determine og:type if page provides $og_type, otherwise use sensible defaults
$ogType = $og_type ?? ($schemaType ?? null);
if (empty($ogType)) {
    $ogType = 'website';
}
?>
<meta property="og:type" content="<?= e($ogType) ?>">
<meta property="og:url" content="<?= e($canonical ?? (SITE_URL . rtrim($requestPath, '/'))) ?>">
<?php
// Choose image: page-provided $image or a fallback asset if present
$fallbackPath = __DIR__ . '/../../assets/images/og-fallback.png';
$ogImage = null;
if (!empty($image)) {
    $ogImage = $image;
} elseif (file_exists($fallbackPath)) {
    $ogImage = rtrim(SITE_URL, '/') . '/assets/images/og-fallback.png';
}
if (!empty($ogImage)):
?>
<meta property="og:image" content="<?= e($ogImage) ?>">
<meta name="twitter:image" content="<?= e($ogImage) ?>">
<?php endif; ?>
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($title ?? SITE_NAME) ?>">
<meta name="twitter:description" content="<?= e($metaDescription) ?>">
<title><?= e($title ?? SITE_NAME) ?></title>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => ['AutoRepair', 'LocalBusiness'],
    'name' => SITE_NAME,
    'description' => $metaDescription,
    'url' => rtrim(SITE_URL, '/'),
    'telephone' => SITE_PHONE,
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => 'تهران',
        'addressRegion' => 'تهران',
        'streetAddress' => SITE_ADDRESS,
    ],
    'geo' => [
        '@type' => 'GeoCoordinates',
        'latitude' => 35.6892,
        'longitude' => 51.3890,
    ],
    'openingHoursSpecification' => [[
        '@type' => 'OpeningHoursSpecification',
        'dayOfWeek' => ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'],
        'opens' => '08:00', 'closes' => '20:00'
    ]],
    'areaServed' => 'تهران',
    'sameAs' => ['https://www.instagram.com/originalshargh','https://www.telegram.me/originalshargh'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php
// Render BreadcrumbList if $breadcrumb is provided as an ordered array of ['name' => '...', 'url' => '...']
if (!empty($breadcrumb) && is_array($breadcrumb)) {
    $items = [];
    $pos = 1;
    foreach ($breadcrumb as $b) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'name' => $b['name'] ?? '',
            'item' => $b['url'] ?? ''
        ];
    }
    echo "<script type=\"application/ld+json\">" . json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "</script>\n";
}
?>
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/responsive.css">

</head>

<body class="site-body">

<header class="site-header">
    <div class="header-topbar">
        <div class="header-topbar-inner">
            <span>📞 <?= e(SITE_PHONE) ?></span>
            <span>🕒 ۸:۰۰ تا ۲۰:۰۰</span>
            <a href="<?= SITE_URL ?>/booking">رزرو سریع</a>
        </div>
    </div>
    <div class="header-inner">
        <a class="brand" href="<?= SITE_URL ?>/">
            <span class="brand-name"><?= e(SITE_NAME) ?></span>
            <span class="brand-tag">تعمیرگاه تخصصی خودرو</span>
        </a>

        <?php
        // Build main navigation items and allow temporary hiding via config
        $mainNav = [
            ['key' => 'home', 'url' => SITE_URL . '/', 'label' => 'خانه'],
            ['key' => 'services', 'url' => SITE_URL . '/services', 'label' => 'خدمات'],
            ['key' => 'articles', 'url' => SITE_URL . '/articles', 'label' => 'مقالات'],
            ['key' => 'shop', 'url' => SITE_URL . '/shop', 'label' => 'فروشگاه'],
            ['key' => 'booking', 'url' => SITE_URL . '/booking', 'label' => 'رزرو'],
            ['key' => 'diagnostic', 'url' => SITE_URL . '/diagnostic', 'label' => 'عیب‌یابی آنلاین'],
        ];

        $hide = defined('TEMP_NAV_HIDE') && TEMP_NAV_HIDE === true;
        $hideList = [];
        if ($hide && defined('TEMP_NAV_HIDE_ITEMS')) {
            $decoded = json_decode(TEMP_NAV_HIDE_ITEMS, true);
            if (is_array($decoded)) { $hideList = $decoded; }
        }
        ?>
        <nav class="main-nav" aria-label="ناوبری اصلی">
            <?php foreach ($mainNav as $item): ?>
                <?php if ($hide && in_array($item['key'], $hideList, true)) { continue; } ?>
                <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
            <?php endforeach; ?>
        </nav>

        <div class="header-actions">
            <?php if (isLoggedIn()): ?>
                <a class="btn-outline" href="<?= SITE_URL ?>/admin/dashboard">مدیریت</a>
                <a class="btn-outline" href="<?= SITE_URL ?>/admin/logout">خروج</a>
            <?php elseif (isCustomerLoggedIn()): ?>
                <span class="header-user">سلام، <?= e(currentCustomerName()) ?></span>
                <a class="btn-outline" href="<?= SITE_URL ?>/dashboard">داشبورد</a>
                <a class="btn-outline" href="<?= SITE_URL ?>/logout">خروج</a>
            <?php else: ?>
                <?php if (!defined('TEMP_HIDE_AUTH_BUTTONS') || !TEMP_HIDE_AUTH_BUTTONS): ?>
                    <a class="btn-outline" href="<?= SITE_URL ?>/login">ورود</a>
                    <a class="btn-primary" href="<?= SITE_URL ?>/register">ثبت نام</a>
                <?php endif; ?>
            <?php endif; ?>
            <a class="btn-outline" href="<?= SITE_URL ?>/booking">رزرو آنلاین</a>
        </div>
    </div>
</header>

<main class="site-main">
<?php if (!empty($breadcrumb) && is_array($breadcrumb)): ?>
    <nav aria-label="مسیر" class="breadcrumb" style="max-width:1100px;margin:0.75rem auto;padding:0 1rem;color:#374151;font-size:0.95rem;">
        <?php foreach ($breadcrumb as $i => $b): ?>
            <?php if ($i > 0): ?> <span aria-hidden="true">›</span> <?php endif; ?>
            <a href="<?= e($b['url'] ?? '#') ?>" style="color:#0d6efd;text-decoration:none;margin:0 0.4rem;"><?= e($b['name'] ?? '') ?></a>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
