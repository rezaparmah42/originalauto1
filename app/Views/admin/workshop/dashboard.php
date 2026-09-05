<?php
$title = 'داشبورد تعمیرگاه | ' . SITE_NAME;
$description = 'داشبورد تعمیرگاه برای بررسی تعمیرات فعال، قطعات کم‌موجود و فعالیت‌های اخیر.';
$canonical = SITE_URL . '/admin/workshop/dashboard';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__ . '/../../layouts/header.php'; ?>

<div class="container" style="padding:2rem 1rem 3rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem;">
        <div>
            <h1>داشبورد تعمیرگاه</h1>
            <p>نمایی از وضعیت امروز، خودروها و موجودی قطعات.</p>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/admin/workshop/repairs" class="btn-outline">لیست تعمیرات</a>
            <a href="<?= SITE_URL ?>/admin/dashboard" class="btn-outline">داشبورد اصلی</a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; margin-bottom:1.5rem;">
        <div class="stat-card">
            <div class="stat-title">تعمیرات جدید امروز</div>
            <div class="stat-value"><?= e($stats['today_new_repairs'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">تعمیرات فعال</div>
            <div class="stat-value"><?= e($stats['today_active_repairs'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">در انتظار قطعات</div>
            <div class="stat-value"><?= e($stats['waiting_parts'] ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">تکمیل شده</div>
            <div class="stat-value"><?= e($stats['completed_repairs'] ?? 0) ?></div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:1rem; margin-bottom:1.5rem;">
        <div class="panel-card">
            <h2>خودروهای پراستفاده</h2>
            <?php if (!empty($stats['most_repaired_vehicles'])): ?>
                <table class="table table-striped" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr><th>خودرو</th><th>تعداد تعمیرات</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stats['most_repaired_vehicles'] as $item): ?>
                            <tr>
                                <td><?= e(($item['brand'] ?? '-') . ' ' . ($item['model'] ?? '-')) ?></td>
                                <td><?= e((int) ($item['total_repairs'] ?? 0)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>هنوز داده‌ای برای خودروهای پرکار ثبت نشده است.</p>
            <?php endif; ?>
        </div>

        <div class="panel-card">
            <h2>مشتریان اخیر</h2>
            <?php if (!empty($stats['recent_customers'])): ?>
                <ul style="margin:0; padding-right:1.2rem;">
                    <?php foreach ($stats['recent_customers'] as $customer): ?>
                        <li style="margin-bottom:0.6rem;">
                            <strong><?= e($customer['name'] ?? '-') ?></strong>
                            <div style="color:#64748b;"><?= e($customer['phone'] ?? '-') ?></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>مشتری جدیدی وجود ندارد.</p>
            <?php endif; ?>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1.1fr 1fr; gap:1rem; margin-bottom:1.5rem;">
        <div class="panel-card">
            <h2>فعالیت اخیر تعمیرات</h2>
            <?php if (!empty($stats['recent_activity'])): ?>
                <ul style="margin:0; padding-right:1.2rem;">
                    <?php foreach ($stats['recent_activity'] as $activity): ?>
                        <li style="margin-bottom:0.7rem;">
                            <strong><?= e(($activity['brand'] ?? '-') . ' ' . ($activity['model'] ?? '-')) ?></strong>
                            <div style="color:#475569;">
                                <?= e($activity['customer_name'] ?? '-') ?> · <?= e($activity['status'] ?? '-') ?> · <?= e($activity['created_at'] ?? '-') ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>فعلاً فعالیت تعمیراتی ثبت نشده است.</p>
            <?php endif; ?>
        </div>

        <div class="panel-card">
            <h2>موجودی قطعات</h2>
            <h3>کم‌موجود</h3>
            <?php if (!empty($stats['low_stock_parts'])): ?>
                <ul style="margin:0 0 1rem; padding-right:1.2rem;">
                    <?php foreach (array_slice($stats['low_stock_parts'], 0, 5) as $part): ?>
                        <li><?= e($part['title_fa'] ?? $part['title_en'] ?? '-') ?> — <?= e((int) ($part['stock'] ?? 0)) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>قطعه‌ای در سطح کم‌موجود نیست.</p>
            <?php endif; ?>

            <h3>ناموجود</h3>
            <?php if (!empty($stats['unavailable_parts'])): ?>
                <ul style="margin:0; padding-right:1.2rem;">
                    <?php foreach (array_slice($stats['unavailable_parts'], 0, 5) as $part): ?>
                        <li><?= e($part['title_fa'] ?? $part['title_en'] ?? '-') ?> — <?= e((int) ($part['stock'] ?? 0)) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>هیچ قطعه‌ای ناموجود نیست.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
