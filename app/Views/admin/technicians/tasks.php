<?php
$title = 'وظایف کارگاهی | ' . SITE_NAME;
?>
<?php require __DIR__.'/../../layouts/header.php'; ?>
<div class="container" style="padding:2rem;">
    <h1>وظایف کارگاهی</h1>
    <table style="width:100%; background:#fff;">
        <thead><tr><th>عنوان</th><th>تعمیر</th><th>تکنسین</th><th>وضعیت</th></tr></thead>
        <tbody>
            <?php if (!empty($items)): foreach ($items as $it): ?>
                <tr>
                    <td><?= e($it['title']) ?></td>
                    <td><?= e($it['repair_id']) ?></td>
                    <td><?= e($it['technician_id']) ?></td>
                    <td><?= e($it['status']) ?></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4">موردی یافت نشد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__.'/../../layouts/footer.php'; ?>
