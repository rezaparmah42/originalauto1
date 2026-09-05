<?php
$title = 'افزودن خدمت | ' . SITE_NAME;
$description = 'افزودن خدمت جدید در پنل مدیریت اورجینال شرق.';
$canonical = SITE_URL . '/admin/services/create';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 800px;">
    <h1>افزودن خدمت</h1>
    <p>اطلاعات خدمت را وارد کنید.</p>

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
            <label>عنوان فارسی</label>
            <input type="text" name="title_fa" value="<?= e($service['title_fa'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان انگلیسی</label>
            <input type="text" name="title_en" value="<?= e($service['title_en'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>اسلاگ</label>
            <input type="text" name="slug" value="<?= e($service['slug'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح فارسی</label>
            <textarea name="description_fa" class="form-control"><?= e($service['description_fa'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح انگلیسی</label>
            <textarea name="description_en" class="form-control"><?= e($service['description_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان سئو فارسی</label>
            <input type="text" name="seo_title_fa" value="<?= e($service['seo_title_fa'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان سئو انگلیسی</label>
            <input type="text" name="seo_title_en" value="<?= e($service['seo_title_en'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح سئو فارسی</label>
            <textarea name="seo_description_fa" class="form-control"><?= e($service['seo_description_fa'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح سئو انگلیسی</label>
            <textarea name="seo_description_en" class="form-control"><?= e($service['seo_description_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>قیمت</label>
            <input type="number" name="price" value="<?= e($service['price'] ?? 0) ?>" class="form-control" step="0.01" min="0">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>مدت زمان</label>
            <input type="text" name="duration" value="<?= e($service['duration'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>تصویر</label>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="1" <?= !empty($service['status']) ? 'selected' : '' ?>>فعال</option>
                <option value="0" <?= empty($service['status']) ? 'selected' : '' ?>>غیر فعال</option>
            </select>
        </div>
        <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
            <button type="submit" class="btn-primary">ثبت خدمت</button>
            <a href="<?= SITE_URL ?>/admin/services" class="btn-outline">انصراف</a>
        </div>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>