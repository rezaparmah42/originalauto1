<?php
$title = 'تعمیر جلوبندی و تعلیق خودرو | ' . SITE_NAME;
$description = 'تعمیر جلوبندی و تعلیق خودرو؛ بررسی سیبک، کمک‌فنر، طبق، بوش و تنظیم زوایا برای فرمان بهتر و آرامش بیشتر.';
$canonical = SITE_URL . '/services/suspension';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'تعلیق و جلوبندی', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">تعلیق و جلوبندی</div>
    <h1>تعمیر جلوبندی و تعلیق خودرو</h1>
    <p>سیستم تعلیق و جلوبندی نقش مهمی در راحتی سرنشینان و ایمنی خودرو دارد. اگر در عبور از دستانداز، ترمز یا پیچ‌ها لرزش، لغزش یا فرمان‌پذیری نامنظم احساس می‌کنید، بهتر است وضعیت تعلیق خودرو بررسی شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعلیق</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/brakes">بررسی ترمز</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>نشانه‌های خرابی</h2>
            <ul>
                <li>لرزش بیش از حد در سر پیچ‌ها یا هنگام عبور از دستانداز</li>
                <li>صدای تقتق یا کوبیدن از قسمت چرخ‌ها</li>
                <li>لغزش یا انحراف خودرو هنگام ترمزگیری</li>
                <li>فرسودگی یا ترک خوردگی در کمک‌فنر یا بوش‌ها</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>قطعات اصلی</h2>
            <ul>
                <li>سیبک، طبق، بوش و کمک‌فنر</li>
                <li>میل‌گاردان و قطعات فرمان</li>
                <li>بلبرینگ و مجموعه‌های تعلیق</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>مراحل بررسی و تعمیر</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>۱) ارزیابی وضعیت خودرو</h3>
            <p>اولین قدم، بررسی میزان فرسودگی قطعات و تست عملکرد سیستم در مسیرهای مختلف است.</p>
        </div>
        <div class="faq-item">
            <h3>۲) بررسی قطعات اصلی</h3>
            <p>سیبک‌ها، بوش‌ها، کمک‌فنرها و سنسورها با دقت بررسی می‌شوند تا علت اصلی خرابی مشخص شود.</p>
        </div>
        <div class="faq-item">
            <h3>۳) تعویض یا تنظیم مناسب</h3>
            <p>در صورت نیاز، قطعات فرسوده تعویض شده و در صورت لزوم، تنظیم زاویه و وضعیت چرخ‌ها انجام می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۴) تست نهایی</h3>
            <p>پس از تعمیر، عملکرد خودرو در پیچ، دستانداز و ترمزگیری مجدداً کنترل می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا لرزش خودرو همیشه به تعلیق مربوط است؟</h3>
            <p>ممکن است هم‌زمان با تعلیق، وضعیت چرخ‌ها، ترمز یا فرمان هم درگیر باشد؛ بنابراین بررسی کامل لازم است.</p>
        </div>
        <div class="faq-item">
            <h3>بعد از تعویض قطعات تعلیق، نیاز به تنظیم زوایا داریم؟</h3>
            <p>بله، در بسیاری از موارد تنظیم زوایا برای حفظ ایمنی، کاهش سایش تایر و بهتر شدن فرمان ضروری است.</p>
        </div>
    </div>
</section>

<section class="cta">
    <h2>اگر خودرو در پیچ‌ها یا هنگام عبور از دستانداز ناپایدار است، تعلیق را بررسی کنید</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست رزرو تعلیق</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>