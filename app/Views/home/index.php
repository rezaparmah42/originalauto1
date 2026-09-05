<?php
// Breadcrumb for home
$breadcrumb = [ ['name' => 'خانه', 'url' => SITE_URL] ];
require __DIR__.'/../layouts/header.php'; ?>

<section class="hero-professional">
    <div class="hero-content">
        <div class="hero-badge">تعمیرگاه تخصصی خودرو | اورجینال شرق</div>
        <h1>از عیب‌یابی دقیق تا تعمیر حرفه‌ای، تجربه‌ای مطمئن برای خودروی شما</h1>
        <p>در اورجینال شرق، خدمات دیاگ، برق، موتور، گیربکس و سرویس دوره‌ای را با تجهیزات روز و تیم فنی متخصص در یک فضای مدرن ارائه می‌دهیم.</p>

        <div class="hero-actions">
            <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعمیرگاه</a>
            <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
        </div>

        <div class="hero-features">
            <div class="hero-feature">
                <strong>۱۰+ سال</strong>
                تجربه در تعمیرات تخصصی
            </div>
            <div class="hero-feature">
                <strong>۹۸٪</strong>
                رضایت مشتریان
            </div>
            <div class="hero-feature">
                <strong>۲۴/۷</strong>
                پشتیبانی و مشاوره آنلاین
            </div>
        </div>
    </div>
</section>

<?php if (!defined('TEMP_HIDE_HOMEPAGE_PRODUCTS') || !TEMP_HIDE_HOMEPAGE_PRODUCTS): ?>
<section class="home-section products-preview">
    <div class="section-heading">
        <h2>قطعات و محصولات پیشنهادی</h2>
        <p>قطعات منتخب را با وضعیت موجودی و اطلاعات کامل بررسی کنید.</p>
    </div>
    <div class="cards product-cards">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <?php if (!empty($product['image'])): ?><img src="<?= SITE_URL ?>/uploads/<?= e($product['image']) ?>" alt="<?= e($product['title_fa'] ?? '') ?>" loading="lazy"><?php endif; ?>
                    <h3><?= e($product['title_fa'] ?? $product['title_en'] ?? 'محصول') ?></h3>
                    <p><?= e(mb_substr(strip_tags($product['description_fa'] ?? ''), 0, 100)) ?></p>
                    <div class="service-meta"><span><?= (int) ($product['stock'] ?? 0) > 0 ? 'موجود' : 'ناموجود' ?></span><span><?= e($product['price'] ?? '-') ?></span></div>
                    <a class="btn-outline" href="<?= SITE_URL ?>/products/<?= rawurlencode($product['slug'] ?? '') ?>">مشاهده محصول</a>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="info-card"><strong>محصولی برای نمایش ثبت نشده است.</strong><span>برای استعلام قطعه با کارشناسان ما تماس بگیرید.</span></div>
        <?php endif; ?>
    </div>
    <a class="text-link" href="<?= SITE_URL ?>/shop">مشاهده همه محصولات</a>
</section>
<?php endif; ?>

<section class="home-section faq-section">
    <div class="section-heading"><h2>پرسش‌های متداول</h2><p>پاسخ کوتاه به پرسش‌های رایج پیش از مراجعه.</p></div>
    <div class="faq-list">
        <details><summary>چطور برای تعمیر خودرو وقت بگیرم؟</summary><p>از فرم رزرو آنلاین استفاده کنید تا تیم پذیرش زمان مناسب را با شما هماهنگ کند.</p></details>
        <details><summary>آیا پیش از تعمیر، هزینه اعلام می‌شود؟</summary><p>پس از بررسی اولیه و تشخیص، مسیر پیشنهادی و برآورد هزینه با شما در میان گذاشته می‌شود.</p></details>
        <details><summary>برای خودروهای مختلف خدمات ارائه می‌شود؟</summary><p>بله، تیم فنی ما خودروهای داخلی و وارداتی را با تجهیزات تخصصی بررسی می‌کند.</p></details>
    </div>
