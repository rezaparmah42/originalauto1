<?php
$title = 'رزرو تعمیرگاه | ' . SITE_NAME;
$description = 'درخواست رزرو تعمیرگاه اورجینال شرق را ثبت کنید تا تیم حرفه‌ای ما زمان و مشکل خودرو شما را بررسی کند.';
$canonical = SITE_URL . '/booking';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="page-hero">
    <div class="hero-badge">رزرو سریع و مطمئن</div>
    <h1>درخواست تعمیر خودرو را در چند دقیقه ثبت کنید</h1>
    <p>با وارد کردن مدل خودرو، نوع خدمات و توضیح مشکل، تیم فنی ما در کوتاه‌ترین زمان با شما هماهنگ می‌شود و مسیر تشخیص و تعمیر را مشخص می‌کند.</p>
    <div class="hero-actions">
        <a class="btn-primary" href="#booking-form">ثبت درخواست رزرو</a>
        <a class="btn-outline" href="<?= SITE_URL ?>/services">مشاهده خدمات</a>
    </div>
</section>

<section class="booking-shell">
    <div class="booking-info">
        <div class="detail-card">
            <h2>چرا رزرو آنلاین ما با اعتماد بیشتری انجام می‌شود؟</h2>
            <ul class="check-list">
                <li>بررسی اولیه و هماهنگی سریع توسط کارشناسان فنی</li>
                <li>دیاگ و تشخیص دقیق قبل از تعمیر برای کاهش هزینه‌ها</li>
                <li>پشتیبانی تخصصی برای خودروهای داخلی و وارداتی</li>
                <li>گارانتی خدمات و بهره‌گیری از تجهیزات حرفه‌ای</li>
            </ul>
        </div>

        <div class="metric-strip">
            <div class="metric-box">
                <strong>12+</strong>
                <span>سال تجربه فنی</span>
            </div>
            <div class="metric-box">
                <strong>24h</strong>
                <span>هماهنگی سریع</span>
            </div>
            <div class="metric-box">
                <strong>100%</strong>
                <span>بررسی دقیق قبل از تعمیر</span>
            </div>
        </div>
    </div>

    <div class="form-card booking-form-card" id="booking-form">
        <div class="form-header">
            <h2>فرم رزرو تعمیرگاه</h2>
            <p class="form-note">اطلاعات خودرو و مشکل را دقیق وارد کنید تا زمان مناسب برای بررسی و تعمیر به شما اعلام شود.</p>
        </div>

        <?php if ($error = flash('error')): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if ($success = flash('success')): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>

        <form class="modern-form" method="post" action="<?= SITE_URL ?>/booking">
            <?= csrf_field() ?>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="customer_name">نام و نام خانوادگی</label>
                    <input class="form-input" id="customer_name" type="text" name="customer_name" value="<?= e(old('customer_name')) ?>" placeholder="نام شما" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="customer_phone">شماره تماس</label>
                    <input class="form-input" id="customer_phone" type="tel" name="customer_phone" value="<?= e(old('customer_phone')) ?>" placeholder="۰۹۱۲۱۲۳۴۵۶۷" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="vehicle_brand">برند خودرو</label>
                    <input class="form-input" id="vehicle_brand" type="text" name="vehicle_brand" value="<?= e(old('vehicle_brand')) ?>" placeholder="مثلاً پژو، سایپا، کیا" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="vehicle_model">مدل خودرو</label>
                    <input class="form-input" id="vehicle_model" type="text" name="vehicle_model" value="<?= e(old('vehicle_model')) ?>" placeholder="مثلاً 206، پراید، ریو" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="vehicle_year">سال تولید</label>
                    <input class="form-input" id="vehicle_year" type="number" min="1350" max="1405" name="vehicle_year" value="<?= e(old('vehicle_year')) ?>" placeholder="۱۴۰۲">
                </div>

                <div class="form-group">
                    <label class="form-label" for="service_id">نوع خدمات</label>
                    <select class="form-input" id="service_id" name="service_id" required>
                        <option value="">انتخاب کنید</option>
                        <?php foreach (($services ?? []) as $service): ?>
                            <option value="<?= (int) $service['id'] ?>"><?= e($service['title_fa'] ?? $service['title_en'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="problem_type">نوع مشکل</label>
                <input class="form-input" id="problem_type" type="text" name="problem_type" value="<?= e(old('problem_type')) ?>" placeholder="مثلاً روشن نشدن، افت قدرت، چراغ چک">
            </div>

            <div class="form-group">
                <label class="form-label" for="problem">توضیحات مشکل</label>
                <textarea class="form-textarea" id="problem" name="problem" rows="5" placeholder="مشکل خودرو را دقیق بنویسید؛ مثلاً روشن نشدن ماشین، افت قدرت، صدای گیربکس، خرابی کولر و ..." required><?= e(old('problem')) ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="booking_date">تاریخ و زمان موردنظر</label>
                <input class="form-input" id="booking_date" type="datetime-local" name="booking_date" value="<?= e(old('booking_date')) ?>">
                <span class="form-note">اگر تاریخ را وارد نکنید، ما در اولین زمان مناسب با شما هماهنگ می‌کنیم.</span>
            </div>

            <div class="booking-highlight">
                <strong>پیش‌نیاز برای تعمیر دقیق:</strong>
                <span>در صورت امکان، در زمان مراجعه، عکس یا ویدئوی مشکل خودرو را هم ارسال کنید.</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">ارسال درخواست رزرو</button>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__.'/../layouts/footer.php'; ?>
