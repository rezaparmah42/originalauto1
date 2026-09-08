<?php
$serviceModel = new App\Models\Service();
$services = $serviceModel->getVisibleServices();
$title = 'خدمات تخصصی تعمیر خودرو | اورجینال شرق';
$description = 'خدمات تخصصی تعمیرگاه اورجینال شرق: دیاگ خودرو، تعمیر برق، موتور، گیربکس، سرویس دوره‌ای و خدمات تخصصی خودروهای داخلی و وارداتی.';
$canonical = SITE_URL . '/services';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => 'خدمات تخصصی تعمیرگاه اورجینال شرق',
    'itemListOrder' => 'https://schema.org/ItemListOrderAscending',
    'itemListElement' => array_values(array_map(static function ($service) {
        return [
            '@type' => 'ListItem',
            'position' => 1,
            'url' => SITE_URL . '/services/' . rawurlencode((string) ($service['slug'] ?? '')),
            'name' => $service['title_fa'] ?? ($service['title_en'] ?? 'خدمت تخصصی'),
        ];
    }, array_slice($services, 0, 6))),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>

<section class="page-hero">
    <div class="hero-badge">خدمات حرفه‌ای تعمیرگاه</div>
    <h1>از عیب‌یابی دقیق تا تعمیر تخصصی، همه خدمات خودرو در یک مکان</h1>
    <p>در اورجینال شرق، خدمات دیاگ خودرو، تعمیر برق، موتور، گیربکس، سرویس دوره‌ای و نگهداری تخصصی برای خودروهای داخلی و وارداتی با تجهیزات حرفه‌ای و مشاوره فنی دقیق ارائه می‌شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو وقت</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/diagnostic">دیاگ آنلاین</a>
    </div>
</section>

<section class="section-shell">
    <div class="section-intro">
        <div class="intro-card">
            <h2>چرا خدمات اورجینال شرق انتخاب مناسبی است؟</h2>
            <p>ما بر تشخیص دقیق، کیفیت اجرا و شفافیت گزارش خدمات تمرکز می‌کنیم تا خودرو شما با اطمینان و سرعت بیشتری به وضعیت ایمن و قابل استفاده خود بازگردد.</p>
        </div>
        <div class="info-stack">
            <div class="info-card">
                <strong>تشخیص دقیق</strong>
                <span>بررسی واقعی مشکل و ارائه گزارش فنی قبل از هر تعمیر.</span>
            </div>
            <div class="info-card">
                <strong>کیفیت اجرا</strong>
                <span>استفاده از استانداردهای فنی برای تعمیرات تخصصی و پایدار.</span>
            </div>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>خدمات تخصصی ما</h2>
        <p>برای نیازهای مختلف خودرو، از سرویس دوره‌ای تا عیب‌یابی و تعمیرات تخصصی، مسیر درست را برای شما مشخص می‌کنیم.</p>
    </div>
    <div class="service-grid">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <?php
                    $slug = $service['slug'] ?? '';
                    $link = $slug !== '' ? SITE_URL . '/services/' . rawurlencode($slug) : SITE_URL . '/booking';
                    $isFeatured = in_array((string) $slug, ['diagnostic', 'automatic-transmission', 'engine-repair'], true);
                    $cardClass = $isFeatured ? 'service-card featured-service' : 'service-card';
                ?>
                <a class="<?= e($cardClass) ?>" href="<?= e($link) ?>">
                    <span class="meta-pill">خدمت تخصصی</span>
                    <?php if ($isFeatured): ?>
                        <span class="featured-badge">پیشنهاد ویژه</span>
                    <?php endif; ?>
                    <h3><?= e($service['title_fa'] ?? ($service['title_en'] ?? 'خدمات تخصصی')) ?></h3>
                    <p><?= e($service['description_fa'] ?? ($service['description_en'] ?? 'خدمات تخصصی تعمیرگاه برای نیازهای خودرو شما آماده است.')) ?></p>
                    <div class="service-meta">
                        <?php if (!empty($service['duration'])): ?>
                            <span><?= e($service['duration']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($service['price'])): ?>
                            <span><?= e($service['price']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <a class="service-card" href="<?= SITE_URL ?>/services/diagnostic">
                <span class="meta-pill">دیاگ تخصصی</span>
                <h3>دیاگ و عیب‌یابی</h3>
                <p>تشخیص دقیق خطاهای ECU، سنسورها و سیستم‌های الکترونیکی خودرو.</p>
            </a>
            <a class="service-card" href="<?= SITE_URL ?>/services/electrical-repair">
                <span class="meta-pill">برق خودرو</span>
                <h3>تعمیر برق خودرو</h3>
                <p>رفع مشکلات روشنایی، سنسورها، مدارها و عملکرد سیستم الکترونیکی.</p>
            </a>
            <a class="service-card" href="<?= SITE_URL ?>/services/engine-repair">
                <span class="meta-pill">موتور</span>
                <h3>تعمیر موتور</h3>
                <p>بررسی و تعمیر موتور، عملکرد، مصرف سوخت و سیستم‌های مرتبط.</p>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="section-shell">
    <div class="feature-strip">
        <div>
            <strong>پشتیبانی از خودروهای داخلی و وارداتی</strong>
            <span>تعمیر و سرویس برای برندهای مختلف با رویکرد تخصصی و دقیق.</span>
        </div>
        <div>
            <strong>گزارش فنی شفاف</strong>
            <span>قبل از هر تعمیر، وضعیت خودرو و مسیر پیشنهادی به‌صورت دقیق برای شما توضیح داده می‌شود.</span>
        </div>
        <div>
            <strong>رزرو سریع و راحت</strong>
            <span>از همین صفحه برای هماهنگی زمان مراجعه یا مشاوره اولیه اقدام کنید.</span>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>پیشنهادهای مرتبط</h2>
        <p>برای حرکت بهتر در مسیر اطلاعات فنی و خدمات خودرو، از این بخش‌ها استفاده کنید.</p>
    </div>
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/vehicles">کاتالوگ خودروها</a>
        <a href="<?= SITE_URL ?>/articles">مقالات فنی و آموزشی</a>
        <a href="<?= SITE_URL ?>/services/diagnostic">دیاگ تخصصی</a>
        <a href="<?= SITE_URL ?>/services/electrical-repair">تعمیر برق خودرو</a>
    </div>
</section>

<section class="cta">
    <h2>برای هماهنگی مشاوره یا رزرو زمان مراجعه آماده هستیم</h2>
    <p>در اورجینال شرق، تشخیص دقیق و تعمیر اصولی از همان ابتدا باعث کاهش هزینه و افزایش عمر خودرو می‌شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو آنلاین</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/diagnostic">مشاوره دیاگ</a>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>