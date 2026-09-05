</main>

<footer class="site-footer">
    <div class="footer-top">
        <div class="footer-brand">
            <strong><?= e(SITE_NAME) ?></strong>
            <p>تعمیرات خودرو با تمرکز روی کیفیت حرفه‌ای، سرعت عمل و تجربه بهتر مشتری.</p>
            <a class="btn-outline footer-cta" href="<?= SITE_URL ?>/booking">رزرو وقت</a>
        </div>

        <div class="footer-links">
            <h3>لینک‌های سریع</h3>
            <a href="<?= SITE_URL ?>/">خانه</a>
            <a href="<?= SITE_URL ?>/services">خدمات</a>
            <a href="<?= SITE_URL ?>/articles">مقالات</a>
            <a href="<?= SITE_URL ?>/vehicles">کاتالوگ خودرو</a>
            <a href="<?= SITE_URL ?>/shop">فروشگاه</a>
            <a href="<?= SITE_URL ?>/diagnostic">عیب‌یابی آنلاین</a>
            <a href="<?= SITE_URL ?>/about">درباره ما</a>
            <a href="<?= SITE_URL ?>/contact">تماس با ما</a>
        </div>

        <div class="footer-contact">
            <h3>تماس با ما</h3>
            <p>تلفن: <?= e(SITE_PHONE) ?></p>
            <p>آدرس: <?= e(SITE_ADDRESS) ?></p>
            <p>پشتیبانی: ۸:۰۰ تا ۲۰:۰۰</p>
        </div>
    </div>

    <div class="footer-bottom">
        <p><?= e(SITE_NAME) ?> © <?= date('Y') ?> | تمام حقوق محفوظ است.</p>
    </div>
</footer>

<script src="<?= SITE_URL ?>/assets/js/app.js"></script>

</body>
</html>
