<?php
$title = 'دیاگ تخصصی خودرو | ' . SITE_NAME;
$description = 'تشخیص و رفع خطاهای ECU و سیستم الکترونیکی خودرو با تجهیزات دیاگ پیشرفته در اورجینال شرق.';
$canonical = SITE_URL . '/services/diagnostic';
$robots = 'index, follow';
// Breadcrumb for diagnostic service
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'دیاگ تخصصی', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    'serviceType' => 'دیاگ تخصصی خودرو',
    'provider' => [
        '@type' => 'LocalBusiness',
        'name' => SITE_NAME,
        'telephone' => SITE_PHONE,
        'areaServed' => 'تهران',
    ],
    'description' => 'تشخیص و رفع خطاهای ECU و سیستم الکترونیکی خودرو با تجهیزات دیاگ پیشرفته.',
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'IRR',
        'price' => '0',
        'availability' => 'https://schema.org/InStock',
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        [
            '@type' => 'Question',
            'name' => 'آیا همه مشکلات خودرو با دیاگ قابل تشخیص هستند؟',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'بسیاری از خرابی‌ها در سیستم ECU، سنسورها و مدارهای الکترونیکی با دیاگ مشخص می‌شوند، اما در بعضی موارد برای تشخیص نهایی به بررسی فنی بیشتر نیاز است.'
            ]
        ],
        [
            '@type' => 'Question',
            'name' => 'بعد از دیاگ، آیا قطعه باید تعویض شود؟',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => 'نه لزوماً. گاهی کد خطا فقط نشانه‌ای از مشکل جانبی است و نیاز به بررسی عملکرد واقعی سنسور یا مدار دارد.'
            ]
        ]
    ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

<section class="page-hero">
    <div class="hero-badge">تشخیص دقیق و سریع</div>
    <h1>دیاگ تخصصی خودرو برای پیدا کردن ریشه مشکل با دقت و سرعت</h1>
    <p>در اورجینال شرق، سیستم ECU، سنسورها، لوازم برقی، سوخت‌رسانی و عملکرد موتور با تجهیزات حرفه‌ای بررسی می‌شوند تا مشکل واقعی خودرو به‌درستی شناسایی و تعمیر شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو دیاگ خودرو</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/diagnostic">عیب‌یابی آنلاین</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>مشکل مشتری چیست؟</h2>
            <p>چراغ چک، کاهش قدرت، روشن یا خاموش شدن ناگهانی، لرزش موتور، مصرف سوخت بالا، مشکل در ترمز، سیستم تهویه و رفتار غیرعادی خودرو معمولاً نشان‌دهنده نیاز به دیاگ تخصصی است.</p>
        </div>
        <div class="detail-card">
            <h2>در دیاگ تخصصی چه چیزی بررسی می‌شود؟</h2>
            <ul>
                <li>کدهای خطای ECU و ABS و سیستم‌های جانبی</li>
                <li>سنسورهای اکسیژن، مپ، سرعت و دمای موتور</li>
                <li>بررسی عملکرد انژکتور، سیستم جرقه و سوخت‌رسانی</li>
                <li>تشخیص دقیق علت اصلی مشکل قبل از تعویض قطعه</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>مزیت خدمات ما</h2>
            <ul>
                <li>استفاده از دستگاه‌های پیشرفته و آپدیت‌شده</li>
                <li>تفسیر دقیق کدهای خطا با تجربه فنی</li>
                <li>کمک به انتخاب درست قطعه و جلوگیری از هزینه‌ اضافی</li>
                <li>بررسی همزمان با سیستم‌های برقی و مکانیکی خودرو</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>علائم هشداری</h2>
            <ul>
                <li>روشن شدن چراغ موتور</li>
                <li>افت شدید شتاب</li>
                <li>لرزش یا خاموشی در دور آرام</li>
                <li>خرابی سیستم ترمز، گیربکس یا کولر</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول درباره دیاگ خودرو</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا همه مشکلات خودرو با دیاگ قابل تشخیص هستند؟</h3>
            <p>بسیاری از خرابی‌ها در سیستم ECU، سنسورها و مدارهای الکترونیکی با دیاگ مشخص می‌شوند، اما در بعضی موارد برای تشخیص نهایی به بررسی فنی بیشتر نیاز است.</p>
        </div>
        <div class="faq-item">
            <h3>بعد از دیاگ، آیا قطعه باید تعویض شود؟</h3>
            <p>نه لزوماً. گاهی کد خطا فقط نشانه‌ای از مشکل جانبی است و نیاز به بررسی عملکرد واقعی سنسور یا مدار دارد. ما قبل از تعویض به طور دقیق بررسی می‌کنیم.</p>
        </div>
        <div class="faq-item">
            <h3>برای خودروهای داخلی و وارداتی هم دیاگ انجام می‌گیرید؟</h3>
            <p>بله، خدمات دیاگ در تعمیرگاه ما برای خودروهای ایرانی، چینی، کره‌ای، ژاپنی و سایر برندهای وارداتی با تجهیزات مناسب انجام می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/services/ecu-programming">ECU Programming</a>
        <a href="<?= SITE_URL ?>/services/electrical-repair">تعمیر برق خودرو</a>
        <a href="<?= SITE_URL ?>/services/engine-repair">تعمیر موتور</a>
        <a href="<?= SITE_URL ?>/booking">رزرو تعمیر</a>
    </div>
</section>

<section class="cta">
    <h2>اگر چراغ خودرو روشن است یا عملکرد آن غیرعادی شده، همین حالا هماهنگ کنید</h2>
    <p>در اورجینال شرق، تشخیص درست و تعمیر اصولی از همان ابتدا باعث جلوگیری از هزینه‌های مکرر و خرابی‌های آینده می‌شود.</p>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست رزرو دیاگ</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
