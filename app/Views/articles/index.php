<?php
$articleModel = new App\Models\Article();
$articles = $articleModel->getVisibleArticles();
$categories = $articleModel->getVisibleCategories();
$title = 'مقالات فنی تعمیر خودرو | ' . SITE_NAME;
$description = 'مقالات تخصصی تعمیر خودرو، دیاگ، برق و نگهداری با محتوای کاربردی و سئو شده در اورجینال شرق.';
$canonical = SITE_URL . '/articles';
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">مرجع آموزشی تعمیر خودرو</div>
    <h1>مقالات کاربردی برای درک بهتر وضعیت خودرو و تصمیم‌گیری هوشمندانه</h1>
    <p>این مقالات بر پایه تجربه کارگاهی و نکات فنی واقعی نوشته شده‌اند تا مالکان خودرو و علاقمندان به تعمیر، به‌راحتی با علائم خرابی و روش‌های پیشگیری آشنا شوند.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">مشاوره با تیم فنی</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>موضوعات پرطرفدار</h2>
        <p>برای دسترسی سریع به موضوعات مورد علاقه خود، موضوعات مقاله را از این بخش انتخاب کنید.</p>
    </div>
    <div class="categories-list">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $categoryItem): ?>
                <a class="category-pill" href="<?= SITE_URL ?>/articles/category/<?= rawurlencode($categoryItem) ?>"><?= e($categoryItem) ?></a>
            <?php endforeach; ?>
        <?php else: ?>
            <a class="category-pill" href="<?= SITE_URL ?>/articles/category/engine">موتور خودرو</a>
            <a class="category-pill" href="<?= SITE_URL ?>/articles/category/electrical">برق خودرو</a>
            <a class="category-pill" href="<?= SITE_URL ?>/articles/category/diagnostic">دیاگ و ECU</a>
            <a class="category-pill" href="<?= SITE_URL ?>/articles/category/gearbox">گیربکس</a>
        <?php endif; ?>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>موضوعات پیشنهادی</h2>
        <p>مقالات کاربردی ما برای کمک به تشخیص زودهنگام مشکلات و تصمیم‌گیری درست در نگهداری خودرو تهیه شده‌اند.</p>
    </div>
    <div class="resource-links">
        <a href="<?= SITE_URL ?>/articles/category/diagnostic">علت روشن شدن چراغ چک</a>
        <a href="<?= SITE_URL ?>/articles/category/electrical">مشکلات ECU</a>
        <a href="<?= SITE_URL ?>/articles/category/engine">خرابی دریچه گاز</a>
        <a href="<?= SITE_URL ?>/articles/category/gearbox">مشکلات گیربکس اتومات</a>
        <a href="<?= SITE_URL ?>/articles/category/maintenance">ضعف باتری و برق خودرو</a>
    </div>
</section>

<section class="section-shell">
    <div class="section-heading">
        <h2>آخرین مقالات</h2>
        <p>مقاله‌های تخصصی ما برای کمک به شناسایی سریع‌تر مشکلات و نگهداری بهتر خودرو تهیه شده‌اند.</p>
    </div>
    <div class="service-grid article-grid">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <article class="service-card article-card">
                    <span class="meta-pill"><?= e($article['category'] ?? 'عمومی') ?></span>
                    <h3>
                        <a href="<?= SITE_URL ?>/articles/<?= e($article['slug'] ?? '') ?>">
                            <?= e($article['title_fa'] ?? ($article['title_en'] ?? 'مقاله')) ?>
                        </a>
                    </h3>
                    <p><?= e(mb_substr(strip_tags($article['content_fa'] ?? $article['content_en'] ?? ''), 0, 160)) ?>...</p>
                    <a class="text-link" href="<?= SITE_URL ?>/articles/<?= e($article['slug'] ?? '') ?>">خواندن مقاله</a>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="info-card">
                <strong>در حال حاضر مقاله‌ای منتشر نشده است.</strong>
                <span>به‌زودی محتوای جدیدی برای شما آماده می‌شود.</span>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
