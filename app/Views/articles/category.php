<?php
$slug = $slug ?? '';
$articleModel = new App\Models\Article();
$articles = $articleModel->getVisibleArticlesByCategory($slug);
$title = 'مقالات ' . e($slug) . ' | ' . SITE_NAME;
$description = 'مقالات تخصصی و آموزشی مرتبط با ' . e($slug) . ' در اورجینال شرق.';
$canonical = SITE_URL . '/articles/category/' . rawurlencode($slug);
$robots = 'index, follow';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="section-hero">
    <div class="page-intro">
        <h1>دسته بندی مقالات</h1>
        <p>مقالات مرتبط با موضوع <?= e($slug) ?> برای کمک به تشخیص و نگهداری خودرو.</p>
    </div>
</section>

<section class="article-section">
    <div class="cards">
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $article): ?>
                <div style="border:1px solid #e5e7eb; border-radius:12px; padding:1rem; background:#fff;">
                    <h3 style="margin-top:0;">
                        <a href="<?= SITE_URL ?>/articles/<?= e($article['slug'] ?? '') ?>" style="color:inherit; text-decoration:none;">
                            <?= e($article['title_fa'] ?? ($article['title_en'] ?? 'مقاله')) ?>
                        </a>
                    </h3>
                    <p><?= e(strip_tags($article['content_fa'] ?? $article['content_en'] ?? '')) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div>مقاله‌ای در این دسته موجود نیست.</div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>