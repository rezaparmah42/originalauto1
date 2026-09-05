<?php
$title = 'ورود مدیر | ' . SITE_NAME;
$description = 'صفحه ورود مدیر برای دسترسی به پنل مدیریت اورجینال شرق.';
$canonical = SITE_URL . '/admin/login';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="form-page admin-login">
    <div class="form-card">
        <div class="form-header">
            <h1>ورود مدیر</h1>
            <p class="form-note">برای دسترسی به بخش مدیریت سفارش‌ها و محتوا وارد شوید.</p>
        </div>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="modern-form" method="post" action="<?= SITE_URL ?>/admin/login">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="username">نام کاربری</label>
                <input class="form-input" id="username" type="text" name="username" autocomplete="username" placeholder="نام کاربری مدیر" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">رمز عبور</label>
                <input class="form-input" id="password" type="password" name="password" autocomplete="current-password" placeholder="رمز عبور" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">ورود</button>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>