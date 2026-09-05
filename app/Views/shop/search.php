<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="form-page shop-search">
    <div class="form-card">
        <div class="form-header">
            <h1>جستجوی قطعات خودرو</h1>
            <p class="form-note">بر اساس برند، مدل، سال یا شماره قطعه جستجو کنید و قطعه‌های سازگار را ببینید.</p>
        </div>

        <form class="modern-form" method="get" action="<?= SITE_URL ?>/shop/search">
            <div class="form-group">
                <label class="form-label" for="brand">برند خودرو</label>
                <select class="form-input" id="brand" name="brand">
                    <option value="">انتخاب کنید</option>
                    <?php foreach ($brands as $brandOption): ?>
                        <option value="<?= e($brandOption) ?>" <?= $filter['brand'] === $brandOption ? 'selected' : '' ?>><?= e($brandOption) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="model">مدل خودرو</label>
                <select class="form-input" id="model" name="model">
                    <option value="">انتخاب کنید</option>
                    <?php foreach ($models as $modelOption): ?>
                        <option value="<?= e($modelOption) ?>" <?= $filter['model'] === $modelOption ? 'selected' : '' ?>><?= e($modelOption) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="year">سال</label>
                <select class="form-input" id="year" name="year">
                    <option value="">انتخاب کنید</option>
                    <?php foreach ($years as $yearOption): ?>
                        <option value="<?= e($yearOption) ?>" <?= $filter['year'] === $yearOption ? 'selected' : '' ?>><?= e($yearOption) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="query">عبارت جستجو</label>
                <input class="form-input" id="query" type="text" name="query" value="<?= e($filter['query'] ?? '') ?>" placeholder="نام محصول یا توضیح">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">جستجو</button>
            </div>
        </form>
    </div>
</section>

<section class="products-list">
    <div class="product-grid">
        <?php if (empty($products)): ?>
            <div class="empty-state">هیچ محصولی یافت نشد.</div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <h2><?= e($product['title_fa'] ?? $product['title_en'] ?? '') ?></h2>
                    <p class="product-summary"><?= e($product['description_fa'] ?? $product['description_en'] ?? '') ?></p>
                    <div class="product-meta">
                        <span>قیمت: <?= number_format((float) ($product['price'] ?? 0)) ?> تومان</span>
                        <span>موجودی: <?= e($product['stock'] ?? 0) ?></span>
                    </div>
                    <a class="btn-outline" href="<?= SITE_URL ?>/shop/product/<?= e($product['slug']) ?>">مشاهده جزئیات</a>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>