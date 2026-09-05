<?php
$title = 'ویرایش مقاله | ' . SITE_NAME;
$description = 'ویرایش مقاله در پنل مدیریت اورجینال شرق.';
$canonical = SITE_URL . '/admin/articles/edit/' . (int) ($article['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 900px;">
    <h1>ویرایش مقاله</h1>
    <p>تغییرات خود را روی مقاله و تنظیمات سئو اعمال کنید.</p>

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
            <input type="text" name="title_fa" value="<?= e($article['title_fa'] ?? '') ?>" class="form-control" required>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان انگلیسی</label>
            <input type="text" name="title_en" value="<?= e($article['title_en'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>اسلاگ</label>
            <input type="text" name="slug" value="<?= e($article['slug'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>دسته</label>
            <input type="text" name="category" value="<?= e($article['category'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>نویسنده</label>
            <input type="text" name="author" value="<?= e($article['author'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>محتوای فارسی</label>
            <textarea name="content_fa" class="form-control" rows="8"><?= e($article['content_fa'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>محتوای انگلیسی</label>
            <textarea name="content_en" class="form-control" rows="8"><?= e($article['content_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان سئو فارسی</label>
            <input type="text" name="seo_title_fa" value="<?= e($article['seo_title_fa'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>عنوان سئو انگلیسی</label>
            <input type="text" name="seo_title_en" value="<?= e($article['seo_title_en'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح سئو فارسی</label>
            <textarea name="seo_description_fa" class="form-control" rows="4"><?= e($article['seo_description_fa'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>توضیح سئو انگلیسی</label>
            <textarea name="seo_description_en" class="form-control" rows="4"><?= e($article['seo_description_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>متا توضیح فارسی</label>
            <textarea name="meta_description_fa" class="form-control" rows="4"><?= e($article['meta_description_fa'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>متا توضیح انگلیسی</label>
            <textarea name="meta_description_en" class="form-control" rows="4"><?= e($article['meta_description_en'] ?? '') ?></textarea>
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>تصویر</label>
            <?php if (!empty($article['image'])): ?><div><img src="<?= SITE_URL ?>/uploads/<?= e($article['image']) ?>" alt="<?= e($article['title_fa'] ?? '') ?>" style="max-width:180px;"></div><?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group" style="margin-bottom:1rem;">
            <label>وضعیت</label>
            <select name="status" class="form-control">
                <option value="1" <?= !empty($article['status']) ? 'selected' : '' ?>>منتشر شده</option>
                <option value="0" <?= empty($article['status']) ? 'selected' : '' ?>>پیش‌نویس</option>
            </select>
        </div>
        <div style="display:flex; gap:0.7rem; flex-wrap:wrap;">
            <button type="submit" class="btn-primary">ذخیره تغییرات</button>
            <a href="<?= SITE_URL ?>/admin/articles" class="btn-outline">انصراف</a>
        </div>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>