</section>

<section class="home-section home-intro">
    <div class="intro-card">
        <h2>چرا مشتریان ما به ما اعتماد می‌کنند؟</h2>
        <p>ما در اورجینال شرق روی دقت تشخیص، کیفیت اجرا و شفافیت در خدمات تمرکز می‌کنیم تا خودروی شما با اطمینان بیشتری به مسیر سالم بازگردد.</p>
    </div>
    <div class="intro-grid">
        <div class="info-card">
            <strong>تشخیص دقیق</strong>
            <span>دیاگ و عیب‌یابی با تجهیزات حرفه‌ای برای شناسایی سریع مشکل</span>
        </div>
        <div class="info-card">
            <strong>تعمیر مطمئن</strong>
            <span>اجرای دقیق خدمات موتور، برق و گیربکس با ضمانت کیفیت کار</span>
        </div>
        <div class="info-card">
            <strong>پشتیبانی مستمر</strong>
            <span>مشاوره قبل و بعد از تعمیر برای حفظ عملکرد خودرو در بلندمدت</span>
        </div>
    </div>
</section>

<section class="trust-section">
    <div class="trust-card">
        <strong>۱۲+</strong>
        <span>سال تجربه تعمیر تخصصی خودرو</span>
    </div>
    <div class="trust-card">
        <strong>۸۵۰۰+</strong>
        <span>خودرو بررسی و تعمیر شده</span>
    </div>
    <div class="trust-card">
        <strong>ECU</strong>
        <span>تخصص در عیب‌یابی و برنامه‌ریزی ECU</span>
    </div>
    <div class="trust-card">
        <strong>ضمانت</strong>
        <span>گارانتی خدمات و پشتیبانی پس از تعمیر</span>
    </div>
</section>

<section class="home-section services-home">
    <div class="section-heading">
        <h2>خدمات تخصصی ما</h2>
        <p>از سرویس دوره‌ای تا تعمیرات تخصصی، همه‌چیز در یک مجموعه برای خودرو شما آماده است.</p>
    </div>
    <div class="cards service-cards">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <?php $slug = $service['slug'] ?? 'services'; $link = SITE_URL . '/services/' . rawurlencode($slug); ?>
                <a class="service-card" href="<?= e($link) ?>">
                    <div class="service-card-icon">⚙️</div>
                    <h3><?= e($service['title_fa'] ?? ($service['title_en'] ?? 'خدمت تخصصی')) ?></h3>
                    <p><?= e($service['description_fa'] ?? ($service['description_en'] ?? 'خدمات تخصصی خودرو در اختیار شماست.')) ?></p>
                    <span>مشاهده جزئیات</span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <a class="service-card" href="<?= SITE_URL ?>/services/diagnostic">
                <div class="service-card-icon">🔧</div>
                <h3>دیاگ تخصصی</h3>
                <p>تشخیص دقیق خطاهای خودرو با تجهیزات پیشرفته و گزارش کامل.</p>
                <span>مشاهده جزئیات</span>
            </a>
            <a class="service-card" href="<?= SITE_URL ?>/services/electrical">
                <div class="service-card-icon">⚡</div>
                <h3>برق خودرو</h3>
                <p>رفع مشکلات سیستم برق، ECU، سنسورها و لوازم برقی خودرو.</p>
                <span>مشاهده جزئیات</span>
            </a>
            <a class="service-card" href="<?= SITE_URL ?>/services/gearbox">
                <div class="service-card-icon">⚙️</div>
                <h3>گیربکس</h3>
                <p>تعمیر و سرویس گیربکس اتوماتیک با رویکرد تخصصی.</p>
                <span>مشاهده جزئیات</span>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="home-section brands">
    <div class="section-heading">
        <h2>خودروهای تحت پوشش</h2>
        <p>پشتیبانی از برندهای داخلی و وارداتی با رویکرد تخصصی و دقیق</p>
    </div>
    <div class="brands-grid">
        <a class="brand-card" href="<?= SITE_URL ?>/brands/iranian">
            <strong>داخلی</strong>
            <span>ایران خودرو، سایپا و دیگر داخلی‌ها</span>
        </a>
        <a class="brand-card" href="<?= SITE_URL ?>/brands/chinese">
            <strong>چینی</strong>
            <span>چری، ام‌وی‌ام، جک و فونیکس</span>
        </a>
        <a class="brand-card" href="<?= SITE_URL ?>/brands/korean">
            <strong>کره‌ای</strong>
            <span>کیا، هیوندای و دیگر کره‌ای‌ها</span>
        </a>
        <a class="brand-card" href="<?= SITE_URL ?>/brands/japanese">
            <strong>ژاپنی</strong>
            <span>تویوتا، نیسان، لکسوس و ژاپنی‌ها</span>
        </a>
        <a class="brand-card" href="<?= SITE_URL ?>/brands/german">
            <strong>آلمانی</strong>
            <span>بنز، بی‌ام‌و و خودروهای آلمانی</span>
        </a>
        <a class="brand-card" href="<?= SITE_URL ?>/brands/other">
            <strong>سایر</strong>
            <span>سایر برندهای حاضر در ایران</span>
        </a>
    </div>
