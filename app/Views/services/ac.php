<?php
$title = 'سرویس کولر خودرو | ' . SITE_NAME;
$description = 'تعمیر و سرویس کولر خودرو؛ شارژ گاز، رفع نشتی و بررسی کمپرسور و سیستم تهویه مطبوع در اورجینال شرق.';
$canonical = SITE_URL . '/services/ac';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'کولر خودرو', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">سیستم تهویه مطبوع</div>
    <h1>سرویس و تعمیر کولر خودرو</h1>
    <p>در روزهای گرم، عملکرد درست کولر خودرو به‌صورت مستقیم روی راحتی رانندگی و تمرکز شما اثر دارد. درصورت ضعیف شدن سرمایش، بوی نامطبوع، افزایش فشار کمپرسور یا عدم عملکرد فن، بررسی سیستم کولر لازم است.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو سرویس کولر</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ و بررسی عملکرد</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>علائم خرابی کولر</h2>
            <ul>
                <li>سرمایش ضعیف یا طولانی شدن زمان سرمایش</li>
                <li>بوی نامطبوع از دریچه‌ها</li>
                <li>گاز فشرده یا نشت گاز در سیستم</li>
                <li>کارکرد نامنظم فن یا کمپرسور</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>مواردی که بررسی می‌شود</h2>
            <ul>
                <li>سطح گاز و فشار سیستم</li>
                <li>کمپرسور، کندانسور و شیرها</li>
                <li>نشتی در لوله‌ها و اتصالات</li>
                <li>بررسی فن، رله و مدار کولر</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>خدمات ما برای سیستم تهویه</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>۱) تشخیص اولیه مشکل</h3>
            <p>با بررسی عملکرد کولر، فشار گاز و عملکرد کمپرسور، علت اصلی مشکل مشخص می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۲) شارژ یا تعمیر سیستم</h3>
            <p>در صورت نیاز، گاز مجدداً شارژ می‌شود یا قطعه معیوب تعمیر و در برخی موارد تعویض می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۳) تست نهایی و تحویل</h3>
            <p>پس از تعمیر، سیستم تهویه با تست عملکرد در حالت‌های مختلف بررسی و رویه اطمینان‌ساز انجام می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا فقط شارژ گاز کولر کافی است؟</h3>
            <p>خیر؛ در صورت وجود نشتی یا خرابی کمپرسور، شارژ مجدد فقط مشکل را موقتاً پنهان می‌کند.</p>
        </div>
        <div class="faq-item">
            <h3>اگر کولر خودرو در حالت خاموش هم ضعیف است، چه کاری انجام می‌دهیم؟</h3>
            <p>این حالت معمولاً به نشتی، فشار نامناسب یا خرابی قطعه اصلی مربوط می‌شود و باید با بررسی فنی دقیق بررسی شود.</p>
        </div>
    </div>
</section>

<section class="cta">
    <h2>اگر سرمایش خودرو به‌درستی انجام نمی‌شود، سیستم کولر را بررسی کنید</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست رزرو کولر</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>