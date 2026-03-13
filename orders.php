<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireLogin();
$user = getLoggedUser();
$page = max(1,(int)($_GET['page']??1));
$offset = ($page - 1) * 10;
$orders = db()->fetchAll("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 10 OFFSET $offset", [$_SESSION['user_id']]);
$total = db()->fetchOne("SELECT COUNT(*) as c FROM orders WHERE user_id=?",[$_SESSION['user_id']])['c'];
$pageTitle = 'My Orders - NSS Skin & Beauty';
include '../includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>My Orders</h1></div></div>
<section class="section"><div class="container"><div class="dashboard-layout">
  <?php include 'sidebar.php'; ?>
  <div class="admin-card">
    <h3 style="margin-bottom:20px">Order History</h3>
    <?php if ($orders): ?>
    <table class="data-table">
      <thead><tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): 
          $itemCount = db()->fetchOne("SELECT COUNT(*) as c FROM order_items WHERE order_id=?",[$o['id']])['c'];
        ?>
        <tr>
          <td><strong style="color:var(--pink)"><?= $o['order_number'] ?></strong></td>
          <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
          <td><?= $itemCount ?> item(s)</td>
          <td><strong><?= formatPrice($o['total']) ?></strong></td>
          <td><?= ucfirst($o['payment_method']) ?></td>
          <td><span class="order-status status-<?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div style="text-align:center;padding:60px 0;color:var(--gray-600)">
      <i class="fas fa-box-open" style="font-size:3rem;margin-bottom:16px;display:block;color:var(--gray-400)"></i>
      <p>No orders yet. <a href="<?= SITE_URL ?>/shop.php" style="color:var(--pink)">Start shopping!</a></p>
    </div>
    <?php endif; ?>
  </div>
</div></div></section>
<?php include '../includes/footer.php'; ?>
