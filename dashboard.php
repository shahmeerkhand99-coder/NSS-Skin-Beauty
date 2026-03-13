<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireLogin();
$user = getLoggedUser();
$stats = [
    'orders' => db()->fetchOne("SELECT COUNT(*) as c FROM orders WHERE user_id=?",[$_SESSION['user_id']])['c'],
    'wishlist' => db()->fetchOne("SELECT COUNT(*) as c FROM wishlist WHERE user_id=?",[$_SESSION['user_id']])['c'],
    'spent' => db()->fetchOne("SELECT SUM(total) as s FROM orders WHERE user_id=? AND status != 'cancelled'",[$_SESSION['user_id']])['s'] ?? 0,
];
$recentOrders = db()->fetchAll("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 5",[$_SESSION['user_id']]);
$pageTitle = 'My Dashboard - NSS Skin & Beauty';
include '../includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>My Account</h1></div></div>
<section class="section">
  <div class="container">
    <div class="dashboard-layout">
      <?php include 'sidebar.php'; ?>
      <div>
        <div style="background:linear-gradient(135deg,var(--pink),var(--pink-dark));border-radius:16px;padding:28px;color:#fff;margin-bottom:24px">
          <h2 style="font-family:'Playfair Display',serif;margin-bottom:4px">Welcome back, <?= sanitize($user['name']) ?>! 💄</h2>
          <p style="opacity:0.85;font-size:0.9rem">Here's your beauty dashboard</p>
        </div>
        <div class="dashboard-stats-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:28px">
          <div class="stat-card"><div class="stat-card-icon pink"><i class="fas fa-box"></i></div><div><div class="stat-card-value"><?= $stats['orders'] ?></div><div class="stat-card-label">Total Orders</div></div></div>
          <div class="stat-card"><div class="stat-card-icon gold"><i class="far fa-heart"></i></div><div><div class="stat-card-value"><?= $stats['wishlist'] ?></div><div class="stat-card-label">Wishlist Items</div></div></div>
          <div class="stat-card"><div class="stat-card-icon green"><i class="fas fa-rupee-sign"></i></div><div><div class="stat-card-value"><?= formatPrice($stats['spent']) ?></div><div class="stat-card-label">Total Spent</div></div></div>
        </div>
        <div class="admin-card">
          <div class="admin-card-header"><h3>Recent Orders</h3><a href="orders.php" style="color:var(--pink);font-size:0.85rem">View All</a></div>
          <?php if ($recentOrders): ?>
          <table class="data-table" style="width:100%">
            <thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
            <tbody>
              <?php foreach ($recentOrders as $o): ?>
              <tr>
                <td><a href="order-detail.php?id=<?= $o['id'] ?>" style="color:var(--pink);font-weight:600"><?= $o['order_number'] ?></a></td>
                <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                <td><?= formatPrice($o['total']) ?></td>
                <td><span class="order-status status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php else: ?><p style="color:var(--gray-600);text-align:center;padding:30px 0">No orders yet. <a href="<?= SITE_URL ?>/shop.php" style="color:var(--pink)">Start shopping!</a></p><?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include '../includes/footer.php'; ?>
