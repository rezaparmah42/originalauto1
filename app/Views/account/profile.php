<?php
$title = 'پروفایل کاربری | ' . SITE_NAME;
$description = 'ویرایش اطلاعات شخصی و تغییر رمز عبور.';
$canonical = SITE_URL . '/account/profile';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem; max-width: 900px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem;">
        <div>
            <h1>پروفایل کاربری</h1>
            <p>اطلاعات شخصی، رمز عبور و دسترسی‌های حساب خود را مدیریت کنید.</p>
        </div>
        <a href="<?= SITE_URL ?>/dashboard" class="btn-outline">بازگشت به داشبورد</a>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger" style="margin-bottom:1rem;"><?= e($error) ?></div>
    <?php endif; ?>
    <?php if ($success = flash('success')): ?>
        <div class="alert alert-success" style="margin-bottom:1rem;"><?= e($success) ?></div>
    <?php endif; ?>

    <div style="display:grid; gap:1.5rem;">
        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">اطلاعات شخصی</h2>
            <form method="post" action="<?= SITE_URL ?>/account/profile">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>نام و نام خانوادگی</label>
                    <input type="text" name="name" value="<?= e($user['name'] ?? '') ?>" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>شماره موبایل</label>
                    <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>ایمیل</label>
                    <input type="email" name="email" value="<?= e($user['email'] ?? '') ?>" class="form-control">
                </div>
                <button type="submit" class="btn-primary">ذخیره اطلاعات</button>
            </form>
        </section>

        <section style="border:1px solid #e5e7eb; border-radius:12px; padding:1.3rem; background:#fff;">
            <h2 style="margin-top:0;">تغییر رمز عبور</h2>
            <form method="post" action="<?= SITE_URL ?>/account/password">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>رمز عبور فعلی</label>
                    <input type="password" name="current_password" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>رمز عبور جدید</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:1rem;">
                    <label>تکرار رمز عبور جدید</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn-primary">تغییر رمز عبور</button>
            </form>
        </section>
    </div>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
