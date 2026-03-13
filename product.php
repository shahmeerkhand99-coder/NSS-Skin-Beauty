<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$slug = $_GET['slug'] ?? '';
if (!$slug) { redirect(SITE_URL . '/shop.php'); }
$product = getProductBySlug($slug);
if (!$product) { http_response_code(404); include '404.php'; exit; }
db()->execute("UPDATE products SET views=views+1 WHERE id=?",[$product['id']]);
$related = getRelatedProducts($product['category_id'], $product['id']);
$reviews = getProductReviews($product['id']);
$wishlistIds = getWishlistIds();
$inWishlist = in_array($product['id'], $wishlistIds);
$gallery = $product['gallery'] ? explode(',', $product['gallery']) : [];
$effectivePrice = $product['sale_price'] ?? $product['price'];
$pageTitle = sanitize($product['name']) . ' - NS Beauty';
$pageDesc = $product['short_description'];
include 'includes/header.php';
?>
<div class="page-banner">
  <div class="container">
    <h1><?= sanitize($product['name']) ?></h1>
    <nav class="breadcrumb">
      <a href="index.php">Home</a> <i class="fas fa-chevron-right" style="font-size:0.7rem"></i>
      <a href="category.php?slug=<?= $product['category_slug'] ?>"><?= sanitize($product['category_name']) ?></a>
      <i class="fas fa-chevron-right" style="font-size:0.7rem"></i>
      <span><?= sanitize($product['name']) ?></span>
    </nav>
  </div>
