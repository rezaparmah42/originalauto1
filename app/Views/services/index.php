<?php
$serviceModel = new App\Models\Service();
$services = $serviceModel->getVisibleServices();
$title = 'خدمات تعمیرگاه اورجینال شرق | ' . SITE_NAME;
$description = 'خدمات تخصصی تعمیر خودرو در اورجینال شرق؛ دیاگ، برق، موتور، گیربکس و سرویس دوره‌ای.';
$canonical = SITE_URL . '/services';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">خدمات حرفه‌ای تعمیرگاه</div>
    <h1>از عیب‌یابی دقیق تا تعمیر تخصصی، همه خدمات خودرو در یکجا</h1>
    <p>در اورجینال شرق، خدمات دیاگ، برق، موتور، گیربکس و سرویس دوره‌ای را با تجهیزات روز و رویکرد تخصصی برای خودروهای ایرانی و وارداتی ارائه می‌دهیم.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو وقت</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/diagnostic">دیاگ آنلاین</a>
    </div>
</section>

<section class="section-shell">
    <div class="section-intro">
        <div class="intro-card">
            <h2>چرا مشتریان ما به خدمات ما اعتماد می‌کنند؟</h2>
            <p>ما روی تشخیص دقیق، کیفیت اجرا و شفافیت در گزارش خدمات تمرکز می‌کنیم تا خودرو شما با اطمینان و سرعت بیشتر به مسیر سالم بازگردد.</p>
        </div>
        <div class="info-stack">
            <div class="info-card">
                <strong>تشخیص تخصصی</strong>
                <span>ارائه گزارش دقیق از وضعیت خودرو و مسیر تعمیر قبل از شروع کار.</span>
            </div>
            <div class="info-card">
                <strong>ضمانت کیفیت</strong>
                <span>اجرای خدمات با دقت فنی بالا و پشتیبانی پس از تعمیر.</span>
            </div>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>خدمات تخصصی ما</h2>
        <p>از سرویس دوره‌ای گرفته تا تعمیرات تخصصی، برای هر نیاز خودرو یک مسیر مشخص داریم.</p>
    </div>
    <div class="service-grid">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <?php
                    $slug = $service['slug'] ?? 'services';
                    $link = SITE_URL . '/services/' . rawurlencode($slug);
                    $isFeatured = ($slug === 'car-restoration');
                    $cardClass = $isFeatured ? 'service-card featured-service' : 'service-card';
                ?>
                <a class="<?= e($cardClass) ?>" href="<?= e($link) ?>">
                    <span class="meta-pill">خدمت تخصصی</span>
                    <?php if ($isFeatured): ?>
                        <span class="featured-badge">خدمت ویژه</span>
                    <?php endif; ?>
                    <h3><?= e($service['title_fa'] ?? ($service['title_en'] ?? 'خدمات تخصصی')) ?></h3>
                    <p><?= e($service['description_fa'] ?? ($service['description_en'] ?? 'خدمات تخصصی تعمیرگاه در اختیار شماست.')) ?></p>
                    <div class="service-meta">
                        <?php if (!empty($service['duration'])): ?>
                            <span><?= e($service['duration']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($service['price'])): ?>
                            <span>هزینه تقریبی: <?= e($service['price']) ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <a class="service-card" href="<?= SITE_URL ?>/services/diagnostic">
                <span class="meta-pill">دیاگ تخصصی</span>
                <h3>دیاگ و عیب‌یابی</h3>
                <p>ارائه گزارش دقیق و تشخیص سریع خطاهای موتور، برق و ECU.</p>
            </a>
            <a class="service-card" href="<?= SITE_URL ?>/services/gearbox">
                <span class="meta-pill">گیربکس</span>
                <h3>تعمیر گیربکس</h3>
                <p>بازرسی، سرویس و تعمیر گیربکس اتوماتیک با دقت بالا.</p>
            </a>
            <a class="service-card" href="<?= SITE_URL ?>/services/maintenance">
                <span class="meta-pill">سرویس دوره‌ای</span>
                <h3>سرویس دوره‌ای</h3>
                <p>نگهداری منظم برای افزایش عمر خودرو و کاهش خرابی‌های آینده.</p>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="section-shell">
    <div class="feature-strip">
        <div>
            <strong>پشتیبانی از خودروهای داخلی و وارداتی</strong>
            <span>تعمیر و سرویس برای برندهای پرکاربرد و خودروهای مدرن.</span>
        </div>
        <div>
            <strong>ارسال گزارش مرحله‌ای</strong>
            <span>شما در جریان روند تشخیص و تعمیر خودرو قرار می‌گیرید.</span>
        </div>
        <div>
            <strong>رزرو سریع و آسان</strong>
            <span>برای مراجعه حضوری یا مشاوره تلفنی، وقت خود را از همین صفحه رزرو کنید.</span>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>پیشنهادهای مرتبط</h2>
        <p>برای حرکت سریع‌تر بین راهنمای خودرو، مقالات و خدمات تخصصی، از این لینک‌ها استفاده کنید.</p>
    </div>
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/vehicles">راهنمای خودروها</a>
        <a href="<?= SITE_URL ?>/articles">مقالات فنی و آموزشی</a>
        <a href="<?= SITE_URL ?>/services/diagnostic">دیاگ تخصصی</a>
        <a href="<?= SITE_URL ?>/services/electrical-repair">تعمیر برق خودرو</a>
    </div>
</section>

<section class="cta">
    <h2>برای دریافت مشاوره یا رزرو وقت آماده هستید؟</h2>
    <p>در کوتاه‌ترین زمان با ما هماهنگ کنید و برنامه تعمیر خودرو را ساده‌تر و مطمئن‌تر شروع کنید.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو آنلاین</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/diagnostic">مشاوره دیاگ</a>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>