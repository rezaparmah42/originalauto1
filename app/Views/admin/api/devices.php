<?php
$title = 'مدیریت دستگاه‌های API | ' . SITE_NAME;
$description = 'نمایش دستگاه‌های فعال API و مدیریت توکن‌ها.';
$canonical = SITE_URL . '/admin/api/devices';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__ . '/../../layouts/header.php'; ?>

<section class="admin-panel">
    <div class="container">
        <h1>دستگاه‌های API فعال</h1>
        <p>در این صفحه می‌توانید توکن‌های فعال روی دستگاه‌های API را بررسی و در صورت نیاز لغو کنید.</p>

        <form method="get" class="form-inline mb-3">
            <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="جستجو بر اساس کاربر یا نام توکن" class="form-control mr-2" />
            <select name="status" class="form-control mr-2">
                <option value="active" <?= (isset($filters['status']) && $filters['status'] === 'active') ? 'selected' : '' ?>>فعال</option>
                <option value="revoked" <?= (isset($filters['status']) && $filters['status'] === 'revoked') ? 'selected' : '' ?>>ابطال شده</option>
                <option value="all" <?= (isset($filters['status']) && $filters['status'] === 'all') ? 'selected' : '' ?>>همه</option>
            </select>
            <button class="btn btn-primary">اعمال</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>شناسه توکن</th>
                    <th>کاربر</th>
                    <th>نام دستگاه</th>
                    <th>پلتفرم</th>
                    <th>آخرین استفاده</th>
                    <th>انقضا</th>
                    <th>ثبت شده در</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($devices)): ?>
                    <?php foreach ($devices as $device): ?>
                        <tr>
                            <td><?= e($device['token_id']) ?></td>
                            <td><?= e($device['user_name'] ?? 'کاربر ناشناس') ?> (<?= e($device['user_id']) ?>)</td>
                            <td><?= e($device['device_name']) ?></td>
                            <td><?= e($device['platform']) ?></td>
                            <td><?= e($device['last_used_at'] ?? '-') ?></td>
                            <td><?= e($device['expires_at'] ?? '-') ?></td>
                            <td><?= e($device['created_at'] ?? '-') ?></td>
                            <td>
                                <?php if (empty($device['revoked'])): ?>
                                <form method="post" action="<?= SITE_URL ?>/admin/api/devices/revoke/<?= e($device['token_id']) ?>" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('آیا می‌خواهید این توکن را باطل کنید؟');">ابطال</button>
                                </form>
                                <?php else: ?>
                                    <span class="text-muted">ابطال شده</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8">هیچ دستگاه فعالی یافت نشد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($total)): ?>
            <?php $totalPages = (int) ceil($total / $perPage); ?>
            <nav>
                <ul class="pagination">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&per_page=<?= e($perPage) ?>&q=<?= urlencode($filters['q'] ?? '') ?>&status=<?= urlencode($filters['status'] ?? 'active') ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>
