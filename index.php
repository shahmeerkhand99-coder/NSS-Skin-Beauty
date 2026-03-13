<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireAdmin();

$stats = [
    'revenue' => db()->fetchOne("SELECT SUM(total) as s FROM orders WHERE status!='cancelled'")['s'] ?? 0,
    'orders' => db()->fetchOne("SELECT COUNT(*) as c FROM orders")['c'],
    'products' => db()->fetchOne("SELECT COUNT(*) as c FROM products WHERE status='active'")['c'],
    'users' => db()->fetchOne("SELECT COUNT(*) as c FROM users WHERE role='customer'")['c'],
];

// Fetch sales data for the last 7 days
$salesData = db()->fetchAll("
    SELECT DATE(created_at) as date, SUM(total) as total 
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
    AND status != 'cancelled'
    GROUP BY DATE(created_at) 
    ORDER BY date ASC
");

$chartLabels = [];
$chartValues = [];
$currentDate = new DateTime('-6 days');
for($i=0; $i<7; $i++) {
    $dateStr = $currentDate->format('Y-0m-d'); // Database format is YYYY-MM-DD
    $chartLabels[] = $currentDate->format('D');
    $val = 0;
    foreach($salesData as $sd) {
        if($sd['date'] == $dateStr) {
            $val = $sd['total'];
            break;
        }
    }
    $chartValues[] = $val;
    $currentDate->modify('+1 day');
}

$recentOrders = db()->fetchAll("SELECT o.*, u.name as user_name FROM orders o LEFT JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 8");
$lowStock = db()->fetchAll("SELECT * FROM products WHERE stock <= 10 AND status='active' ORDER BY stock ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Dashboard - NSS Skin & Beauty Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f8fafc; }
        .stat-card-premium { background: #fff; padding: 24px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid var(--gray-100); display: flex; align-items: center; gap: 20px; transition: transform 0.3s ease; }
        .stat-card-premium:hover { transform: translateY(-5px); }
        .stat-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .chart-container { background: #fff; padding: 24px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: 1px solid var(--gray-100); margin-top: 24px; }
    </style>
</head>
<body>
<div class="admin-body">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include 'includes/topbar.php'; ?>
        <div class="admin-content">
            <!-- Stats -->
            <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                <div class="stat-card-premium">
                    <div class="stat-icon" style="background:var(--pink-light);color:var(--pink)"><i class="fas fa-wallet"></i></div>
                    <div>
                        <div style="font-size:0.85rem;color:var(--gray-500);font-weight:600">Total Revenue</div>
                        <div style="font-size:1.5rem;font-weight:800;color:var(--dark)"><?= CURRENCY_SYMBOL.' '.number_format($stats['revenue'], 0) ?></div>
                    </div>
                </div>
                <div class="stat-card-premium">
                    <div class="stat-icon" style="background:#e0f2fe;color:#0369a1"><i class="fas fa-shopping-bag"></i></div>
                    <div>
                        <div style="font-size:0.85rem;color:var(--gray-500);font-weight:600">Total Orders</div>
                        <div style="font-size:1.5rem;font-weight:800;color:var(--dark)"><?= $stats['orders'] ?></div>
                    </div>
                </div>
                <div class="stat-card-premium">
                    <div class="stat-icon" style="background:#fef3c7;color:#b45309"><i class="fas fa-box"></i></div>
                    <div>
                        <div style="font-size:0.85rem;color:var(--gray-500);font-weight:600">Active Products</div>
                        <div style="font-size:1.5rem;font-weight:800;color:var(--dark)"><?= $stats['products'] ?></div>
                    </div>
                </div>
                <div class="stat-card-premium">
                    <div class="stat-icon" style="background:#f1f5f9;color:var(--gray-700)"><i class="fas fa-users"></i></div>
                    <div>
                        <div style="font-size:0.85rem;color:var(--gray-500);font-weight:600">Customers</div>
                        <div style="font-size:1.5rem;font-weight:800;color:var(--dark)"><?= $stats['users'] ?></div>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns: 1fr 340px; gap: 24px; margin-top: 24px;">
                <div>
                    <!-- Chart -->
                    <div class="chart-container">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                            <h3 style="font-family:'Playfair Display'">Sales Analytics</h3>
                            <span style="font-size:0.75rem;background:var(--gray-100);padding:4px 12px;border-radius:20px;font-weight:700">Last 7 Days</span>
                        </div>
                        <canvas id="salesChart" height="120"></canvas>
                    </div>

                    <!-- Recent Orders -->
                    <div class="admin-card shadow-premium mt-24">
                        <div class="admin-card-header">
                            <h3>Recent Orders</h3>
                            <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $o): ?>
                                        <tr>
                                            <td style="font-weight:700">#<?= $o['order_number'] ?></td>
                                            <td><?= sanitize($o['user_name'] ?? $o['full_name']) ?></td>
                                            <td style="color:var(--pink);font-weight:700"><?= formatPrice($o['total']) ?></td>
                                            <td><span class="order-status status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
                                            <td class="table-actions">
                                                <a href="orders.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-edit"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:24px">
                    <!-- Quick Actions -->
                    <div class="admin-card shadow-premium">
                        <h3 style="margin-bottom:16px">Quick Actions</h3>
                        <div style="display:grid;grid-template-columns:1fr;gap:12px">
                            <a href="products.php?action=add" class="btn btn-primary" style="text-align:center;justify-content:center;padding:12px"><i class="fas fa-plus"></i> Add New Product</a>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                <a href="coupons.php" class="btn btn-outline btn-sm" style="text-align:center;justify-content:center"><i class="fas fa-tag"></i> Coupon</a>
                                <a href="banners.php" class="btn btn-outline btn-sm" style="text-align:center;justify-content:center"><i class="fas fa-image"></i> Banner</a>
                            </div>
                            <a href="<?= SITE_URL ?>" target="_blank" class="btn btn-outline btn-sm" style="text-align:center;justify-content:center"><i class="fas fa-external-link-alt"></i> View Store</a>
                        </div>
                    </div>

                    <!-- Low Stock Alerts -->
                    <?php if ($lowStock): ?>
                    <div class="admin-card shadow-premium">
                        <div class="admin-card-header">
                            <h3 style="color:#cf1322"><i class="fas fa-exclamation-triangle"></i> Low Stock</h3>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:12px">
                            <?php foreach($lowStock as $ls): ?>
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;background:#fff1f0;border-radius:10px;border:1px solid #ffa39e">
                                    <div style="font-size:0.82rem;font-weight:600;max-width:140px;overflow:hidden;text-overflow:ellipsis"><?= $ls['name'] ?></div>
                                    <div style="font-size:0.75rem;font-weight:800;color:#cf1322"><?= $ls['stock'] ?> left</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Daily Sales (<?= CURRENCY_SYMBOL ?>)',
                data: <?= json_encode($chartValues) ?>,
                borderColor: '#c99b67',
                backgroundColor: 'rgba(201, 155, 103, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#c99b67',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) { return '<?= CURRENCY_SYMBOL ?> ' + value; }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
