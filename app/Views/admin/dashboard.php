<?php
$title = 'داشبورد مدیریت | ' . SITE_NAME;
$description = 'داشبورد مدیریتی برای نظارت بر کاربران، رزروها، سفارش‌ها و وضعیت خدمات تعمیرگاه.';
$canonical = SITE_URL . '/admin/dashboard';
$robots = 'noindex, nofollow';
?>

<?php require __DIR__.'/../layouts/header.php'; ?>

<section class="admin-dashboard">
    <div class="dashboard-shell">
        <div class="dashboard-header">
            <div>
                <h1>داشبورد مدیریت</h1>
                <p>نمایی از عملکرد تعمیرگاه و فعالیت‌های اخیر.</p>
            </div>
            <div class="dashboard-summary">
                <p><strong>مدیر:</strong> <?= e($adminSession['name'] ?? 'مدیر') ?></p>
                <p><strong>ایمیل:</strong> <?= e($adminSession['email'] ?? '-') ?></p>
                <p><strong>تاریخ:</strong> <?= e($today ?? date('Y-m-d')) ?></p>
                <p><strong>وضعیت سیستم:</strong> <?= e($status ?? 'Operational') ?></p>
            </div>
        </div>

        <div class="dashboard-grid">
            <?php
            $statLabels = [
                'users' => 'کاربران',
                'products' => 'محصولات',
                'services' => 'خدمات',
                'bookings' => 'رزروها',
                'repairs' => 'تعمیرات',
                'orders' => 'سفارش‌ها',
                'low_stock_products' => 'کم‌موجود',
                'out_of_stock_products' => 'تمام‌شده',
                'suppliers' => 'تأمین‌کنندگان',
            ];
            ?>

            <?php foreach ($statLabels as $key => $label): ?>
                <div class="stat-card">
                    <div class="stat-title"><?= e($label) ?></div>
                    <div class="stat-value"><?= e($stats[$key] ?? 0) ?></div>
                </div>
            <?php endforeach; ?>

            <div class="stat-card">
                <div class="stat-title">محتوای دانش AI</div>
                <div class="stat-value"><?= e($aiStats['knowledge_count'] ?? 0) ?></div>
            </div>

            <div class="stat-card">
                <div class="stat-title">رزروهای در انتظار</div>
                <div class="stat-value"><?= e($stats['pending_bookings'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">سفارش‌های در انتظار</div>
                <div class="stat-value"><?= e($stats['pending_orders'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">درآمد</div>
                <div class="stat-value"><?= e($stats['revenue'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">سفارش‌های پرداخت‌شده</div>
                <div class="stat-value"><?= e($stats['paid_orders'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">پرداخت‌های معوق</div>
                <div class="stat-value"><?= e($stats['pending_payments'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">پرداخت‌های ناموفق</div>
                <div class="stat-value"><?= e($stats['failed_payments'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">تعمیرات فعال</div>
                <div class="stat-value"><?= e($stats['active_repairs'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">اسکن‌های تشخیصی</div>
                <div class="stat-value"><?= e($diagnosticStats['total_scans'] ?? 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">خطاهای فعال</div>
                <div class="stat-value"><?= e($diagnosticStats['active_faults'] ?? 0) ?></div>
            </div>
        </div>

        <div class="dashboard-panels">
            <div class="panel-card">
                <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                    <h2>کاربران اخیر</h2>
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                        <a href="<?= SITE_URL ?>/admin/articles" class="btn-outline">مدیریت مقالات</a>
                        <a href="<?= SITE_URL ?>/admin/products" class="btn-outline">مدیریت محصولات</a>
                        <a href="<?= SITE_URL ?>/admin/workshop/repairs" class="btn-outline">تعمیرگاه</a>
                        <a href="<?= SITE_URL ?>/admin/vehicles/intelligence" class="btn-outline">هوش خودرو</a>
                    </div>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>نام</th>
                            <th>ایمیل</th>
                            <th>تلفن</th>
                            <th>نقش</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentUsers)): ?>
                            <?php foreach ($recentUsers as $user): ?>
                                <tr>
                                    <td><?= e($user['name'] ?? '-') ?></td>
                                    <td><?= e($user['email'] ?? '-') ?></td>
                                    <td><?= e($user['phone'] ?? '-') ?></td>
                                    <td><?= e($user['role'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">هنوز کاربری ثبت نشده است.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel-card">
                <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                    <h2>رزروهای اخیر</h2>
                    <a href="<?= SITE_URL ?>/admin/bookings" class="btn-outline">مدیریت رزروها</a>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>کاربر</th>
                            <th>خدمت</th>
                            <th>وضعیت</th>
                            <th>تاریخ رزرو</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentBookings)): ?>
                            <?php foreach ($recentBookings as $booking): ?>
                                <tr>
                                    <td><?= e($booking['user_name'] ?? '-') ?></td>
                                    <td><?= e($booking['service_name'] ?? '-') ?></td>
                                    <td><?= e($booking['status'] ?? '-') ?></td>
                                    <td><?= e($booking['booking_date'] ?? $booking['created_at'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">رزروی موجود نیست.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel-card">
                <div class="panel-header" style="display:flex; justify-content:space-between; align-items:center; gap:1rem;">
                    <h2>تشخیص‌های اخیر</h2>
                    <a href="<?= SITE_URL ?>/admin/diagnostics" class="btn-outline">مشاهده همه</a>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>جلسه</th>
                            <th>خودرو</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentDiagnostics)): ?>
                            <?php foreach ($recentDiagnostics as $item): ?>
                                <tr>
                                    <td>#<?= e($item['id'] ?? '-') ?></td>
                                    <td><?= e($item['vehicle_model'] ?? '-') ?></td>
                                    <td><?= e($item['created_at'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">تشخیصی ثبت نشده است.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel-card">
                <div class="panel-header">
                    <h2>سفارش‌های اخیر</h2>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>کد</th>
                            <th>کاربر</th>
                            <th>وضعیت</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td>#<?= e($order['id'] ?? '-') ?></td>
                                    <td><?= e($order['user_name'] ?? '-') ?></td>
                                    <td><?= e($order['status'] ?? '-') ?></td>
                                    <td><?= e($order['created_at'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4">سفارشی موجود نیست.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<style>
.admin-dashboard { padding: 2rem 1rem 3rem; }
.dashboard-shell { max-width: 1200px; margin: 0 auto; }
.dashboard-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 1.5rem; margin-bottom: 1.5rem; }
.dashboard-summary { background: #f7f9fc; border: 1px solid #e6ebf1; padding: 1rem 1.2rem; border-radius: 12px; min-width: 280px; }
.dashboard-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.stat-card { background: linear-gradient(135deg, #0d1724, #184a8d); color: #fff; border-radius: 14px; padding: 1rem 1.1rem; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
.stat-title { font-size: 0.95rem; opacity: 0.9; margin-bottom: 0.4rem; }
.stat-value { font-size: 2rem; font-weight: 700; }
.dashboard-panels { display: grid; gap: 1rem; }
.panel-card { background: #fff; border: 1px solid #e6ebf1; border-radius: 14px; padding: 1rem 1.1rem; box-shadow: 0 6px 16px rgba(0,0,0,0.04); }
.panel-header h2 { margin: 0 0 0.8rem; font-size: 1.1rem; }
.dashboard-table { width: 100%; border-collapse: collapse; }
.dashboard-table th, .dashboard-table td { padding: 0.7rem 0.5rem; border-bottom: 1px solid #eef2f6; text-align: right; font-size: 0.95rem; }
.dashboard-table th { color: #4a5568; }
</style>

<?php require __DIR__.'/../layouts/footer.php'; ?>