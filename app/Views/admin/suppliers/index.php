<?php
$title = 'مدیریت تأمین‌کنندگان | ' . SITE_NAME;
$description = 'مدیریت تأمین‌کنندگان و اطلاعات تماس در پنل مدیریت.';
$canonical = SITE_URL . '/admin/suppliers';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1>تأمین‌کنندگان</h1>
            <p>مدیریت اطلاعات تأمین‌کنندگان و تماس‌های فروش.</p>
        </div>
        <div>
            <a href="<?= SITE_URL ?>/admin/suppliers/create" class="btn-primary">افزودن تأمین‌کننده</a>
            <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">بازگشت به داشبورد</a>
        </div>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <table class="table table-striped" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>نام</th>
                <th>تماس</th>
                <th>تلفن</th>
                <th>ایمیل</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($suppliers)): ?>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?= e($supplier['name'] ?? '-') ?></td>
                        <td><?= e($supplier['contact'] ?? '-') ?></td>
                        <td><?= e($supplier['phone'] ?? '-') ?></td>
                        <td><?= e($supplier['email'] ?? '-') ?></td>
                        <td>
                            <a href="<?= SITE_URL ?>/admin/suppliers/show/<?= (int) ($supplier['id'] ?? 0) ?>" class="btn-outline">نمایش</a>
                            <a href="<?= SITE_URL ?>/admin/suppliers/edit/<?= (int) ($supplier['id'] ?? 0) ?>" class="btn-outline">ویرایش</a>
                            <form method="post" action="<?= SITE_URL ?>/admin/suppliers/delete/<?= (int) ($supplier['id'] ?? 0) ?>" style="display:inline;" onsubmit="return confirm('آیا از حذف این تأمین‌کننده اطمینان دارید؟');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-primary" style="background:#b91c1c; border-color:#b91c1c;">حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">تأمین‌کننده‌ای ثبت نشده است.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
