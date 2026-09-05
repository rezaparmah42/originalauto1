<?php
$title = 'افزودن دانش تشخیصی | ' . SITE_NAME;
$description = 'ایجاد یک ورودی جدید در دانش تشخیصی.';
$canonical = SITE_URL . '/admin/ai-knowledge/create';
$robots = 'noindex, nofollow';
?>
<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>افزودن مورد جدید</h1>

    <form method="post" action="<?= SITE_URL ?>/admin/ai-knowledge/create">
        <?= csrf_field() ?>
        <label>کد DTC</label>
        <input type="text" name="dtc_code" style="width:100%; padding:0.6rem; margin:0.5rem 0;" required>

        <label>عنوان</label>
        <input type="text" name="title" style="width:100%; padding:0.6rem; margin:0.5rem 0;" required>

        <label>شرح</label>
        <textarea name="description" rows="4" style="width:100%; padding:0.6rem; margin:0.5rem 0;"></textarea>

        <label>شدت</label>
        <select name="severity" style="width:100%; padding:0.6rem; margin:0.5rem 0;">
            <option value="unknown">نامشخص</option>
            <option value="low">کم</option>
            <option value="medium">متوسط</option>
            <option value="high">زیاد</option>
            <option value="critical">حیاتی</option>
        </select>

        <label>دلایل احتمالی</label>
        <textarea name="possible_causes" rows="3" style="width:100%; padding:0.6rem; margin:0.5rem 0;"></textarea>

        <label>اقدامات پیشنهادی</label>
        <textarea name="recommended_actions" rows="3" style="width:100%; padding:0.6rem; margin:0.5rem 0;"></textarea>

        <button type="submit" class="btn-primary">ذخیره</button>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
