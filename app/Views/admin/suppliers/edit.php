<?php
$title = 'ویرایش تأمین‌کننده | ' . SITE_NAME;
$description = 'ویرایش اطلاعات تأمین‌کننده در پنل مدیریت.';
$canonical = SITE_URL . '/admin/suppliers/edit/' . (int) ($supplier['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>ویرایش تأمین‌کننده</h1>
    <p>تغییر اطلاعات تأمین‌کننده را در فرم زیر وارد کنید.</p>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= SITE_URL ?>/admin/suppliers/edit/<?= (int) ($supplier['id'] ?? 0) ?>" style="max-width:700px; display:grid; gap:1rem;">
        <?= csrf_field() ?>
        <div>
            <label>نام تأمین‌کننده</label>
            <input type="text" name="name" value="<?= e($supplier['name'] ?? '') ?>" required style="width:100%; padding:0.7rem;">
        </div>
        <div>
            <label>نام تماس</label>
            <input type="text" name="contact" value="<?= e($supplier['contact'] ?? '') ?>" style="width:100%; padding:0.7rem;">
        </div>
        <div>
            <label>تلفن</label>
            <input type="text" name="phone" value="<?= e($supplier['phone'] ?? '') ?>" style="width:100%; padding:0.7rem;">
        </div>
        <div>
            <label>ایمیل</label>
            <input type="email" name="email" value="<?= e($supplier['email'] ?? '') ?>" style="width:100%; padding:0.7rem;">
        </div>
        <div>
            <label>یادداشت</label>
            <textarea name="notes" rows="4" style="width:100%; padding:0.7rem;"><?= e($supplier['notes'] ?? '') ?></textarea>
        </div>
        <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
            <button type="submit" class="btn-primary">به‌روزرسانی تأمین‌کننده</button>
            <a href="<?= SITE_URL ?>/admin/suppliers" class="btn-outline">انصراف</a>
        </div>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
