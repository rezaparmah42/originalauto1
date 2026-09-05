<?php
$title = 'گزارش‌های API | ' . SITE_NAME;
$description = 'نمایش فعالیت‌های API و گزارش‌های درخواست‌ها.';
$canonical = SITE_URL . '/admin/api/logs';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__ . '/../../layouts/header.php'; ?>

<section class="admin-panel">
    <div class="container">
        <h1>گزارش‌های API</h1>
        <p>لیست آخرین درخواست‌های API با جزئیات وضعیت پاسخ و کاربر مرتبط.</p>
        <form method="get" class="form-inline mb-3">
            <input type="text" name="endpoint" value="<?= e($filters['endpoint'] ?? '') ?>" placeholder="فیلتر بر اساس endpoint" class="form-control mr-2" />
            <input type="number" name="response_code" value="<?= e($filters['response_code'] ?? '') ?>" placeholder="کد پاسخ" class="form-control mr-2" />
            <select name="sort" class="form-control mr-2">
                <option value="desc" <?= (isset($filters['sort']) && $filters['sort'] === 'desc') ? 'selected' : '' ?>>جدیدترین</option>
                <option value="asc" <?= (isset($filters['sort']) && $filters['sort'] === 'asc') ? 'selected' : '' ?>>قدیمی‌ترین</option>
            </select>
            <button class="btn btn-primary">اعمال</button>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>زمان</th>
                    <th>کاربر</th>
                    <th>endpoint</th>
                    <th>متد</th>
                    <th>IP</th>
                    <th>کد پاسخ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= e($log['created_at']) ?></td>
                            <td><?= e($log['user_name'] ?? 'ناشناس') ?> <?= e(!empty($log['user_email']) ? '(' . $log['user_email'] . ')' : '') ?></td>
                            <td><?= e($log['endpoint']) ?></td>
                            <td><?= e($log['method']) ?></td>
                            <td><?= e($log['ip_address']) ?></td>
                            <td><?= e($log['response_code']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">هیچ گزارشی یافت نشد.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (!empty($total)): ?>
            <?php $totalPages = (int) ceil($total / $perPage); ?>
            <nav>
                <ul class="pagination">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>&per_page=<?= e($perPage) ?>&endpoint=<?= urlencode($filters['endpoint'] ?? '') ?>&response_code=<?= urlencode($filters['response_code'] ?? '') ?>&sort=<?= urlencode($filters['sort'] ?? 'desc') ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</section>
