<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$orderNum = $_GET['order'] ?? '';
$order = $orderNum ? db()->fetchOne("SELECT * FROM orders WHERE order_number=?",[$orderNum]) : null;
if (!$order) redirect(SITE_URL . '/');
$orderItems = db()->fetchAll("SELECT * FROM order_items WHERE order_id=?",[$order['id']]);
$pageTitle = 'Order Confirmed - NS Beauty';
include 'includes/header.php';
?>
<section class="section" style="max-width:640px;margin:0 auto;padding:60px 20px">
  <div style="text-align:center;margin-bottom:32px">
    <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--pink),var(--pink-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 8px 24px rgba(233,30,140,0.3)">
      <i class="fas fa-check" style="font-size:2rem;color:#fff"></i>
    </div>
    <h1 style="font-family:'Playfair Display',serif;font-size:2rem;margin-bottom:8px">Order Confirmed!</h1>
    <p style="color:var(--gray-600)">Thank you <strong><?= sanitize($order['full_name']) ?></strong>! Your order has been placed successfully.</p>
    <p style="margin-top:12px;background:var(--pink-light);padding:12px 20px;border-radius:10px;font-size:0.9rem">Order Number: <strong style="color:var(--pink)"><?= $order['order_number'] ?></strong></p>
  </div>
  <div class="form-card mb-24">
    <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:16px">Order Details</h3>
    <?php foreach ($orderItems as $item): ?>
    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--gray-100)">
      <img src="<?= $item['product_image'] ? UPLOAD_URL.'products/'.$item['product_image'] : 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=60&q=80' ?>" alt="" style="width:52px;height:52px;border-radius:8px;object-fit:cover">
      <div style="flex:1"><div style="font-weight:600;font-size:0.9rem"><?= sanitize($item['product_name']) ?></div><div style="font-size:0.78rem;color:var(--gray-600)">Qty: <?= $item['qty'] ?></div></div>
      <div style="font-weight:700;color:var(--pink)"><?= formatPrice($item['total']) ?></div>
    </div>
    <?php endforeach; ?>
    <div style="margin-top:16px">
      <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($order['subtotal']) ?></span></div>
      <?php if ($order['discount']>0): ?><div class="summary-row" style="color:#27ae60"><span>Discount</span><span>-<?= formatPrice($order['discount']) ?></span></div><?php endif; ?>
      <div class="summary-row"><span>Shipping</span><span><?= $order['shipping']>0 ? formatPrice($order['shipping']) : 'Free' ?></span></div>
      <div class="summary-row total"><span>Total Paid</span><span class="amount"><?= formatPrice($order['total']) ?></span></div>
    </div>
  </div>
  <div class="form-card mb-24 order-summary-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div><h4 style="font-size:0.88rem;font-weight:700;margin-bottom:8px">Shipping To</h4>
      <p style="font-size:0.85rem;color:var(--gray-600);line-height:1.6"><?= sanitize($order['address']) ?>, <?= sanitize($order['city']) ?>, <?= sanitize($order['state']) ?></p>
      <p style="font-size:0.85rem;color:var(--gray-600)"><?= sanitize($order['phone']) ?></p>
    </div>
    <div><h4 style="font-size:0.88rem;font-weight:700;margin-bottom:8px">Payment Method</h4>
      <?php $pm=['cod'=>'Cash on Delivery','card'=>'Credit/Debit Card','online'=>'Online Payment']; ?>
      <p style="font-size:0.85rem;color:var(--gray-600)"><?= $pm[$order['payment_method']] ?? $order['payment_method'] ?></p>
      <span class="order-status status-pending">Pending</span>
    </div>
  </div>
  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
    <?php if (isLoggedIn()): ?><a href="user/orders.php" class="btn btn-outline">View My Orders</a><?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
