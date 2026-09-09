<?php
$title = ($service['title_fa'] ?? $service['title_en'] ?? 'خدمت') . ' ' . ($vehicle['name_fa'] ?? $vehicle['brand'] ?? '') . ' | ' . SITE_NAME;
$description = 'اطلاعات تخصصی خدمات ' . ($service['title_fa'] ?? '') . ' برای ' . ($vehicle['name_fa'] ?? $vehicle['brand'] ?? '') . '؛ بررسی علائم، مراحل سرویس و نکات نگهداری.';
$canonical = $canonical ?? (SITE_URL . '/services/' . rawurlencode($service['slug'] ?? '') . '/' . rawurlencode($vehicle['slug'] ?? ''));
$robots = 'index, follow';
require __DIR__ . '/../layouts/header.php';
?>
<section class="page-hero">
    <div class="hero-badge">خدمات تخصصی</div>
    <h1><?= e($service['title_fa'] ?? $service['title_en'] ?? 'خدمت') ?> <?= e($vehicle['name_fa'] ?? $vehicle['brand'] ?? '') ?></h1>
    <p><?= e($description) ?></p>
    <?php $heroImg = SITE_URL . '/uploads/vehicles/' . rawurlencode($vehicle['brand'] ?? '') . '/' . rawurlencode($vehicle['slug'] ?? '') . '.jpg'; ?>
    <div class="hero-media">
        <img src="<?= $heroImg ?>" alt="<?= e($service['title_fa'] ?? '') ?> <?= e($vehicle['name_fa'] ?? '') ?>" width="1200" height="675" loading="eager" fetchpriority="high" onerror="this.style.display='none'" title="<?= e($service['title_fa'] ?? '') ?> <?= e($vehicle['name_fa'] ?? '') ?>">
    </div>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو سرویس</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/vehicles/<?= rawurlencode((string) ($vehicle['brand'] ?? '')) ?>/<?= rawurlencode((string) ($vehicle['slug'] ?? '')) ?>">مشاهده صفحه مدل</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <article class="detail-card">
            <h2>علائم نیاز به <?= e($service['title_fa'] ?? '') ?></h2>
            <ul>
                <li>در صورت مشاهده مشکل در عملکرد خودرو، ابتدا علائم و خطاهای اسکن شده با وضعیت تجربه‌ای راننده تطبیق داده می‌شود.</li>
                <li>در مدل <?= e($vehicle['name_fa'] ?? $vehicle['brand'] ?? '') ?>، شارژ باتری، عملکرد سنسورها و وضعیت سیستم خنک‌کننده از جمله مواردی هستند که باید بررسی شوند.</li>
                <li>اگر سرویس به‌طور مکرر تکرار شود، انجام بررسی دقیق‌تر و تست‌های جانبی برای تشخیص ریشهٔ مشکل ضروری است.</li>
                <li>در خودروهای رایج بازار ایران، کیفیت روغن، فیلترها و وضعیت قطعات مصرفی روی عملکرد نهایی اثر مستقیم دارند.</li>
            </ul>
        </article>
        <article class="detail-card">
            <h2>مراحل انجام کار</h2>
            <ol>
                <li>بررسی اولیه علائم و تاریخچه سرویس خودرو.</li>
                <li>دیاگ و بررسی سیستم‌های مرتبط با خدمت انتخابی.</li>
                <li>تست قطعات، سنسورها و عملکرد فنی خودرو.</li>
                <li>تشخیص نهایی و اعلام دقیق وضعیت به مالک خودرو.</li>
                <li>انجام تعمیر، تنظیم یا تعویض قطعات مورد نیاز.</li>
                <li>بازبینی نهایی و کنترل عملکرد پس از تعمیر.</li>
            </ol>
        </article>
    </div>
</section>

<?php $midImg = SITE_URL . '/uploads/services/' . rawurlencode($service['slug'] ?? '') . '/' . rawurlencode($service['slug'] ?? '') . '-mid.jpg'; ?>
<section class="section-shell"><div class="hero-media mid"><img src="<?= $midImg ?>" alt="نمای سرویس <?= e($service['title_fa'] ?? '') ?>" width="800" height="600" loading="lazy" onerror="this.style.display='none'" title="<?= e($service['title_fa'] ?? '') ?>"></div></section>

<?php $serviceSubcategories = $subcategories ?? []; ?>
<section class="section-shell">
    <div class="section-heading">
        <h2>زیرخدمت‌های مرتبط با <?= e($service['title_fa'] ?? $service['title_en'] ?? 'خدمت') ?> برای <?= e($vehicle['name_fa'] ?? $vehicle['brand'] ?? '') ?></h2>
    </div>
    <?php if (!empty($serviceSubcategories)): ?>
        <div class="service-grid">
            <?php foreach (array_slice($serviceSubcategories, 0, 6) as $sub): ?>
                <a class="service-card" href="<?= SITE_URL ?>/services/<?= rawurlencode((string) ($service['slug'] ?? '')) ?>/<?= rawurlencode((string) ($sub['slug'] ?? '')) ?>">
                    <span class="meta-pill">زیرخدمت</span>
                    <h3><?= e($sub['title_fa'] ?? ($sub['title_en'] ?? 'زیرخدمت')) ?></h3>
                    <p><?= e($sub['intro'] ?? '') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="detail-card">
            <p>برای این سرویس، زیرخدمت‌های تخصصی در بانک محتوا ثبت شده‌اند و در مسیرهای بعدی تکمیل می‌شوند.</p>
        </div>
    <?php endif; ?>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>نکات فنی در مدل <?= e($vehicle['name_fa'] ?? $vehicle['brand'] ?? '') ?></h2>
    </div>
    <div class="service-grid">
        <article class="service-card">
            <h3>بررسی دقیق‌تر در سرویس</h3>
            <p>برای این مدل، حفظ سلامت سیستم‌های جانبی و وضعیت مصرف قطعات همواره در نتیجه نهایی تعمیر اثرگذار است.</p>
        </article>
        <article class="service-card">
            <h3>اهمیت سرویس به‌موقع</h3>
            <p>رسیدگی زودهنگام به نشانه‌های جزئی مانع از گسترش خرابی و کاهش هزینه‌های تعمیر در آینده می‌شود.</p>
        </article>
        <article class="service-card">
            <h3>تست عملکرد پس از کار</h3>
            <p>بازبینی پس از تعمیر برای اطمینان از عملکرد درست و کاهش احتمال بازگشت مشکل ضروری است.</p>
        </article>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="detail-grid">
        <article class="detail-card">
            <h3>آیا این خدمت برای <?= e($vehicle['name_fa'] ?? $vehicle['brand'] ?? '') ?> هم کاربرد دارد؟</h3>
            <p>بله، در صورت نیاز به بررسی فنی، سیستم‌های مرتبط و سرویس دوره‌ای، این سرویس برای خودروهای مشابه این مدل هم اجرا می‌شود.</p>
        </article>
        <article class="detail-card">
            <h3>قبل از سرویس چه اطلاعاتی لازم است؟</h3>
            <p>نوع خودرو، سابقه خرابی، زمان آخرین سرویس و علائم فعلی از مهم‌ترین اطلاعات برای تصمیم‌گیری دقیق است.</p>
        </article>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>خدمات مرتبط در همین مدل</h2>
    </div>
    <div class="resource-links">
        <?php foreach (array_slice($relatedServices ?? [], 0, 4) as $related): ?>
            <a href="<?= SITE_URL ?>/services/<?= rawurlencode((string) ($related['slug'] ?? '')) ?>"><?= e($related['title_fa'] ?? '') ?></a>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
