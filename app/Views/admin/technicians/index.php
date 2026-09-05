<?php
$title = 'تکنسین‌ها | ' . SITE_NAME;
?>
<?php require __DIR__.'/../../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>تکنسین‌ها</h1>
    <a href="<?= SITE_URL ?>/admin/technicians/create" class="btn-primary">افزودن تکنسین</a>
    <table style="width:100%; margin-top:1rem; background:#fff;">
        <thead><tr><th>نام</th><th>تخصص</th><th>تلفن</th><th>وضعیت</th><th>عملیات</th></tr></thead>
        <tbody>
            <?php if (!empty($items)): foreach ($items as $it): ?>
                <tr>
                    <td><?= e($it['name']) ?></td>
                    <td><?= e($it['specialization']) ?></td>
                    <td><?= e($it['phone']) ?></td>
                    <td><?= e($it['status']) ?></td>
                    <td><a href="<?= SITE_URL ?>/admin/technicians/edit/<?= (int)$it['id'] ?>">ویرایش</a></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5">تکنسینی یافت نشد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__.'/../../layouts/footer.php'; ?>
