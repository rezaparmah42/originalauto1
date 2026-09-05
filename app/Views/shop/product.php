<?php
$product = $product ?? null;
if ($product) {
    $title = (!empty($product['seo_title_fa']) ? $product['seo_title_fa'] : ($product['title_fa'] ?? $product['title_en'] ?? 'محصول')) . ' | ' . SITE_NAME;
    $description = !empty($product['seo_description_fa']) ? $product['seo_description_fa'] : ($product['description_fa'] ?? $product['description_en'] ?? '');
    $canonical = SITE_URL . '/products/' . rawurlencode($product['slug'] ?? '');
    $robots = 'index, follow';
} else {
    http_response_code(404);
    $title = 'محصول یافت نشد | ' . SITE_NAME;
    $description = 'محصول درخواستی در دسترس نیست.';
    $canonical = SITE_URL . '/shop';
    $robots = 'noindex, follow';
    $image = null;
}
?>
<?php
// Breadcrumb for product
$breadcrumb = [
    ['name' => 'خانه', 'url' => SITE_URL],
    ['name' => 'فروشگاه', 'url' => SITE_URL . '/shop'],
    ['name' => $product['title_fa'] ?? ($product['title_en'] ?? 'محصول'), 'url' => $canonical],
];
require __DIR__.'/../layouts/header.php'; ?>

<?php if (!$product): ?>
    <section class="section-hero">
        <div class="page-intro">
            <h1>محصول یافت نشد</h1>
            <p>محصول مورد نظر در حال حاضر موجود نیست.</p>
        </div>
    </section>
<?php else: ?>
    <section class="section-hero">
        <div class="page-intro">
            <h1><?= e($product['title_fa'] ?? ($product['title_en'] ?? 'محصول')) ?></h1>
            <p><?= e($product['description_fa'] ?? ($product['description_en'] ?? '')) ?></p>
        </div>
    </section>

    <section class="product-detail">
        <div class="detail-grid">
            <div class="gallery">
                <?php $productModel = new \App\Models\Product();
                $images = $productModel->getProductImages($product['id'] ?? 0); ?>
                <?php if (!empty($images)): ?>
                    <div class="gallery-main">
                        <img id="main-image" src="<?= SITE_URL ?>/uploads/<?= e($images[0]['image']) ?>" alt="<?= e($images[0]['alt_text'] ?? '') ?>" loading="lazy">
                    </div>
                    <div class="gallery-thumbs">
                        <?php foreach ($images as $img): ?>
                            <button type="button" class="thumb" data-src="<?= SITE_URL ?>/uploads/<?= e($img['image']) ?>">
                                <img src="<?= SITE_URL ?>/uploads/<?= e($img['image']) ?>" alt="<?= e($img['alt_text'] ?? '') ?>" loading="lazy">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?= SITE_URL ?>/uploads/<?= e($product['image']) ?>" alt="<?= e($product['title_fa'] ?? '') ?>" loading="lazy">
                    <?php else: ?>
                        <img src="<?= SITE_URL ?>/assets/images/product-fallback.png" alt="تصویر محصول" loading="lazy">
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="detail-info">
                <div><strong>کد محصول:</strong> <?= e($product['sku'] ?? '-') ?></div>
                <div><strong>قیمت:</strong> <?= e($product['price'] ?? '-') ?></div>
                <div><strong>وضعیت:</strong> <?= (int) ($product['stock'] ?? 0) > 0 ? 'موجود' : 'ناموجود' ?></div>
                <div><strong>موجودی:</strong> <?= e((int) ($product['stock'] ?? 0)) ?> عدد</div>
                <?php if ((int) ($product['stock'] ?? 0) > 0): ?>
                    <form method="post" action="<?= SITE_URL ?>/cart/add">
                        <?= csrf_field() ?>
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <label>تعداد: <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" style="width:80px;"></label>
                        <button class="btn-primary" type="submit">افزودن به سبد خرید</button>
                    </form>
                <?php endif; ?>
                <div class="product-specs">
                    <h4>مشخصات</h4>
                    <?php if (!empty($product['specifications'])): ?>
                        <div><?= nl2br(e($product['specifications'])) ?></div>
                    <?php elseif (!empty($product['description_fa'])): ?>
                        <div><?= nl2br(e($product['description_fa'])) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($product['compatible_models'])): ?>
            <div class="related-products" style="margin-top:2rem;">
                <h3>سازگاری با خودروها</h3>
                <ul style="padding-inline-start:1.2rem; margin:0; line-height:1.8;">
                    <?php foreach ($product['compatible_models'] as $compat): ?>
                        <?php $brand = $compat['brand_name_fa'] ?? $compat['brand_name_en'] ?? 'خودرو'; ?>
                        <?php $modelName = $compat['name_fa'] ?? $compat['name_en'] ?? $compat['slug'] ?? 'مدل'; ?>
                        <?php $year = $compat['year'] ?? ''; ?>
                        <?php $engine = $compat['engine_type'] ?? ''; ?>
                        <li><?= e($brand) ?> - <?= e($modelName) ?><?php if ($year !== ''): ?> (<?= e($year) ?>)<?php endif; ?><?php if ($engine !== ''): ?> - <?= e($engine) ?><?php endif; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($product['related_products'])): ?>
            <div class="related-products" style="margin-top:2rem;">
                <h3>محصولات مرتبط</h3>
                <div class="service-grid">
                    <?php foreach ($product['related_products'] as $related): ?>
                        <div class="product-card">
                            <div class="product-image-wrap">
                                <?php if (!empty($related['image'])): ?>
                                    <img src="<?= SITE_URL ?>/uploads/<?= e($related['image']) ?>" alt="<?= e($related['title_fa'] ?? '') ?>" loading="lazy">
                                <?php endif; ?>
                            </div>
                            <div class="product-body">
                                <h4><a href="<?= SITE_URL ?>/products/<?= rawurlencode($related['slug'] ?? '') ?>"><?= e($related['title_fa'] ?? $related['title_en'] ?? '') ?></a></h4>
                                <div class="product-meta">
                                    <span class="price"><?= e($related['price'] ?? '-') ?></span>
                                    <span class="stock <?= ((int)($related['stock'] ?? 0) > 0) ? 'in-stock' : 'out-of-stock' ?>"><?= ((int)($related['stock'] ?? 0) > 0) ? 'موجود' : 'ناموجود' ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php if ($product):
    $productSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product['title_fa'] ?? ($product['title_en'] ?? ''),
        'description' => mb_substr(strip_tags($description ?? ''),0,160),
        'url' => $canonical,
        'seller' => [
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
    if (!empty($product['image'])) {
        $productSchema['image'] = SITE_URL . '/uploads/' . ltrim($product['image'], '/');
    }
    if (!empty($product['price'])) {
        $productSchema['offers'] = [
            '@type' => 'Offer',
            'priceCurrency' => 'IRR',
            'price' => $product['price'],
            'availability' => !empty($product['stock']) && $product['stock'] > 0 ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock',
            'url' => $canonical,
        ];
    }
    // Set OG type and image for header meta
    $og_type = 'product';
    $image = !empty($product['image']) ? SITE_URL . '/uploads/' . ltrim($product['image'], '/') : (isset($images[0]['image']) ? SITE_URL . '/uploads/' . ltrim($images[0]['image'], '/') : null);
    $schemaData = $productSchema;
    require __DIR__ . '/../partials/schema.php';
endif;
?>

<?php require __DIR__.'/../layouts/footer.php'; ?>