<?php
$title = 'ویرایش محصول | ' . SITE_NAME;
$description = 'ویرایش محصول در پنل مدیریت اورجینال شرق.';
$canonical = SITE_URL . '/admin/products/edit/' . (int) ($product['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 800px;">
    <h1>ویرایش محصول</h1>
    <p>تغییر اطلاعات محصول و در صورت نیاز بارگذاری تصویر جدید.</p>

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
        <?php $productImages = (new \App\Models\Product())->getProductImages((int) ($product['id'] ?? 0)); ?>
        <?php if (!empty($productImages)): ?>
            <div class="form-group" style="margin-bottom:1rem;">
                <label>گالری تصاویر</label>
                <div style="display:flex; flex-wrap:wrap; gap:0.75rem; margin-top:0.5rem;">
                    <?php foreach ($productImages as $image): ?>
                        <div style="border:1px solid #ddd; padding:0.5rem; border-radius:8px; text-align:center; max-width:180px;">
                            <img src="<?= SITE_URL ?>/uploads/<?= e($image['image']) ?>" alt="<?= e($image['alt_text'] ?? '') ?>" style="width:120px; height:120px; object-fit:cover; border-radius:6px;">
                            <div style="margin-top:0.5rem; display:flex; gap:0.4rem; justify-content:center; flex-wrap:wrap;">
                                <form method="post" action="<?= SITE_URL ?>/admin/products/<?= (int)($product['id'] ?? 0) ?>/images/primary" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="image_id" value="<?= (int)($image['id'] ?? 0) ?>">
                                    <button type="submit" class="btn-outline" style="padding:0.4rem 0.8rem;"><?= !empty($image['is_primary']) ? 'اصلی' : 'تعیین به‌عنوان اصلی' ?></button>
                                </form>
                                <form method="post" action="<?= SITE_URL ?>/admin/products/<?= (int)($product['id'] ?? 0) ?>/images/delete" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="image_id" value="<?= (int)($image['id'] ?? 0) ?>">
                                    <button type="submit" class="btn-primary" style="padding:0.4rem 0.8rem; background:#b91c1c; border-color:#b91c1c;">حذف</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>افزودن تصویر جدید</label>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>نمایش در صفحه ویژه</label>
            <select name="featured" class="form-control">
                <option value="0" <?= empty($product['featured']) ? 'selected' : '' ?>>خیر</option>
                <option value="1" <?= !empty($product['featured']) ? 'selected' : '' ?>>بله</option>
            </select>
        </div>

        <?php $compatibilityChecked = array_fill_keys(array_map('intval', array_values((array) ($compatibleModelIds ?? []))), true); ?>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>خودروهای سازگار</label>
            <?php if (empty($vehicleModels)): ?>
                <div class="alert alert-info" style="margin-top:0.5rem;">هیچ خودرویی برای سازگاری ثبت نشده است.</div>
            <?php else: ?>
                <div style="max-height: 300px; overflow-y:auto; border:1px solid #ddd; border-radius:8px; padding:0.75rem; margin-top:0.5rem; background:#fff;">
                    <?php foreach ($vehicleModels as $vehicle): ?>
                        <?php $vehicleId = (int) ($vehicle['id'] ?? 0); $brandName = $vehicle['brand_name_fa'] ?? $vehicle['brand_name_en'] ?? $vehicle['brand'] ?? 'خودرو'; $modelName = $vehicle['name_fa'] ?? $vehicle['name_en'] ?? $vehicle['slug'] ?? 'مدل'; ?>
                        <label style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem;">
                            <input type="checkbox" name="compatible_model_ids[]" value="<?= $vehicleId ?>" <?= !empty($compatibilityChecked[$vehicleId]) ? 'checked' : '' ?>>
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
            <label>تصویر فعلی</label>
            <?php if (!empty($product['image'])): ?>
                <div style="margin-top:0.5rem;">
                    <img src="<?= SITE_URL ?>/uploads/<?= e($product['image']) ?>" alt="<?= e($product['title_fa'] ?? '') ?>" style="max-width:180px; border-radius:8px;">
                </div>
            <?php endif; ?>
            <input type="file" name="image" class="form-control" style="margin-top:0.5rem;">
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
            <button type="submit" class="btn-primary">به‌روزرسانی محصول</button>
            <a href="<?= SITE_URL ?>/admin/products" class="btn-outline">انصراف</a>
        </div>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
