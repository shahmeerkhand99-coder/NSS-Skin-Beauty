<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$q = trim($_GET['q'] ?? '');
$page = max(1,(int)($_GET['page']??1));
$products = $q ? getProducts(['search'=>$q],$page) : [];
$total = $q ? getProductCount(['search'=>$q]) : 0;
$wishlistIds = getWishlistIds();
$pageTitle = 'Search: ' . sanitize($q) . ' - NS Beauty';
include 'includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>Search Results</h1><p style="margin-top:8px;color:var(--gray-600)"><?= $q ? "Showing results for <strong>\"".sanitize($q)."\"</strong> — $total products found" : 'Enter a keyword to search' ?></p></div></div>
<section class="section"><div class="container">
<?php if ($products): ?>
<div class="products-grid">
  <?php foreach ($products as $p): $p['category_name']=db()->fetchOne("SELECT name FROM categories WHERE id=?",[$p['category_id']])['name']??''; echo renderProductCard($p,$wishlistIds); endforeach; ?>
</div>
<?= getPagination($total,$page,PRODUCTS_PER_PAGE,'search.php?q='.urlencode($q).'&') ?>
<?php elseif ($q): ?>
<div style="text-align:center;padding:80px 0"><i class="fas fa-search" style="font-size:3rem;color:var(--gray-400);display:block;margin-bottom:16px"></i><h3>No results for "<?= sanitize($q) ?>"</h3><p style="color:var(--gray-600);margin:12px 0">Try different keywords or browse our categories.</p><a href="shop.php" class="btn btn-primary mt-16">Browse All Products</a></div>
<?php endif; ?>
</div></section>
<?php include 'includes/footer.php'; ?>
