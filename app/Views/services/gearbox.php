<?php
$title = 'تعمیر گیربکس اتوماتیک | ' . SITE_NAME;
$description = 'عیب یابی و تعمیر تخصصی گیربکس اتوماتیک؛ رفع تقه، تاخیر تعویض دنده و خطاهای گیربکس در اورجینال شرق.';
$canonical = SITE_URL . '/services/gearbox';
$robots = 'index, follow';
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'خدمات', 'url' => SITE_URL . '/services'],
    ['name' => 'گیربکس', 'url' => $canonical],
];
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">گیربکس و انتقال قدرت</div>
    <h1>تعمیر تخصصی گیربکس اتوماتیک و دستی</h1>
    <p>در صورت تاخیر در تعویض دنده، لغزش، تقه شدید، صدای نامنظم یا کاهش عملکرد، بررسی گیربکس باید در اولین فرصت انجام شود. در اورجینال شرق، عیب‌یابی دقیق و تعمیر اصولی باعث جلوگیری از خرابی‌های سنگین‌تر می‌شود.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو سرویس گیربکس</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">پیگیری دیاگ خودرو</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>نشانه‌های خرابی گیربکس</h2>
            <ul>
                <li>تاخیر در تعویض دنده یا جابجایی نامنظم</li>
                <li>تقه، لرزش یا صدای غیرعادی در دنده‌گیری</li>
                <li>افت قدرت یا عملکرد ضعیف در شتاب</li>
                <li>روشن شدن چراغ گیربکس یا دریافت خطای سیستم</li>
            </ul>
        </div>
        <div class="detail-card">
            <h2>کارهایی که بررسی می‌شود</h2>
            <ul>
                <li>سطح و کیفیت روغن گیربکس</li>
                <li>بررسی سنسور و عملکرد فرمان الکترونیکی</li>
                <li>کالیبراسیون دنده‌ها و قطعات داخلی</li>
                <li>تشخیص نیاز به تعویض قطعه یا سرویس کامل</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>فرآیند انجام کار</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>۱) بررسی اولیه و شنیدن مشکل</h3>
            <p>در اولین مرحله، رفتار خودرو در حالت‌های مختلف مانند ترمز، شتاب‌گیری و دنده‌گیری بررسی می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۲) دیاگ و تست عملکرد</h3>
            <p>با اسکن سیستم گیربکس و بررسی فشار و روغن، علت اصلی خرابی شناسایی می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۳) تعمیر یا سرویس تخصصی</h3>
            <p>در صورت لازم، تعویض ATF، تعویض کیت کلاچ، سرویس قطعات یا بازسازی گیربکس انجام می‌شود.</p>
        </div>
        <div class="faq-item">
            <h3>۴) تست نهایی و تحویل</h3>
            <p>پس از تعمیر، عملکرد دنده‌ها، لغزش و صدای گیربکس با تست رانندگی مجدد تأیید می‌شود.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا مشکل گیربکس همیشه به تعویض کامل نیاز دارد؟</h3>
            <p>نه، بسیاری از مشکلات با سرویس روغن، بررسی سنسورها، تعویض کلاچ یا تعمیر قطعات داخلی قابل حل‌اند.</p>
        </div>
        <div class="faq-item">
            <h3>اگر گیربکس در دنده‌ها تقه می‌گیرد چه باید کرد؟</h3>
            <p>در این حالت، بررسی سریع سیستم گیربکس و سطح روغن لازم است تا خسارت بیشتر به قطعات داخلی وارد نشود.</p>
        </div>
    </div>
</section>

<section class="cta">
    <h2>اگر خودرو در دنده‌گیری با تأخیر یا تقه همراه است، همین حالا بررسی کنید</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست رزرو گیربکس</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
