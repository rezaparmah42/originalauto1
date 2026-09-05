<?php
$title = $title ?? ('تماس با اورجینال شرق | ' . SITE_NAME);
$description = $description ?? 'راه‌های تماس با تعمیرگاه اورجینال شرق برای مشاوره، رزرو و پیگیری خدمات خودرو.';
$canonical = $canonical ?? (SITE_URL . '/contact');
$robots = $robots ?? 'index, follow';
// Breadcrumb for contact
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'تماس با ما', 'url' => $canonical],
];
require __DIR__ . '/../layouts/header.php';
?>
<section class="page-hero">
    <div class="hero-badge">در ارتباط با تیم پذیرش</div>
    <h1>برای مشاوره، رزرو و پیگیری خدمات با ما در تماس باشید</h1>
    <p>شرح کوتاهی از مشکل خودرو یا زمان پیشنهادی مراجعه را ارسال کنید؛ تیم پذیرش برای هماهنگی دقیق‌تر با شما تماس می‌گیرد.</p>
    <div class="hero-actions"><a class="btn-primary" href="<?= SITE_URL ?>/booking">رزرو آنلاین</a><a class="btn-outline" href="tel:<?= e(SITE_PHONE) ?>">تماس با تعمیرگاه</a></div>
</section>
<section class="section-shell">
    <div class="detail-grid">
        <article class="detail-card"><h2>اطلاعات تماس</h2><p><strong>تلفن:</strong> <a href="tel:<?= e(SITE_PHONE) ?>"><?= e(SITE_PHONE) ?></a></p><p><strong>آدرس:</strong> <?= e(SITE_ADDRESS) ?></p></article>
        <article class="detail-card"><h2>ساعات پاسخ‌گویی</h2><p>هر روز از ساعت ۸:۰۰ تا ۲۰:۰۰ برای هماهنگی خدمات، مشاوره اولیه و پیگیری درخواست‌ها پاسخ‌گو هستیم.</p><a class="text-link" href="<?= SITE_URL ?>/services">انتخاب خدمت موردنظر</a></article>
    </div>
</section>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