</section>

<section class="home-section articles-preview">
    <div class="section-heading">
        <h2>آخرین مقالات تعمیراتی</h2>
        <p>نکات کاربردی برای شناسایی زودهنگام خرابی‌ها و نگهداری بهتر خودرو</p>
    </div>
    <div class="cards article-cards">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <a class="article-card" href="<?= SITE_URL ?>/articles/<?= rawurlencode($article['slug'] ?? '') ?>">
                    <h3><?= e($article['title_fa'] ?? ($article['title_en'] ?? 'مقاله تخصصی')) ?></h3>
                    <p><?= e(mb_substr(strip_tags($article['content_fa'] ?? ($article['content_en'] ?? '')), 0, 140)) ?>...</p>
                    <span>خواندن مقاله</span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="article-card">
                <h3>علائم خرابی گیربکس</h3>
                <p>در صورت مشاهده تاخیر در تعویض دنده یا صدای غیرعادی، بهتر است بررسی تخصصی انجام شود.</p>
                <span>مقاله راهنما</span>
            </div>
            <div class="article-card">
                <h3>دلایل روشن شدن چراغ چک</h3>
                <p>چراغ چک می‌تواند نشانه‌ی مشکلات متعددی از سنسور تا سیستم احتراق باشد.</p>
                <span>مقاله راهنما</span>
            </div>
            <div class="article-card">
                <h3>زمان تعویض روغن موتور</h3>
                <p>تعویض به‌موقع روغن موتور، یکی از مهم‌ترین اقدامات برای حفظ عمر موتور است.</p>
                <span>مقاله راهنما</span>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="cta">
    <h2>خودروی شما نیاز به رسیدگی فوری دارد؟</h2>
    <p>برای رزرو وقت، مشاوره تخصصی یا ارسال درخواست تعمیر، همین حالا اقدام کنید.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست تعمیر</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/booking">تماس با ما</a>
    </div>
</section>

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type' => 'Question', 'name' => 'چطور برای تعمیر خودرو وقت بگیرم؟', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'از فرم رزرو آنلاین استفاده کنید تا تیم پذیرش زمان مناسب را هماهنگ کند.']],
        ['@type' => 'Question', 'name' => 'آیا پیش از تعمیر هزینه اعلام می‌شود؟', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'پس از بررسی اولیه و تشخیص، مسیر پیشنهادی و برآورد هزینه اعلام می‌شود.']],
        ['@type' => 'Question', 'name' => 'برای خودروهای مختلف خدمات ارائه می‌شود؟', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'بله، خودروهای داخلی و وارداتی با تجهیزات تخصصی بررسی می‌شوند.']],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<?php require __DIR__.'/../layouts/footer.php'; ?>
