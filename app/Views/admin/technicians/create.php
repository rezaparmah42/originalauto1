<?php
$title = 'افزودن تکنسین | ' . SITE_NAME;
?>
<?php require __DIR__.'/../../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>افزودن تکنسین</h1>
    <form method="post" action="<?= SITE_URL ?>/admin/technicians/create">
        <?= csrf_field() ?>
        <label>نام</label>
        <input name="name" required>
        <label>تخصص</label>
        <input name="specialization">
        <label>تلفن</label>
        <input name="phone">
        <label>وضعیت</label>
        <select name="status"><option value="active">فعال</option><option value="inactive">غیرفعال</option></select>
        <button class="btn-primary">ذخیره</button>
    </form>
</div>
<?php require __DIR__.'/../../layouts/footer.php'; ?>
