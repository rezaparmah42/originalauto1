<?php
$title = 'ورود مشتری | ' . SITE_NAME;
$description = 'وارد حساب کاربری خود در اورجینال شرق شوید و مدیریت رزروها، درخواست سرویس و مشاهده سابقه خدمات را به‌سادگی انجام دهید.';
$canonical = SITE_URL . '/login';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="form-page auth-panel">
    <div class="form-card">
        <div class="form-header">
            <h1>ورود مشتری</h1>
            <p class="form-note">برای رزرو تعمیرگاه یا مشاهده سابقه خدمات، لطفاً وارد حساب کاربری خود شوید.</p>
        </div>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="modern-form" method="post" action="<?= SITE_URL ?>/login">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label" for="login">شماره موبایل یا ایمیل</label>
                <input class="form-input" id="login" type="text" name="login" value="<?= e(old('login')) ?>" autocomplete="username" placeholder="مثلاً 0912xxxxxxx" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">رمز عبور</label>
                <input class="form-input" id="password" type="password" name="password" autocomplete="current-password" placeholder="رمز عبور خود را وارد کنید" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">ورود به حساب</button>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
