<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$cartItems = getCartItems();
$subtotal = getCartTotal($cartItems);
$shipping = $subtotal >= FREE_SHIPPING_MIN ? 0 : SHIPPING_CHARGE;
$discount = $_SESSION['coupon_discount'] ?? 0;
$total = $subtotal - $discount + $shipping;
$pageTitle = 'Shopping Cart - NS Beauty';
include 'includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>Shopping Cart</h1><nav class="breadcrumb"><a href="index.php">Home</a> <i class="fas fa-chevron-right" style="font-size:0.7rem"></i> <span>Cart</span></nav></div></div>
<section class="section">
  <div class="container">
    <?php if ($cartItems): ?>
    <div class="cart-layout">
      <div>
        <table class="cart-table">
          <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
          <tbody>
          <?php foreach ($cartItems as $item): ?>
          <tr>
            <td>
              <div class="cart-product">
                <img src="<?= $item['image'] ? UPLOAD_URL.'products/'.$item['image'] : 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=100&q=80' ?>" alt="<?= sanitize($item['name']) ?>">
                <div>
                  <a href="product.php?slug=<?= $item['slug'] ?>" style="font-weight:600;font-size:0.9rem"><?= sanitize($item['name']) ?></a>
                </div>
              </div>
            </td>
            <td><?= formatPrice($item['unit_price']) ?></td>
            <td>
              <div class="qty-control">
                <button class="qty-btn" onclick="updateCartQty(<?= $item['id'] ?>,<?= $item['qty']-1 ?>)">−</button>
                <span class="qty-value"><?= $item['qty'] ?></span>
                <button class="qty-btn" onclick="updateCartQty(<?= $item['id'] ?>,<?= $item['qty']+1 ?>)">+</button>
              </div>
            </td>
            <td style="font-weight:700;color:var(--pink)"><?= formatPrice($item['unit_price'] * $item['qty']) ?></td>
            <td><button class="btn btn-delete btn-sm" onclick="removeFromCart(<?= $item['id'] ?>)"><i class="fas fa-times"></i></button></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <div style="display:flex;justify-content:space-between;margin-top:20px;flex-wrap:wrap;gap:12px">
          <a href="shop.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
        </div>
      </div>
      <!-- Summary -->
      <div class="order-summary-box">
        <h3>Order Summary</h3>
        <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
        <?php if ($discount > 0): ?><div class="summary-row" style="color:#27ae60"><span>Discount</span><span>-<?= formatPrice($discount) ?></span></div><?php endif; ?>
        <div class="summary-row"><span>Shipping</span><span><?= $shipping > 0 ? formatPrice($shipping) : '<span style="color:#27ae60">Free</span>' ?></span></div>
        <?php if ($subtotal < FREE_SHIPPING_MIN): ?>
        <p style="font-size:0.78rem;color:var(--gray-600);margin:4px 0">Add <?= formatPrice(FREE_SHIPPING_MIN - $subtotal) ?> more for free shipping!</p>
        <?php endif; ?>
        <div class="summary-row total"><span>Total</span><span class="amount"><?= formatPrice($total) ?></span></div>
        <!-- Coupon -->
        <div style="margin-top:16px">
          <div class="coupon-form" id="couponForm">
            <input type="text" id="couponCode" placeholder="Enter coupon code" <?= isset($_SESSION['coupon_code']) ? 'value="'.$_SESSION['coupon_code'].'" disabled' : '' ?>>
            <button type="button" onclick="applyCoupon()"><?= isset($_SESSION['coupon_code']) ? 'Applied ✓' : 'Apply' ?></button>
          </div>
          <?php if (isset($_SESSION['coupon_code'])): ?>
          <p style="font-size:0.8rem;color:#27ae60"><i class="fas fa-check-circle"></i> Coupon <strong><?= $_SESSION['coupon_code'] ?></strong> applied!</p>
          <button onclick="removeCoupon()" style="font-size:0.78rem;color:var(--pink);background:none;border:none;cursor:pointer">Remove coupon</button>
          <?php endif; ?>
        </div>
        <a href="checkout.php" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:16px"><i class="fas fa-lock"></i> Proceed to Checkout</a>
      </div>
    </div>
    <?php else: ?>
    <div style="text-align:center;padding:100px 0">
      <i class="fas fa-shopping-bag" style="font-size:4rem;color:var(--gray-400);margin-bottom:20px;display:block"></i>
      <h2 style="margin-bottom:12px">Your cart is empty</h2>
      <p style="color:var(--gray-600);margin-bottom:24px">Looks like you haven't added anything yet.</p>
      <a href="shop.php" class="btn btn-primary">Start Shopping</a>
    </div>
    <?php endif; ?>
  </div>
</section>
<script>
async function updateCartQty(id, qty) {
    if(qty < 1) { removeFromCart(id); return; }
    await fetch('ajax/cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=update&cart_id=${id}&qty=${qty}`});
    location.reload();
}
async function removeFromCart(id) {
    await fetch('ajax/cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=remove&cart_id=${id}`});
    location.reload();
}
async function applyCoupon() {
    const code = document.getElementById('couponCode')?.value;
    if(!code) return;
    const res = await fetch('ajax/coupon.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`code=${encodeURIComponent(code)}&subtotal=<?= $subtotal ?>`});
    const data = await res.json();
    showToast(data.message, data.valid?'success':'error');
    if(data.valid) setTimeout(()=>location.reload(),800);
}
async function removeCoupon() {
    await fetch('ajax/coupon.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:'action=remove'});
    location.reload();
}
</script>
<?php include 'includes/footer.php'; ?>
