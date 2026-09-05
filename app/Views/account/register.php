<?php
$title = 'ثبت نام مشتری | ' . SITE_NAME;
$description = 'ثبت نام در اورجینال شرق برای دریافت خدمات حرفه‌ای تعمیر خودرو، رزرو سریع و مدیریت سفارش‌ها به صورت آنلاین.';
$canonical = SITE_URL . '/register';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="form-page auth-panel">
    <div class="form-card">
        <div class="form-header">
            <h1>ثبت نام مشتری</h1>
            <p class="form-note">با ایجاد حساب کاربری، سریع‌تر و راحت‌تر خدمات تعمیر و نگهداری خودرو را رزرو کنید.</p>
        </div>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="modern-form" method="post" action="<?= SITE_URL ?>/register">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="name">نام کامل</label>
                <input class="form-input" id="name" type="text" name="name" value="<?= e(old('name')) ?>" autocomplete="name" placeholder="نام و نام خانوادگی" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="phone">شماره موبایل</label>
                <input class="form-input" id="phone" type="tel" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel" placeholder="0912xxxxxxxx" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">ایمیل (اختیاری)</label>
                <input class="form-input" id="email" type="email" name="email" value="<?= e(old('email')) ?>" autocomplete="email" placeholder="اختیاری">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">رمز عبور</label>
                <input class="form-input" id="password" type="password" name="password" autocomplete="new-password" placeholder="رمز عبور جدید" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirm">تکرار رمز عبور</label>
                <input class="form-input" id="password_confirm" type="password" name="password_confirm" autocomplete="new-password" placeholder="رمز عبور را دوباره وارد کنید" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">ثبت نام</button>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
