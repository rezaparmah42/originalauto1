<?php
$title = 'جزئیات تأمین‌کننده | ' . SITE_NAME;
$description = 'نمایش جزئیات تأمین‌کننده و محصولات تأمین‌شده.';
$canonical = SITE_URL . '/admin/suppliers/show/' . (int) ($supplier['id'] ?? 0);
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; flex-wrap:wrap;">
        <div>
            <h1><?= e($supplier['name'] ?? '-') ?></h1>
            <p>نمایش اطلاعات تماس و محصولات تأمین‌شده توسط این تأمین‌کننده.</p>
        </div>
        <a href="<?= SITE_URL ?>/admin/suppliers" class="btn-outline">بازگشت</a>
    </div>

    <div style="display:grid; gap:1rem;">
        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem;">
            <p><strong>نام تماس:</strong> <?= e($supplier['contact'] ?? '-') ?></p>
            <p><strong>تلفن:</strong> <?= e($supplier['phone'] ?? '-') ?></p>
            <p><strong>ایمیل:</strong> <?= e($supplier['email'] ?? '-') ?></p>
            <p><strong>یادداشت:</strong> <?= e($supplier['notes'] ?? '-') ?></p>
        </div>

        <div class="panel-card" style="background:#fff; border:1px solid #e6ebf1; border-radius:12px; padding:1rem;">
            <h2>محصولات تأمین‌شده</h2>
            <?php if (!empty($products)): ?>
                <ul>
                    <?php foreach ($products as $product): ?>
                        <li><?= e($product['title_fa'] ?? $product['title_en'] ?? '-') ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>هنوز محصولی برای این تأمین‌کننده ثبت نشده است.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
