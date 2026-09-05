<?php
$title = 'دانش تشخیصی | ' . SITE_NAME;
$description = 'مدیریت دانش تشخیصی (DTC).';
$canonical = SITE_URL . '/admin/ai-knowledge';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <h1>دانش تشخیصی</h1>
    <a href="<?= SITE_URL ?>/admin/ai-knowledge/create" class="btn-primary">افزودن مورد</a>

    <table style="width:100%; border-collapse:collapse; margin-top:1rem; background:#fff;">
        <thead>
            <tr>
                <th>کد DTC</th>
                <th>عنوان</th>
                <th>شدت</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?= e($it['dtc_code'] ?? '-') ?></td>
                        <td><?= e($it['title'] ?? '-') ?></td>
                        <td><?= e($it['severity'] ?? '-') ?></td>
                        <td><a href="<?= SITE_URL ?>/admin/ai-knowledge/edit/<?= (int) ($it['id'] ?? 0) ?>">ویرایش</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4">موردی یافت نشد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__.'/../../layouts/footer.php'; ?>