</div>
<section class="section">
  <div class="container">
    <div class="product-detail-grid">
      <!-- Gallery -->
      <div class="product-gallery">
        <div class="gallery-main">
          <img id="galleryMain" src="<?= $product['image'] ? UPLOAD_URL.'products/'.$product['image'] : 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=600&q=80' ?>" alt="<?= sanitize($product['name']) ?>">
        </div>
        <?php if ($gallery): ?>
        <div class="gallery-thumbs">
          <div class="gallery-thumb active" data-img="<?= UPLOAD_URL.'products/'.$product['image'] ?>">
            <img src="<?= UPLOAD_URL.'products/'.$product['image'] ?>" alt="">
          </div>
          <?php foreach ($gallery as $img): ?>
          <div class="gallery-thumb" data-img="<?= UPLOAD_URL.'products/'.trim($img) ?>">
            <img src="<?= UPLOAD_URL.'products/'.trim($img) ?>" alt="">
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <!-- Info -->
      <div class="product-detail-info">
        <p class="product-category"><?= sanitize($product['category_name']) ?></p>
        <h1><?= sanitize($product['name']) ?></h1>
        <div class="product-rating">
          <?= getStarRating(round($product['avg_rating'] ?? 0, 1), $product['review_count'] ?? 0) ?>
        </div>
        <div class="product-detail-price">
          <span class="price-current"><?= formatPrice($effectivePrice) ?></span>
          <?php if ($product['sale_price']): ?>
            <span class="price-original"><?= formatPrice($product['price']) ?></span>
            <span class="discount-badge">-<?= $product['discount_percent'] ?>%</span>
          <?php endif; ?>
        </div>
        <p class="product-stock <?= $product['stock'] > 0 ? 'stock-in' : 'stock-out' ?>">
          <i class="fas fa-<?= $product['stock'] > 0 ? 'check-circle' : 'times-circle' ?>"></i>
          <?= $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' available)' : 'Out of Stock' ?>
        </p>
        <?php if ($product['short_description']): ?>
        <p style="color:var(--gray-600);margin:16px 0;line-height:1.7"><?= sanitize($product['short_description']) ?></p>
        <?php endif; ?>
        <?php if ($product['stock'] > 0): ?>
        <div class="qty-row">
          <div class="qty-box">
            <button type="button" class="qty-btn" data-action="minus">−</button>
            <input type="number" id="productQty" class="qty-value" value="1" min="1" max="<?= $product['stock'] ?>" name="qty">
            <button type="button" class="qty-btn" data-action="plus">+</button>
          </div>
          <div class="product-actions" style="flex:1;display:flex;flex-direction:column;gap:12px">
            <div style="display:flex;gap:12px;width:100%">
              <button class="btn btn-primary" style="flex:1" data-id="<?= $product['id'] ?>" id="addToCartBtn"><i class="fas fa-shopping-bag"></i> Add to Cart</button>
              <button class="btn-wishlist wishlist-btn <?= $inWishlist ? 'active' : '' ?>" data-id="<?= $product['id'] ?>" title="Wishlist">
                <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
              </button>
            </div>
            <button class="btn btn-gold" style="width:100%;height:50px;font-weight:700;letter-spacing:1px" id="buyNowBtn"><i class="fas fa-bolt"></i> BUY IT NOW</button>
          </div>
        </div>
        <?php endif; ?>
        <script>
        document.getElementById('buyNowBtn')?.addEventListener('click', async function() {
            this.disabled=true; this.innerHTML='<i class="fas fa-spinner fa-spin"></i> Processing...';
            const qty = document.getElementById('productQty')?.value||1;
            const res = await fetch('ajax/cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=add&product_id=<?= $product['id'] ?>&qty=${qty}`});
            const data = await res.json();
            if(data.success) {
                window.location.href = 'checkout.php';
            } else {
                showToast(data.message, 'error');
                this.disabled=false; this.innerHTML='<i class="fas fa-bolt"></i> BUY IT NOW';
            }
        });
        </script>
        <div style="display:flex;gap:12px;flex-wrap:wrap;font-size:0.82rem;color:var(--gray-600);margin-top:16px">
          <?php if ($product['sku']): ?><span><strong>SKU:</strong> <?= $product['sku'] ?></span><?php endif; ?>
          <?php if ($product['weight']): ?><span><strong>Weight:</strong> <?= $product['weight'] ?></span><?php endif; ?>
          <span><strong>Brand:</strong> <?= sanitize($product['brand']) ?></span>
        </div>
        <!-- Tabs -->
        <div class="product-tabs mt-32">
          <div class="tabs-nav">
            <button class="tab-btn active" data-tab="tab-desc">Description</button>
            <?php if ($product['ingredients']): ?><button class="tab-btn" data-tab="tab-ingr">Ingredients</button><?php endif; ?>
            <?php if ($product['how_to_use']): ?><button class="tab-btn" data-tab="tab-how">How to Use</button><?php endif; ?>
            <button class="tab-btn" data-tab="tab-reviews">Reviews (<?= count($reviews) ?>)</button>
          </div>
          <div id="tab-desc" class="tab-content active" style="color:var(--gray-600);line-height:1.8"><?= nl2br(sanitize($product['description'])) ?></div>
          <?php if ($product['ingredients']): ?><div id="tab-ingr" class="tab-content" style="color:var(--gray-600);line-height:1.8"><?= nl2br(sanitize($product['ingredients'])) ?></div><?php endif; ?>
          <?php if ($product['how_to_use']): ?><div id="tab-how" class="tab-content" style="color:var(--gray-600);line-height:1.8"><?= nl2br(sanitize($product['how_to_use'])) ?></div><?php endif; ?>
          <div id="tab-reviews" class="tab-content">
            <?php if ($reviews): foreach ($reviews as $r): ?>
            <div style="padding:16px 0;border-bottom:1px solid var(--gray-200)">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                <span><?= getStarRating($r['rating']) ?></span>
                <strong><?= sanitize($r['user_name']) ?></strong>
                <span style="font-size:0.78rem;color:var(--gray-600)"><?= timeAgo($r['created_at']) ?></span>
              </div>
              <?php if ($r['title']): ?><p style="font-weight:600;margin-bottom:4px"><?= sanitize($r['title']) ?></p><?php endif; ?>
              <p style="color:var(--gray-600);font-size:0.9rem"><?= sanitize($r['comment']) ?></p>
            </div>
            <?php endforeach; else: ?>
            <p style="color:var(--gray-600);padding:20px 0">No reviews yet. Be the first to review this product!</p>
            <?php endif; ?>
            <?php if (isLoggedIn()): ?>
            <div style="margin-top:24px;background:var(--gray-100);border-radius:12px;padding:20px">
              <h4 style="margin-bottom:16px">Write a Review</h4>
              <form id="reviewForm" data-product="<?= $product['id'] ?>">
                <div class="form-group"><label>Your Rating *</label>
                  <select name="rating" class="form-control"><option value="5">⭐⭐⭐⭐⭐ Excellent</option><option value="4">⭐⭐⭐⭐ Good</option><option value="3">⭐⭐⭐ Average</option><option value="2">⭐⭐ Poor</option><option value="1">⭐ Terrible</option></select>
                </div>
                <div class="form-group"><label>Review Title</label><input type="text" name="title" class="form-control" placeholder="Summarize your experience"></div>
                <div class="form-group"><label>Your Review *</label><textarea name="comment" class="form-control" rows="4" placeholder="Share your thoughts about this product..." required></textarea></div>
                <button type="submit" class="btn btn-primary">Submit Review</button>
              </form>
            </div>
            <?php else: ?><p style="margin-top:16px;padding:12px;background:var(--pink-light);border-radius:10px;font-size:0.88rem"><a href="login.php" style="color:var(--pink);font-weight:600">Login</a> to write a review</p><?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- Related -->
<?php if ($related): ?>
<section class="section bg-off-white">
  <div class="container">
    <div class="section-header"><h2>You May Also Like</h2></div>
    <div class="products-grid">
      <?php foreach ($related as $rp): ?>
        <?php $rp['category_name'] = $product['category_name']; ?>
        <?= renderProductCard($rp, $wishlistIds) ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<script>
document.getElementById('addToCartBtn')?.addEventListener('click', async function() {
    this.disabled=true; this.innerHTML='<i class="fas fa-spinner fa-spin"></i> Adding...';
    const qty = document.getElementById('productQty')?.value||1;
    const res = await fetch('ajax/cart.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`action=add&product_id=<?= $product['id'] ?>&qty=${qty}`});
    const data = await res.json();
    showToast(data.message, data.success?'success':'error');
    document.querySelectorAll('.cart-count').forEach(el=>el.textContent=data.count>0?data.count:'');
    this.disabled=false; this.innerHTML='<i class="fas fa-shopping-bag"></i> Add to Cart';
});
document.getElementById('reviewForm')?.addEventListener('submit',async function(e){
    e.preventDefault();
    const data = new FormData(this);
    data.append('product_id','<?= $product['id'] ?>');
    const res = await fetch('ajax/review.php',{method:'POST',body:data});
    const json = await res.json();
    showToast(json.message, json.success?'success':'error');
    if(json.success) this.reset();
});
</script>
<?php include 'includes/footer.php'; ?>
