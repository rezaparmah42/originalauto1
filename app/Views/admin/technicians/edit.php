<?php
$title = 'ویرایش تکنسین | ' . SITE_NAME;
?>
<?php require __DIR__.'/../../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>ویرایش تکنسین</h1>
    <form method="post" action="<?= SITE_URL ?>/admin/technicians/edit/<?= (int)($item['id'] ?? 0) ?>">
        <?= csrf_field() ?>
        <label>نام</label>
        <input name="name" value="<?= e($item['name'] ?? '') ?>" required>
        <label>تخصص</label>
        <input name="specialization" value="<?= e($item['specialization'] ?? '') ?>">
        <label>تلفن</label>
        <input name="phone" value="<?= e($item['phone'] ?? '') ?>">
        <label>وضعیت</label>
        <select name="status">
            <option value="active" <?= ($item['status'] ?? '') === 'active' ? 'selected' : '' ?>>فعال</option>
            <option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>غیرفعال</option>
        </select>
        <button class="btn-primary">بروز رسانی</button>
    </form>
</div>
<?php require __DIR__.'/../../layouts/footer.php'; ?>
