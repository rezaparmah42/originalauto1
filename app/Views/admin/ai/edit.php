<?php
$title = 'ویرایش دانش تشخیصی | ' . SITE_NAME;
$description = 'ویرایش یک ورودی از دانش تشخیصی.';
$canonical = SITE_URL . '/admin/ai-knowledge/edit/' . (int) ($item['id'] ?? 0);
$robots = 'noindex, nofollow';
?>
<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>ویرایش مورد</h1>

    <form method="post" action="<?= SITE_URL ?>/admin/ai-knowledge/edit/<?= (int) ($item['id'] ?? 0) ?>">
        <?= csrf_field() ?>
        <label>کد DTC</label>
        <input type="text" name="dtc_code" value="<?= e($item['dtc_code'] ?? '') ?>" style="width:100%; padding:0.6rem; margin:0.5rem 0;" required>

        <label>عنوان</label>
        <input type="text" name="title" value="<?= e($item['title'] ?? '') ?>" style="width:100%; padding:0.6rem; margin:0.5rem 0;" required>

        <label>شرح</label>
        <textarea name="description" rows="4" style="width:100%; padding:0.6rem; margin:0.5rem 0;"><?= e($item['description'] ?? '') ?></textarea>

        <label>شدت</label>
        <select name="severity" style="width:100%; padding:0.6rem; margin:0.5rem 0;">
            <option value="unknown" <?= ($item['severity'] ?? '') === 'unknown' ? 'selected' : '' ?>>نامشخص</option>
            <option value="low" <?= ($item['severity'] ?? '') === 'low' ? 'selected' : '' ?>>کم</option>
            <option value="medium" <?= ($item['severity'] ?? '') === 'medium' ? 'selected' : '' ?>>متوسط</option>
            <option value="high" <?= ($item['severity'] ?? '') === 'high' ? 'selected' : '' ?>>زیاد</option>
            <option value="critical" <?= ($item['severity'] ?? '') === 'critical' ? 'selected' : '' ?>>حیاتی</option>
        </select>

        <label>دلایل احتمالی</label>
        <textarea name="possible_causes" rows="3" style="width:100%; padding:0.6rem; margin:0.5rem 0;"><?= e($item['possible_causes'] ?? '') ?></textarea>

        <label>اقدامات پیشنهادی</label>
        <textarea name="recommended_actions" rows="3" style="width:100%; padding:0.6rem; margin:0.5rem 0;"><?= e($item['recommended_actions'] ?? '') ?></textarea>

        <button type="submit" class="btn-primary">بروز رسانی</button>
    </form>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
