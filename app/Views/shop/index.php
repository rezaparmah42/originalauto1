






























<?php
$title = 'فروشگاه | ' . SITE_NAME;
$description = 'مشاهده محصولات و قطعات سازگار برای خودروهای مختلف در اورجینال شرق.';
$canonical = SITE_URL . '/shop';
?>
<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">فروشگاه قطعات و لوازم یدکی</div>
    <h1>انتخاب قطعات مناسب برای خودرو شما، با مشاوره تخصصی و دسترسی سریع</h1>
    <p>در این بخش می‌توانید با فیلتر کردن برند، مدل و موتور خودرو، محصولات مرتبط را مشاهده کرده و برای استعلام یا خرید با ما در تماس باشید.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="<?= SITE_URL ?>/booking">استعلام سریع</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
    </div>
</section>

<section class="section-shell shop-shell">
    <div class="shop-panel">
        <div class="section-heading">
            <h2>فیلتر محصولات</h2>
            <p>برای پیدا کردن قطعه مناسب، اطلاعات خودرو را انتخاب کنید.</p>
        </div>

        <form class="modern-form" method="get" action="<?= SITE_URL ?>/shop">
            <div class="form-group">
                <label class="form-label" for="q">جست‌وجوی محصول</label>
                <input class="form-input" id="q" name="q" value="<?= e($filter['q'] ?? '') ?>" placeholder="نام یا توضیحات محصول">
            </div>
            <div class="form-group">
                <label class="form-label" for="category">دسته‌بندی</label>
                <select class="form-input" id="category" name="category">
                    <option value="">همه دسته‌ها</option>
                    <?php foreach ($categories as $catOption): ?>
                        <option value="<?= (int)$catOption['id'] ?>" <?= (!empty($filter['category']) && (int)$filter['category'] === (int)$catOption['id']) ? 'selected' : '' ?>><?= e($catOption['name_fa'] ?? $catOption['name_en'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="vehicle_brand_id">برند خودرو</label>
                <select class="form-input" id="vehicle_brand_id" name="vehicle_brand_id">
                    <option value="">انتخاب برند</option>
                    <?php foreach ($vehicleBrands as $brandOption): ?>
                        <option value="<?= (int) ($brandOption['id'] ?? 0) ?>" <?= (!empty($filter['vehicle_brand_id']) && (int)$filter['vehicle_brand_id'] === (int)($brandOption['id'] ?? 0)) ? 'selected' : '' ?>><?= e($brandOption['name_fa'] ?? $brandOption['name_en'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="vehicle_model_id">مدل خودرو</label>
                <select class="form-input" id="vehicle_model_id" name="vehicle_model_id">
                    <option value="">انتخاب مدل</option>
                    <?php foreach ($vehicleModels as $modelOption): ?>
                        <option value="<?= (int) ($modelOption['id'] ?? 0) ?>" <?= (!empty($filter['vehicle_model_id']) && (int)$filter['vehicle_model_id'] === (int)($modelOption['id'] ?? 0)) ? 'selected' : '' ?>><?= e($modelOption['name_fa'] ?? $modelOption['name_en'] ?? '') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="vehicle_year">سال ساخت</label>
                <input class="form-input" id="vehicle_year" name="vehicle_year" type="number" min="1900" max="2100" step="1" value="<?= e($filter['vehicle_year'] ?? '') ?>" placeholder="مثال: 1402">
            </div>

            <div class="form-group">
                <label class="form-label" for="sort">مرتب‌سازی</label>
                <select class="form-input" id="sort" name="sort">
                    <option value="newest" <?= (empty($filter['sort']) || $filter['sort']==='newest') ? 'selected' : '' ?>>جدیدترین</option>
                    <option value="price_low" <?= (!empty($filter['sort']) && $filter['sort']==='price_low') ? 'selected' : '' ?>>قیمت: کم → زیاد</option>
                    <option value="price_high" <?= (!empty($filter['sort']) && $filter['sort']==='price_high') ? 'selected' : '' ?>>قیمت: زیاد → کم</option>
                    <option value="popular" <?= (!empty($filter['sort']) && $filter['sort']==='popular') ? 'selected' : '' ?>>محبوب‌ترین</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">فیلتر کردن</button>
                <?php if (!empty($filter['vehicle_brand_id']) || !empty($filter['vehicle_model_id']) || !empty($filter['vehicle_year'])): ?>
                    <a class="btn-outline" href="<?= SITE_URL ?>/shop?clear_vehicle=1">پاک کردن فیلتر خودرو</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if (!empty($selectedVehicle)): ?>
            <div class="info-card" style="margin-top: 1rem;">
                <strong>خودروی انتخاب شده:</strong>
                <?= e($selectedVehicle['brand_name'] ?? '') ?>
                <?= e($selectedVehicle['model_name'] ?? '') ?>
                <?= !empty($selectedVehicle['vehicle_year']) ? e($selectedVehicle['vehicle_year']) : '' ?>
                <br>
                <a href="<?= SITE_URL ?>/shop?clear_vehicle=1" class="text-link">تغییر خودرو</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="shop-results">
        <div class="section-heading">
            <h2>محصولات پیشنهادی</h2>
            <p>بر اساس انتخاب شما، محصولات مرتبط و در دسترس نمایش داده می‌شوند.</p>
        </div>
        <div class="service-grid">
            <?php if (empty($filteredProducts)): ?>
                <div class="info-card">
                    <?php if (!empty($selectedVehicle)): ?>
                        <strong>برای این خودرو محصول سازگار پیدا نشد</strong>
                    <?php else: ?>
                        <strong>در حال حاضر محصولی با این فیلترها در دسترس نیست.</strong>
                    <?php endif; ?>
                    <span>برای مشاوره و استعلام قطعه، همین حالا با ما تماس بگیرید.</span>
                </div>
            <?php else: ?>
                <?php foreach ($filteredProducts as $product): ?>
                    <div class="product-card">
                        <div class="product-image-wrap">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= SITE_URL ?>/uploads/<?= e($product['image']) ?>" alt="<?= e($product['title_fa'] ?? '') ?>" loading="lazy">
                            <?php else: ?>
                                <img src="<?= SITE_URL ?>/assets/images/product-fallback.png" alt="تصویر محصول" loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="product-body">
                            <h3 class="product-title"><a href="<?= SITE_URL ?>/products/<?= rawurlencode($product['slug'] ?? '') ?>"><?= e(mb_substr($product['title_fa'] ?? $product['title_en'] ?? 'محصول', 0, 80)) ?></a></h3>
                            <p class="product-excerpt"><?= e(mb_substr($product['description_fa'] ?? $product['description_en'] ?? '', 0, 120)) ?></p>
                            <div class="product-meta">
                                <span class="price"><?= !empty($product['price']) ? e($product['price']) : 'تماس' ?></span>
                                <span class="stock <?= ((int)$product['stock'] > 0) ? 'in-stock' : 'out-of-stock' ?>"><?= ((int)$product['stock'] > 0) ? 'موجود' : 'ناموجود' ?></span>
                            </div>
                            <div class="product-actions">
                                <a class="btn-outline" href="<?= SITE_URL ?>/products/<?= rawurlencode($product['slug'] ?? '') ?>">جزئیات</a>
                                <?php if ((int) ($product['stock'] ?? 0) > 0): ?>
                                    <form method="post" action="<?= SITE_URL ?>/cart/add" class="inline-cart-form">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="btn-primary" type="submit">افزودن به سبد</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php if (!empty($pages) && $pages > 1): ?>
            <div class="pagination">
                <?php if (($page ?? 1) > 1): ?>
                    <a href="<?= SITE_URL ?>/shop?page=<?= (int) (($page ?? 1) - 1) ?>&q=<?= rawurlencode($filter['q'] ?? '') ?>&category=<?= (int) ($filter['category'] ?? 0) ?>&sort=<?= e($filter['sort'] ?? '') ?>&vehicle_brand_id=<?= (int) ($filter['vehicle_brand_id'] ?? 0) ?>&vehicle_model_id=<?= (int) ($filter['vehicle_model_id'] ?? 0) ?>&vehicle_year=<?= rawurlencode((string) ($filter['vehicle_year'] ?? '')) ?>" class="btn-outline">قبلی</a>
                <?php endif; ?>
                <span>صفحه <?= e($page ?? 1) ?> از <?= e($pages) ?></span>
                <?php if (($page ?? 1) < $pages): ?>
                    <a href="<?= SITE_URL ?>/shop?page=<?= (int) (($page ?? 1) + 1) ?>&q=<?= rawurlencode($filter['q'] ?? '') ?>&category=<?= (int) ($filter['category'] ?? 0) ?>&sort=<?= e($filter['sort'] ?? '') ?>&vehicle_brand_id=<?= (int) ($filter['vehicle_brand_id'] ?? 0) ?>&vehicle_model_id=<?= (int) ($filter['vehicle_model_id'] ?? 0) ?>&vehicle_year=<?= rawurlencode((string) ($filter['vehicle_year'] ?? '')) ?>" class="btn-outline">بعدی</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
