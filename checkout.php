<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$cartItems = getCartItems();
if (!$cartItems) redirect(SITE_URL . '/cart.php');
$subtotal = getCartTotal($cartItems);
$discount = $_SESSION['coupon_discount'] ?? 0;
$shipping = $subtotal >= FREE_SHIPPING_MIN ? 0 : SHIPPING_CHARGE;
$total = $subtotal - $discount + $shipping;
$error = '';
$success = false;

if ($_POST) {
    $name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $state = sanitize($_POST['state'] ?? '');
    $zip = sanitize($_POST['zip'] ?? '');
    $payment = $_POST['payment_method'] ?? 'cod';
    $notes = sanitize($_POST['notes'] ?? '');
    if (!$name || !$email || !$phone || !$address || !$city) { $error = 'Please fill all required fields.'; }
    else {
        $orderNum = generateOrderNumber();
        $orderId = db()->insert("INSERT INTO orders (order_number,user_id,full_name,email,phone,address,city,state,zip,subtotal,discount,shipping,total,coupon_code,payment_method,notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
            [$orderNum, $_SESSION['user_id']??null, $name, $email, $phone, $address, $city, $state, $zip, $subtotal, $discount, $shipping, $total, $_SESSION['coupon_code']??null, $payment, $notes]);
        foreach ($cartItems as $item) {
            db()->insert("INSERT INTO order_items(order_id,product_id,product_name,product_image,price,qty,total) VALUES(?,?,?,?,?,?,?)",
                [$orderId, $item['product_id'], $item['name'], $item['image'], $item['unit_price'], $item['qty'], $item['unit_price']*$item['qty']]);
            db()->execute("UPDATE products SET stock=stock-? WHERE id=?",[$item['qty'],$item['product_id']]);
        }
        // Clear cart
        if (isLoggedIn()) db()->execute("DELETE FROM cart WHERE user_id=?",[$_SESSION['user_id']]);
        else db()->execute("DELETE FROM cart WHERE session_id=?",[getCartKey()]);
        unset($_SESSION['coupon_discount'], $_SESSION['coupon_code']);
        redirect(SITE_URL . '/order-confirmation.php?order=' . $orderNum);
    }
}
$pageTitle = 'Checkout - NS Beauty';
$userData = isLoggedIn() ? getLoggedUser() : [];
include 'includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>Secure Checkout</h1><nav class="breadcrumb"><a href="index.php">Home</a> <i class="fas fa-chevron-right" style="font-size:0.7rem"></i> <a href="cart.php">Cart</a> <i class="fas fa-chevron-right" style="font-size:0.7rem"></i> <span>Checkout</span></nav></div></div>
<section class="section">
  <div class="container">
    <?php if ($error): ?><div style="background:#fde8e8;color:#c0392b;padding:14px 20px;border-radius:10px;margin-bottom:24px;border-left:4px solid #c0392b"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
    <form method="POST">
    <div class="cart-layout">
      <div>
        <div class="form-card mb-24">
          <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin-bottom:20px"><i class="fas fa-map-marker-alt" style="color:var(--pink)"></i> Shipping Details</h3>
          <div class="form-row">
            <div class="form-group"><label>Full Name *</label><input type="text" name="full_name" class="form-control" value="<?= $userData['name']??'' ?>" required></div>
            <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" value="<?= $userData['email']??'' ?>" required></div>
          </div>
          <div class="form-group"><label>Phone *</label><input type="text" name="phone" class="form-control" value="<?= $userData['phone']??'' ?>" required></div>
          <div class="form-group"><label>Address *</label><input type="text" name="address" class="form-control" placeholder="Street address, house number" required></div>
          <div class="form-row">
            <div class="form-group"><label>City *</label><input type="text" name="city" class="form-control" required></div>
            <div class="form-group"><label>State</label><input type="text" name="state" class="form-control"></div>
          </div>
          <div class="form-row">
            <div class="form-group"><label>ZIP Code</label><input type="text" name="zip" class="form-control"></div>
            <div class="form-group"><label>Country</label><input type="text" name="country" class="form-control" value="Pakistan"></div>
          </div>
          <div class="form-group"><label>Order Notes (Optional)</label><textarea name="notes" class="form-control" rows="3" placeholder="Any special instructions?"></textarea></div>
        </div>
        <div class="form-card">
          <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem;margin-bottom:20px"><i class="fas fa-credit-card" style="color:var(--pink)"></i> Payment Method</h3>
          <label class="payment-option selected">
            <input type="radio" name="payment_method" value="cod" checked>
            <div class="payment-icon-box"><i class="fas fa-money-bill-wave"></i></div>
            <div><strong>Cash on Delivery</strong><div style="font-size:0.8rem;color:var(--gray-600)">Pay when your order arrives</div></div>
          </label>
          <label class="payment-option">
            <input type="radio" name="payment_method" value="card">
            <div class="payment-icon-box"><i class="fas fa-credit-card"></i></div>
            <div><strong>Credit / Debit Card</strong><div style="font-size:0.8rem;color:var(--gray-600)">Visa, Mastercard accepted</div></div>
          </label>
          <label class="payment-option">
            <input type="radio" name="payment_method" value="online">
            <div class="payment-icon-box"><i class="fas fa-mobile-alt"></i></div>
            <div><strong>Online Payment</strong><div style="font-size:0.8rem;color:var(--gray-600)">JazzCash, EasyPaisa, Bank Transfer</div></div>
          </label>
        </div>
      </div>
      <!-- Order Summary -->
      <div class="order-summary-box">
        <h3>Your Order</h3>
        <?php foreach ($cartItems as $item): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--gray-100)">
          <img src="<?= $item['image'] ? UPLOAD_URL.'products/'.$item['image'] : 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=60&q=80' ?>" alt="" style="width:52px;height:52px;border-radius:8px;object-fit:cover">
          <div style="flex:1">
            <div style="font-size:0.84rem;font-weight:600"><?= sanitize($item['name']) ?></div>
            <div style="font-size:0.78rem;color:var(--gray-600)">x<?= $item['qty'] ?></div>
          </div>
          <div style="font-weight:700;font-size:0.9rem;color:var(--pink)"><?= formatPrice($item['unit_price']*$item['qty']) ?></div>
        </div>
        <?php endforeach; ?>
        <div class="summary-row mt-16"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
        <?php if ($discount>0): ?><div class="summary-row" style="color:#27ae60"><span>Coupon Discount</span><span>-<?= formatPrice($discount) ?></span></div><?php endif; ?>
        <div class="summary-row"><span>Shipping</span><span><?= $shipping>0 ? formatPrice($shipping) : '<span style="color:#27ae60">Free</span>' ?></span></div>
        <div class="summary-row total"><span>Total</span><span class="amount"><?= formatPrice($total) ?></span></div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:16px;font-size:1rem;padding:15px">
          <i class="fas fa-lock"></i> Place Order
        </button>
        <p style="font-size:0.75rem;color:var(--gray-600);text-align:center;margin-top:10px"><i class="fas fa-shield-alt"></i> Your data is protected with SSL encryption</p>
      </div>
    </div>
    </form>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
