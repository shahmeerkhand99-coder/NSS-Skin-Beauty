<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
requireLogin();
$wishlistItems = db()->fetchAll("SELECT w.*, p.name, p.slug, p.image, p.price, p.sale_price, p.stock FROM wishlist w JOIN products p ON p.id=w.product_id WHERE w.user_id=?",[$_SESSION['user_id']]);
$pageTitle = 'My Wishlist - NSS Skin & Beauty';
include 'includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>My Wishlist</h1><nav class="breadcrumb"><a href="index.php">Home</a> <i class="fas fa-chevron-right" style="font-size:0.7rem"></i> <span>Wishlist</span></nav></div></div>
<section class="section"><div class="container">
<?php if ($wishlistItems): ?>
<div class="products-grid">
  <?php foreach ($wishlistItems as $item):
    $product = $item;
    // Ensure product id is the actual product ID, not wishlist row ID
    $product['id'] = $item['product_id'];
    $product['category_name'] = '';
    $product['avg_rating'] = 0;
    $product['review_count'] = 0;
    $product['discount_percent'] = $item['sale_price'] ? round((($item['price']-$item['sale_price'])/$item['price'])*100) : 0;
    $product['is_new'] = 0;
    echo renderProductCard($product, [$item['product_id']]);
  endforeach; ?>
</div>
<?php else: ?>
<div style="text-align:center;padding:100px 0">
  <i class="far fa-heart" style="font-size:4rem;color:var(--gray-400);margin-bottom:20px;display:block"></i>
  <h2 style="margin-bottom:12px">Your wishlist is empty</h2>
  <a href="shop.php" class="btn btn-primary">Start Shopping</a>
</div>
<?php endif; ?>
</div></section>
<?php include 'includes/footer.php'; ?>
