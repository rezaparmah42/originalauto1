<?php
$title = 'مدیریت کاربران | ' . SITE_NAME;
$description = 'مدیریت کاربران و نقش‌های سامانه.';
$canonical = SITE_URL . '/admin/users';
$robots = 'noindex, nofollow';
require __DIR__ . '/../../layouts/header.php';
?>
<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>مدیریت کاربران</h1>
    <form method="get" action="<?= SITE_URL ?>/admin/users" style="display:flex; gap:.5rem; margin:1rem 0; flex-wrap:wrap;">
        <input class="form-control" type="search" name="search" value="<?= e($search ?? '') ?>" placeholder="جست‌وجوی نام، ایمیل یا تلفن">
        <button class="btn-primary" type="submit">جست‌وجو</button>
    </form>
    <table class="dashboard-table">
        <thead><tr><th>نام</th><th>ایمیل</th><th>تلفن</th><th>نقش</th><th>وضعیت</th><th>عملیات</th></tr></thead>
        <tbody>
        <?php foreach (($users ?? []) as $user): ?>
            <tr>
                <td><?= e($user['name'] ?? '') ?></td>
                <td><?= e($user['email'] ?? '') ?></td>
                <td><?= e($user['phone'] ?? '-') ?></td>
                <td><?= e($user['role'] ?? 'customer') ?></td>
                <td><?= !empty($user['status']) ? 'فعال' : 'غیرفعال' ?></td>
                <td><a class="btn-outline" href="<?= SITE_URL ?>/admin/users/edit/<?= (int) $user['id'] ?>">ویرایش</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($users)): ?><tr><td colspan="6">کاربری پیدا نشد.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
