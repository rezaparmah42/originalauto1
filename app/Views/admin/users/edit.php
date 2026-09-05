<?php
$title = 'ویرایش کاربر | ' . SITE_NAME;
$description = 'ویرایش اطلاعات و دسترسی کاربر.';
$canonical = SITE_URL . '/admin/users/edit/' . (int) ($user['id'] ?? 0);
$robots = 'noindex, nofollow';
require __DIR__ . '/../../layouts/header.php';
?>
<div class="container" style="padding:2rem 1rem 3rem; max-width:700px;">
    <h1>ویرایش کاربر</h1>
    <?php if (!empty($errors)): ?><div class="alert alert-danger"><ul><?php foreach ($errors as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <form method="post" action="<?= SITE_URL ?>/admin/users/edit/<?= (int) ($user['id'] ?? 0) ?>">
        <?= csrf_field() ?>
        <div class="form-group"><label>نام</label><input class="form-control" type="text" name="name" value="<?= e($user['name'] ?? '') ?>" required></div>
        <div class="form-group"><label>ایمیل</label><input class="form-control" type="email" name="email" value="<?= e($user['email'] ?? '') ?>" required></div>
        <div class="form-group"><label>تلفن</label><input class="form-control" type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>"></div>
        <div class="form-group"><label>نقش</label><select class="form-control" name="role"><?php foreach (($roles ?? []) as $role): ?><option value="<?= e($role) ?>" <?= (($user['role'] ?? '') === $role) ? 'selected' : '' ?>><?= e($role) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label>وضعیت</label><select class="form-control" name="status"><option value="1" <?= !empty($user['status']) ? 'selected' : '' ?>>فعال</option><option value="0" <?= empty($user['status']) ? 'selected' : '' ?>>غیرفعال</option></select></div>
        <div style="display:flex;gap:.7rem;margin-top:1rem;"><button class="btn-primary" type="submit">ذخیره تغییرات</button><a class="btn-outline" href="<?= SITE_URL ?>/admin/users">انصراف</a></div>
    </form>
</div>
<?php require __DIR__ . '/../../layouts/footer.php'; ?>
