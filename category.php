<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$slug = $_GET['slug'] ?? '';
$category = $slug ? getCategoryBySlug($slug) : null;
if (!$category) redirect(SITE_URL . '/shop.php');
$page = max(1,(int)($_GET['page']??1));
$products = getProducts(['category_id'=>$category['id']],$page);
$total = getProductCount(['category_id'=>$category['id']]);
$wishlistIds = getWishlistIds();
$pageTitle = sanitize($category['name']) . ' - NS Beauty';
include 'includes/header.php';
?>
<div class="page-banner">
  <div class="container">
    <h1><?= sanitize($category['name']) ?></h1>
    <?php if ($category['description']): ?><p style="color:var(--gray-600);margin-top:8px"><?= sanitize($category['description']) ?></p><?php endif; ?>
    <nav class="breadcrumb mt-16"><a href="index.php">Home</a> <i class="fas fa-chevron-right" style="font-size:0.7rem"></i> <span><?= sanitize($category['name']) ?></span></nav>
  </div>
</div>
<section class="section">
  <div class="container">
    <div class="shop-header">
      <p style="color:var(--gray-600);font-size:0.88rem"><strong><?= $total ?></strong> products in <?= sanitize($category['name']) ?></p>
    </div>
    <?php if ($products): ?>
    <div class="products-grid">
      <?php foreach ($products as $p): $p['category_name']=$category['name']; echo renderProductCard($p,$wishlistIds); endforeach; ?>
    </div>
    <?= getPagination($total,$page,PRODUCTS_PER_PAGE,'category.php?slug='.$slug.'&') ?>
    <?php else: ?>
    <div style="text-align:center;padding:80px 0"><h3>No products in this category yet.</h3></div>
    <?php endif; ?>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
