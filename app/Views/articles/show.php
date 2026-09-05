<?php
$articleModel = new App\Models\Article();
$article = $articleModel->findBySlug($slug ?? '');
if (!$article) {
    http_response_code(404);
    $title = 'مقاله یافت نشد | ' . SITE_NAME;
    $description = 'مقاله مورد نظر در دسترس نیست.';
    $canonical = SITE_URL . '/articles';
    $robots = 'noindex, follow';
    $image = null;
} else {
    $title = (!empty($article['seo_title_fa']) ? $article['seo_title_fa'] : ($article['title_fa'] ?? $article['title_en'] ?? 'مقاله')) . ' | ' . SITE_NAME;
    $description = !empty($article['seo_description_fa']) ? $article['seo_description_fa'] : (!empty($article['meta_description_fa']) ? $article['meta_description_fa'] : ($article['content_fa'] ?? ''));
    $canonical = SITE_URL . '/articles/' . rawurlencode($article['slug'] ?? '');
    $robots = 'index, follow';
    $image = !empty($article['image']) ? SITE_URL . '/uploads/' . ltrim($article['image'], '/') : null;
}
?>
<?php
// Breadcrumb for article
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'مقالات', 'url' => SITE_URL . '/articles'],
    ['name' => $article['title_fa'] ?? ($article['title_en'] ?? 'مقاله'), 'url' => $canonical],
];
require __DIR__.'/../layouts/header.php';
?>

<?php if (!$article): ?>
    <section class="section-hero">
        <div class="page-intro">
            <h1>مقاله یافت نشد</h1>
            <p>مقاله درخواستی در حال حاضر موجود نیست.</p>
        </div>
    </section>
<?php else: ?>
    <?php
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article['seo_title_fa'] ?: $article['title_fa'] ?: $article['title_en'] ?: '',
        'description' => $article['seo_description_fa'] ?: $article['meta_description_fa'] ?: strip_tags($article['content_fa'] ?? $article['content_en'] ?? ''),
        'author' => [
            '@type' => 'Person',
            'name' => $article['author'] ?: SITE_NAME,
        ],
        'datePublished' => date('c', strtotime($article['created_at'] ?? date('Y-m-d H:i:s'))),
        'url' => $canonical,
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $canonical,
        ],
        'publisher' => [
            '@type' => 'LocalBusiness',
            'name' => SITE_NAME,
            'url' => rtrim(SITE_URL, '/'),
            'telephone' => SITE_PHONE,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => SITE_ADDRESS,
            ],
        ],
    ];
    if (!empty($article['image'])) {
        $articleSchema['image'] = SITE_URL . '/uploads/' . ltrim($article['image'], '/');
    }
    ?>
    <?php
    // Set OG type and image for header meta
    $og_type = 'article';
    $image = !empty($article['image']) ? SITE_URL . '/uploads/' . ltrim($article['image'], '/') : null;
    // Use partial to render JSON-LD
    $schemaData = $articleSchema;
    require __DIR__ . '/../partials/schema.php';
    ?>

    <article style="max-width:900px; margin:2rem auto; padding:0 1rem 3rem;">
        <h1><?= e($article['title_fa'] ?? ($article['title_en'] ?? 'عنوان مقاله')) ?></h1>
        <p style="color:#64748b;"><?= e($article['category'] ?? 'عمومی') ?> • <?= e($article['author'] ?? 'اورجینال شرق') ?></p>
        <div style="line-height:2;">
            <?= nl2br(strip_tags($article['content_fa'] ?? ($article['content_en'] ?? ''), '<h2><h3><p><strong><ul><ol><li><br>')) ?>
        </div>
    </article>
    <?php
    $relatedArticles = array_values(array_filter((new App\Models\Article())->getVisibleArticles(), static function ($item) use ($article) {
        return ($item['slug'] ?? '') !== ($article['slug'] ?? '') && ($item['category'] ?? '') === ($article['category'] ?? '');
    }));
    ?>
    <?php if (!empty($relatedArticles)): ?>
        <section class="section-shell">
            <div class="section-heading"><h2>مقالات مرتبط</h2><p>مطالب تکمیلی برای مطالعه بیشتر.</p></div>
            <div class="service-grid article-grid">
                <?php foreach (array_slice($relatedArticles, 0, 3) as $related): ?><a class="service-card article-card" href="<?= SITE_URL ?>/articles/<?= rawurlencode($related['slug'] ?? '') ?>"><h3><?= e($related['title_fa'] ?? '') ?></h3><p><?= e(mb_substr(strip_tags($related['content_fa'] ?? ''), 0, 140)) ?>...</p></a><?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__.'/../layouts/footer.php'; ?>