<?php
$title = 'افزودن محصول | ' . SITE_NAME;
$description = 'افزودن محصول جدید در پنل مدیریت اورجینال شرق.';
$canonical = SITE_URL . '/admin/products/create';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 800px;">
    <h1>افزودن محصول</h1>
    <p>اطلاعات محصول را وارد کنید و تصویر آن را بارگذاری کنید.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>دسته‌بندی</label>
            <select name="category_id" class="form-control">
                <option value="">بدون دسته</option>
                <?php foreach ((new \App\Models\Product())->categoriesList() as $cat): ?>
                    <option value="<?= (int)$cat['id'] ?>" <?= (!empty($product['category_id']) && (int)$product['category_id'] === (int)$cat['id']) ? 'selected' : '' ?>><?= e($cat['name_fa'] ?? $cat['name_en'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>نمایش در صفحه ویژه</label>
            <select name="featured" class="form-control">
                <option value="0" <?= empty($product['featured']) ? 'selected' : '' ?>>خیر</option>
                <option value="1" <?= !empty($product['featured']) ? 'selected' : '' ?>>بله</option>
            </select>
        </div>

        <?php $vehicleModels = (new \App\Models\ProductCompatibility())->getVehicleModelOptions(); ?>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>خودروهای سازگار</label>
            <?php if (empty($vehicleModels)): ?>
                <div class="alert alert-info" style="margin-top:0.5rem;">هیچ خودرویی برای سازگاری ثبت نشده است.</div>
            <?php else: ?>
                <div style="max-height: 300px; overflow-y:auto; border:1px solid #ddd; border-radius:8px; padding:0.75rem; margin-top:0.5rem; background:#fff;">
                    <?php foreach ($vehicleModels as $vehicle): ?>
                        <?php $vehicleId = (int) ($vehicle['id'] ?? 0); $brandName = $vehicle['brand_name_fa'] ?? $vehicle['brand_name_en'] ?? $vehicle['brand'] ?? 'خودرو'; $modelName = $vehicle['name_fa'] ?? $vehicle['name_en'] ?? $vehicle['slug'] ?? 'مدل'; ?>
                        <label style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                            <input type="checkbox" name="compatible_model_ids[]" value="<?= $vehicleId ?>">
                            <span><?= e($brandName) ?> - <?= e($modelName) ?><?php if (!empty($vehicle['year_from']) || !empty($vehicle['year_to'])): ?> (<?= e($vehicle['year_from'] ?? '') ?><?= (!empty($vehicle['year_from']) && !empty($vehicle['year_to'])) ? ' - ' : '' ?><?= e($vehicle['year_to'] ?? '') ?>)<?php endif; ?><?php if (!empty($vehicle['engine_type'])): ?> - <?= e($vehicle['engine_type']) ?><?php endif; ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان SEO (فارسی)</label>
            <input type="text" name="seo_title_fa" value="<?= e($product['seo_title_fa'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیحات SEO (فارسی)</label>
            <textarea name="seo_description_fa" class="form-control"><?= e($product['seo_description_fa'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>کلمات کلیدی (فارسی)</label>
            <input type="text" name="search_keywords_fa" value="<?= e($product['search_keywords_fa'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان فارسی</label>
            <input type="text" name="title_fa" value="<?= e($product['title_fa'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان انگلیسی</label>
            <input type="text" name="title_en" value="<?= e($product['title_en'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح فارسی</label>
            <textarea name="description_fa" class="form-control"><?= e($product['description_fa'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح انگلیسی</label>
            <textarea name="description_en" class="form-control"><?= e($product['description_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>تصویر</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>کد کالا (SKU)</label>
            <input type="text" name="sku" class="form-control" value="<?= e($product['sku'] ?? '') ?>">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>مشخصات فنی</label>
            <textarea name="specifications" class="form-control" rows="5"><?= e($product['specifications'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>قیمت</label>
            <input type="number" name="price" class="form-control" value="<?= e($product['price'] ?? 0) ?>" step="0.01" min="0">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>موجودی</label>
            <input type="number" name="stock" class="form-control" value="<?= e($product['stock'] ?? 0) ?>" min="0">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="1" <?= !empty($product['status']) ? 'selected' : '' ?>>فعال</option>
                <option value="0" <?= empty($product['status']) ? 'selected' : '' ?>>غیر فعال</option>
            </select>
        </div>
        <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
            <button type="submit" class="btn-primary">ثبت محصول</button>
            <a href="<?= SITE_URL ?>/admin/products" class="btn-outline">انصراف</a>
        </div>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>