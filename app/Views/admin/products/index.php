<?php
$title = 'مدیریت محصولات | ' . SITE_NAME;
$description = 'مدیریت محصولات در پنل مدیریت اورجینال شرق.';
$canonical = SITE_URL . '/admin/products';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>مدیریت محصولات</h1>
            <p>نمایش، جست‌وجو و مدیریت محصولات فروشگاه.</p>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/admin/products/create" class="btn-primary">افزودن محصول</a>
            <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
        </div>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <form method="get" action="<?= SITE_URL ?>/admin/products" style="display:flex; gap:0.7rem; flex-wrap:wrap; margin-bottom:1rem;">
        <input type="text" name="search" value="<?= e($search ?? '') ?>" placeholder="جست‌وجو بر اساس نام" style="padding:0.7rem; min-width:220px;">
        <select name="category" style="padding:0.7rem; min-width:180px;">
            <option value="">همه دسته‌ها</option>
            <?php foreach ($categories ?? [] as $cat): ?>
                <option value="<?= (int) ($cat['id'] ?? 0) ?>" <?= (!empty($category) && (int)$category === (int)($cat['id'] ?? 0)) ? 'selected' : '' ?>><?= e($cat['name_fa'] ?? $cat['name_en'] ?? '') ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" style="padding:0.7rem; min-width:180px;">
            <option value="all" <?= (($status ?? 'all') === 'all') ? 'selected' : '' ?>>همه وضعیت‌ها</option>
            <option value="active" <?= (($status ?? 'all') === 'active') ? 'selected' : '' ?>>فعال</option>
            <option value="inactive" <?= (($status ?? 'all') === 'inactive') ? 'selected' : '' ?>>غیر فعال</option>
        </select>
        <select name="stock" style="padding:0.7rem; min-width:180px;">
            <option value="all" <?= (($stock ?? 'all') === 'all') ? 'selected' : '' ?>>همه موجودی‌ها</option>
            <option value="in_stock" <?= (($stock ?? 'all') === 'in_stock') ? 'selected' : '' ?>>موجود</option>
            <option value="out_of_stock" <?= (($stock ?? 'all') === 'out_of_stock') ? 'selected' : '' ?>>ناموجود</option>
            <option value="low_stock" <?= (($stock ?? 'all') === 'low_stock') ? 'selected' : '' ?>>کم‌موجود</option>
        </select>
        <button type="submit" class="btn-primary">فیلتر</button>
    </form>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>تصویر</th>
                <th>نام</th>
                <th>دسته</th>
                <th>قیمت</th>
                <th>موجودی</th>
                <th>ویژه</th>
                <th>وضعیت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= SITE_URL ?>/uploads/<?= e($product['image']) ?>" alt="<?= e($product['title_fa'] ?? '') ?>" style="width:60px; height:60px; object-fit:cover; border-radius:6px;">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= e($product['title_fa'] ?? $product['title_en'] ?? '') ?></td>
                        <td><?= e((function ($categoryId) use ($categories) { foreach (($categories ?? []) as $cat) { if ((int)($cat['id'] ?? 0) === (int)$categoryId) { return $cat['name_fa'] ?? $cat['name_en'] ?? ''; } } return '-'; })($product['category_id'] ?? null)) ?></td>
                        <td><?= e($product['price'] ?? 0) ?></td>
                        <td><?= e($product['stock'] ?? 0) ?></td>
                        <td><?= !empty($product['featured']) ? 'بله' : 'خیر' ?></td>
                        <td><?= !empty($product['status']) ? 'فعال' : 'غیر فعال' ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/products/edit/<?= (int) ($product['id'] ?? 0) ?>" class="btn-outline">ویرایش</a>
                            <form method="post" action="<?= SITE_URL ?>/admin/products/delete/<?= (int) ($product['id'] ?? 0) ?>" style="display:inline;" onsubmit="return confirm('آیا از حذف این محصول اطمینان دارید؟');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-primary" style="background:#b91c1c; border-color:#b91c1c;">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">محصولی یافت نشد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($pages > 1): ?>
        <div style="margin-top:1rem;">
            <?php if (($page ?? 1) > 1): ?>
                <a href="<?= SITE_URL ?>/admin/products?page=<?= (int) (($page ?? 1) - 1) ?>&search=<?= rawurlencode($search ?? '') ?>&category=<?= e($category ?? '') ?>&status=<?= e($status ?? 'all') ?>&stock=<?= e($stock ?? 'all') ?>" class="btn-outline">قبلی</a>
            <?php endif; ?>
            <span style="margin:0 0.7rem;">صفحه <?= e($page ?? 1) ?> از <?= e($pages) ?></span>
            <?php if (($page ?? 1) < $pages): ?>
                <a href="<?= SITE_URL ?>/admin/products?page=<?= (int) (($page ?? 1) + 1) ?>&search=<?= rawurlencode($search ?? '') ?>&category=<?= e($category ?? '') ?>&status=<?= e($status ?? 'all') ?>&stock=<?= e($stock ?? 'all') ?>" class="btn-outline">بعدی</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>