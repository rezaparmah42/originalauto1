<?php
$title = 'دسته‌بندی‌ها | ' . SITE_NAME;
$description = 'مدیریت دسته‌بندی‌های محصولات';
$canonical = SITE_URL . '/admin/categories';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>دسته‌بندی‌ها</h1>
            <p>مدیریت گروه‌بندی محصولات و دسته‌های اصلی.</p>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/admin/categories/create" class="btn-primary">افزودن دسته‌بندی</a>
            <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
        </div>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;">
            <?= e($error) ?>
        </div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>نام فارسی</th>
                <th>نام انگلیسی</th>
                <th>اسلاگ</th>
                <th>والد</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= e($category['name_fa'] ?? '-') ?></td>
                        <td><?= e($category['name_en'] ?? '-') ?></td>
                        <td><?= e($category['slug'] ?? '-') ?></td>
                        <td><?= e($category['parent_id'] ?? '-') ?></td>
                        <td><?= !empty($category['status']) ? 'فعال' : 'غیرفعال' ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/categories/edit/<?= (int) ($category['id'] ?? 0) ?>" class="btn-outline">ویرایش</a>
                            <form method="post" action="<?= SITE_URL ?>/admin/categories/delete/<?= (int) ($category['id'] ?? 0) ?>" style="display:inline;" onsubmit="return confirm('آیا از حذف این دسته اطمینان دارید؟');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-primary" style="background:#b91c1c; border-color:#b91c1c;">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">هیچ دسته‌بندی‌ای ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
