<?php
$title = 'تکمیل خرید | ' . SITE_NAME;
$description = 'تکمیل اطلاعات سفارش و ثبت خرید.';
$canonical = SITE_URL . '/checkout';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<div class="container" style="padding: 2rem 1rem 3rem;">
    <h1>تکمیل خرید</h1>
    <p>اطلاعات سفارش را تکمیل کنید.</p>

    <form method="post" action="<?= SITE_URL ?>/orders/place" style="display:grid; gap:1rem; max-width:800px;">
        <?= csrf_field() ?>
        <div>
            <label>انتخاب خودرو</label>
            <select name="vehicle_id" style="width:100%; padding:0.7rem;">
                <option value="">بدون خودرو مشخص</option>
                <?php foreach ($vehicles ?? [] as $vehicle): ?>
                    <option value="<?= (int) ($vehicle['id'] ?? 0) ?>"><?= e($vehicle['model'] ?? $vehicle['name'] ?? '-') ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>آدرس تحویل</label>
            <textarea name="address" rows="4" required style="width:100%; padding:0.7rem;"></textarea>
        </div>
        <div>
            <label>محصولات سفارش</label>
            <ul>
                <?php foreach ($items ?? [] as $item): ?>
                    <li><?= e($item['title'] ?? '-') ?> × <?= (int) ($item['quantity'] ?? 1) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div><strong>جمع کل:</strong> <?= e($total ?? 0) ?></div>
        <button type="submit" class="btn-primary">ثبت سفارش</button>
    </form>
</div>

<?php require __DIR__.'/../layouts/footer.php'; ?>
