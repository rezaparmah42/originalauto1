<?php
$title = 'مدیریت مقالات | ' . SITE_NAME;
$description = 'مدیریت مقالات فنی و سئو در پنل مدیریت اورجینال شرق.';
$canonical = SITE_URL . '/admin/articles';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>مدیریت مقالات</h1>
            <p>ایجاد، ویرایش، انتشار و حذف مقالات فنی تعمیر خودرو.</p>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/admin/articles/create" class="btn-primary">افزودن مقاله</a>
            <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
        </div>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="get" action="<?= SITE_URL ?>/admin/articles" style="display:flex; gap:0.7rem; flex-wrap:wrap; margin-bottom:1rem;">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="جست‌وجو بر اساس عنوان، دسته یا اسلاگ" style="padding:0.7rem; min-width:260px;">
        <select name="status" style="padding:0.7rem;">
            <option value="all" <?= (($status ?? 'all') === 'all') ? 'selected' : '' ?>>همه</option>
            <option value="active" <?= (($status ?? 'all') === 'active') ? 'selected' : '' ?>>منتشر شده</option>
            <option value="inactive" <?= (($status ?? 'all') === 'inactive') ? 'selected' : '' ?>>پیش‌نویس</option>
        </select>
        <button type="submit" class="btn-primary">فیلتر</button>
    </form>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>دسته</th>
                <th>نویسنده</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?= e($article['title_fa'] ?? $article['title_en'] ?? '') ?></td>
                        <td><?= e($article['category'] ?? '-') ?></td>
                        <td><?= e($article['author'] ?? '-') ?></td>
                        <td><?= !empty($article['status']) ? 'منتشر شده' : 'پیش‌نویس' ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/articles/edit/<?= (int) ($article['id'] ?? 0) ?>" class="btn-outline">ویرایش</a>
                            <form method="post" action="<?= SITE_URL ?>/admin/articles/delete/<?= (int) ($article['id'] ?? 0) ?>" style="display:inline;" onsubmit="return confirm('آیا از حذف این مقاله اطمینان دارید؟');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-primary" style="background:#b91c1c; border-color:#b91c1c;">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">مقاله‌ای یافت نشد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
        <div style="margin-top:1rem;">
            <?php if (($page ?? 1) > 1): ?>
                <a href="<?= SITE_URL ?>/admin/articles?page=<?= (int) (($page ?? 1) - 1) ?>&search=<?= rawurlencode($search ?? '') ?>&status=<?= e($status ?? 'all') ?>" class="btn-outline">قبلی</a>
            <?php endif; ?>
            <span style="margin:0 0.7rem;">صفحه <?= e($page ?? 1) ?> از <?= e($pages) ?></span>
            <?php if (($page ?? 1) < $pages): ?>
                <a href="<?= SITE_URL ?>/admin/articles?page=<?= (int) (($page ?? 1) + 1) ?>&search=<?= rawurlencode($search ?? '') ?>&status=<?= e($status ?? 'all') ?>" class="btn-outline">بعدی</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>