<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

$categories = getAllCategories();
$categoryId = isset($_GET['cat']) ? (int)$_GET['cat'] : null;
$sort = $_GET['sort'] ?? 'newest';
$minPrice = isset($_GET['min']) ? (int)$_GET['min'] : 0;
$maxPrice = isset($_GET['max']) ? (int)$_GET['max'] : 10000;
$page = max(1, (int)($_GET['page'] ?? 1));
$filter = $_GET['filter'] ?? '';

$filters = ['sort' => $sort, 'min_price' => $minPrice > 0 ? $minPrice : null, 'max_price' => $maxPrice < 10000 ? $maxPrice : null];
if ($categoryId) $filters['category_id'] = $categoryId;
if ($filter === 'new') $filters['is_new'] = true;
if ($filter === 'bestseller') $filters['is_bestseller'] = true;

$products = getProducts($filters, $page);
$total = getProductCount($filters);
$wishlistIds = getWishlistIds();

$currentCat = $categoryId ? db()->fetchOne("SELECT * FROM categories WHERE id=?",[$categoryId]) : null;
$pageTitle = ($currentCat ? $currentCat['name'] . ' - ' : '') . 'Shop - NS Beauty';
include 'includes/header.php';
?>
<div class="page-banner">
  <div class="container">
    <h1><?= $currentCat ? sanitize($currentCat['name']) : 'Shop All Products' ?></h1>
    <nav class="breadcrumb"><a href="index.php">Home</a> <i class="fas fa-chevron-right" style="font-size:0.7rem"></i> <span>Shop</span></nav>
  </div>
</div>
<section class="section">
  <div class="container">
    <div class="shop-layout">
      <!-- Filters -->
      <aside class="filter-sidebar">
        <h3 style="font-weight:700;margin-bottom:20px">Filters</h3>
        <form method="GET" id="filterForm">
          <div class="filter-group">
            <div class="filter-title">Categories</div>
            <?php foreach ($categories as $cat): ?>
            <label class="filter-option">
              <input type="radio" name="cat" value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()">
              <?= sanitize($cat['name']) ?>
            </label>
            <?php endforeach; ?>
            <label class="filter-option">
              <input type="radio" name="cat" value="" <?= !$categoryId ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()">
              All Categories
            </label>
          </div>
          <div class="filter-group">
            <div class="filter-title">Price Range</div>
            <div id="priceDisplay" style="font-size:0.85rem;color:var(--pink);font-weight:600;margin-bottom:10px">Rs. 0 - Rs. 10,000</div>
            <div class="price-range">
              <input type="number" name="min" id="priceMin" value="<?= $minPrice ?>" placeholder="Min" min="0">
              <input type="number" name="max" id="priceMax" value="<?= $maxPrice ?>" placeholder="Max" min="0">
            </div>
          </div>
          <div class="filter-group">
            <div class="filter-title">Availability</div>
            <label class="filter-option"><input type="radio" name="filter" value="new" <?= $filter==='new'?'checked':'' ?> onchange="document.getElementById('filterForm').submit()"> New Arrivals</label>
            <label class="filter-option"><input type="radio" name="filter" value="bestseller" <?= $filter==='bestseller'?'checked':'' ?> onchange="document.getElementById('filterForm').submit()"> Best Sellers</label>
            <label class="filter-option"><input type="radio" name="filter" value="" <?= !$filter?'checked':'' ?> onchange="document.getElementById('filterForm').submit()"> All Products</label>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%">Apply Filters</button>
          <a href="shop.php" class="btn btn-outline" style="width:100%;margin-top:10px">Clear Filters</a>
        </form>
      </aside>
      <!-- Products -->
      <div>
        <div class="shop-header">
          <p style="color:var(--gray-600);font-size:0.88rem">Showing <strong><?= count($products) ?></strong> of <strong><?= $total ?></strong> products</p>
          <select class="sort-select" onchange="window.location.href='?sort='+this.value+'<?= $categoryId?'&cat='.$categoryId:'' ?>'">
            <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest First</option>
            <option value="price_asc" <?= $sort==='price_asc'?'selected':'' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>Price: High to Low</option>
            <option value="popular" <?= $sort==='popular'?'selected':'' ?>>Most Popular</option>
          </select>
        </div>
        <?php if ($products): ?>
        <div class="products-grid">
          <?php foreach ($products as $product): ?>
            <?php $product['category_name'] = db()->fetchOne("SELECT name FROM categories WHERE id=?",[$product['category_id']])['name'] ?? ''; ?>
            <?= renderProductCard($product, $wishlistIds) ?>
          <?php endforeach; ?>
        </div>
        <?php $baseUrl = 'shop.php?' . http_build_query(array_filter(['cat'=>$categoryId,'sort'=>$sort,'min'=>$minPrice,'max'=>$maxPrice,'filter'=>$filter])); ?>
        <?= getPagination($total, $page, PRODUCTS_PER_PAGE, $baseUrl) ?>
        <?php else: ?>
        <div style="text-align:center;padding:80px 0">
          <i class="fas fa-search" style="font-size:3rem;color:var(--gray-400);margin-bottom:16px"></i>
          <h3>No products found</h3>
          <a href="shop.php" class="btn btn-primary mt-16">Browse All Products</a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
