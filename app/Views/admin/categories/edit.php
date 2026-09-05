<?php
$title = 'ویرایش دسته‌بندی | ' . SITE_NAME;
$description = 'ویرایش دسته‌بندی محصول';
$canonical = SITE_URL . '/admin/categories/edit/' . (int) ($category['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 750px;">
    <h1>ویرایش دسته‌بندی</h1>
    <p>دسته‌بندی را ویرایش کنید.</p>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" style="display:grid; gap:1rem;">
        <?= csrf_field() ?>
        <div>
            <label>نام فارسی</label>
            <input type="text" name="name_fa" value="<?= e($category['name_fa'] ?? '') ?>" class="form-control">
        </div>
        <div>
            <label>نام انگلیسی</label>
            <input type="text" name="name_en" value="<?= e($category['name_en'] ?? '') ?>" class="form-control">
        </div>
        <div>
            <label>اسلاگ</label>
            <input type="text" name="slug" value="<?= e($category['slug'] ?? '') ?>" class="form-control">
        </div>
        <div>
            <label>دسته والد</label>
            <select name="parent_id" class="form-control">
                <option value="">بدون والد</option>
                <?php foreach ((new \App\Models\ProductCategory())->getAll() as $parent): ?>
                    <?php if ((int)($category['id'] ?? 0) === (int)($parent['id'] ?? 0)) continue; ?>
                    <option value="<?= (int)($parent['id'] ?? 0) ?>" <?= (!empty($category['parent_id']) && (int)$category['parent_id'] === (int)($parent['id'] ?? 0)) ? 'selected' : '' ?>><?= e($parent['name_fa'] ?? $parent['name_en'] ?? '') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>توضیح فارسی</label>
            <textarea name="description_fa" rows="4" class="form-control"><?= e($category['description_fa'] ?? '') ?></textarea>
        </div>
        <div>
            <label>توضیح انگلیسی</label>
            <textarea name="description_en" rows="4" class="form-control"><?= e($category['description_en'] ?? '') ?></textarea>
        </div>
        <div>
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="1" <?= (!empty($category['status']) ? 'selected' : '') ?>>فعال</option>
                <option value="0" <?= (empty($category['status']) ? 'selected' : '') ?>>غیرفعال</option>
            </select>
        </div>
        <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
            <button type="submit" class="btn-primary">به‌روزرسانی دسته‌بندی</button>
            <a href="<?= SITE_URL ?>/admin/categories" class="btn-outline">انصراف</a>
        </div>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
