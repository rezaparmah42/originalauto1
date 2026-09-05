<?php
$title = 'تعمیر برق خودرو | ' . SITE_NAME;
$description = 'تعمیر سیستم برق خودرو، سنسورها، ECU، باتری و روشنایی با خدمات تخصصی در اورجینال شرق.';
$canonical = SITE_URL . '/services/electrical-repair';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">تعمیر برق خودرو</div>
    <h1>رفع مشکلات برق، سنسورها و سیستم‌های الکترونیکی</h1>
    <p>مشکلات برق خودرو می‌توانند روی روشن شدن، استارت، چراغ‌ها، سیستم تهویه و حتی عملکرد موتور اثر بگذارند. این بخش درباره علائم، علت و روش تعمیر توضیح می‌دهد.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو تعمیر برق</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services/diagnostic">دیاگ پیشرفته</a>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>مشکل مشتری</h2>
            <p>شایع‌ترین مشکلات برق خودرو شامل روشن نشدن، استارت نخوردن، قطعی سیستم روشنایی، خرابی سنسورها و اختلال در عملکرد لوازم برقی است.</p>
        </div>
        <div class="detail-card">
            <h2>علائم شایع</h2>
            <ul>
                <li>قطعی یا نوسان برق</li>
                <li>خرابی چراغ‌ها و سیستم روشنایی</li>
                <li>استارت نخوردن یا خاموش شدن ناگهانی</li>
                <li>خطای سنسورها و عملکرد نامنظم</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="detail-grid">
        <div class="detail-card">
            <h2>شرح خدمات</h2>
            <p>در این خدمت، سیم‌کشی، اتصالات، قطعات برقی، سنسورها و ماژول‌های کنترل بررسی می‌شوند. در صورت نیاز، تعمیر، تعویض قطعه یا تنظیمات الکترونیکی انجام می‌شود.</p>
        </div>
        <div class="detail-card">
            <h2>خودروهای پشتیبانی‌شده</h2>
            <ul>
                <li>خودروهای داخلی و وارداتی</li>
                <li>خودروهای با سیستم‌های الکترونیکی پیشرفته</li>
                <li>خودروهای دارای سنسورهای متعدد و ماژول‌های کنترل</li>
            </ul>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>سوالات متداول</h2>
    </div>
    <div class="faq-list">
        <div class="faq-item">
            <h3>آیا مشکل برق همیشه به باتری برمی‌گردد؟</h3>
            <p>نه؛ گاهی مشکل از دینام، کلیدها، سیم‌کشی یا سنسورهای الکترونیکی است و بررسی تخصصی لازم است.</p>
        </div>
        <div class="faq-item">
            <h3>چرا روشن شدن چراغ‌ها با خرابی برقی همراه است؟</h3>
            <p>چون سیستم روشنایی به‌صورت مستقیم به برق و اتصالات خودرو وابسته است و هر خرابی در مسیر جریان می‌تواند روی آن اثر بگذارد.</p>
        </div>
    </div>
</section>

<section class="section-shell">
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/services/ecu-programming">ECU Programming</a>
        <a href="<?= SITE_URL ?>/articles">مقالات برق خودرو</a>
        <a href="<?= SITE_URL ?>/vehicles">دسته‌بندی خودروها</a>
    </div>
</section>

<section class="cta">
    <h2>مشکل برق خودرو را جدی بگیرید و سریع‌تر حل کنید</h2>
    <a class="btn-primary" href="<?= SITE_URL ?>/booking">درخواست تعمیر برق</a>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
