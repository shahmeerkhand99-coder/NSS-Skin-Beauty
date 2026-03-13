<?php
$adminPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="admin-sidebar">
  <div class="admin-logo">
    <div style="display:flex;align-items:center;gap:4px;font-size:1.4rem;font-weight:800">
      <span style="color:var(--pink);font-family:'Playfair Display',serif;font-style:italic">NSS</span>
      <span style="color:#fff;font-family:'Playfair Display',serif">Skin & Beauty</span>
    </div>
    <div style="font-size:0.7rem;color:rgba(255,255,255,0.4);margin-top:4px">Admin Panel</div>
  </div>
  <nav class="admin-nav">
    <div class="nav-section">Main</div>
    <a href="index.php" class="<?= $adminPage==='index'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <div class="nav-section">Catalog</div>
    <a href="products.php" class="<?= $adminPage==='products'?'active':'' ?>"><i class="fas fa-box"></i> Products</a>
    <a href="categories.php" class="<?= $adminPage==='categories'?'active':'' ?>"><i class="fas fa-tags"></i> Categories</a>
    <div class="nav-section">Sales</div>
    <a href="orders.php" class="<?= $adminPage==='orders'?'active':'' ?>"><i class="fas fa-shopping-bag"></i> Orders</a>
    <a href="coupons.php" class="<?= $adminPage==='coupons'?'active':'' ?>"><i class="fas fa-ticket-alt"></i> Coupons</a>
    <div class="nav-section">Content</div>
    <a href="banners.php" class="<?= $adminPage==='banners'?'active':'' ?>"><i class="fas fa-image"></i> Banners</a>
    <a href="reviews.php" class="<?= $adminPage==='reviews'?'active':'' ?>"><i class="fas fa-star"></i> Reviews</a>
    <div class="nav-section">Users</div>
    <a href="users.php" class="<?= $adminPage==='users'?'active':'' ?>"><i class="fas fa-users"></i> Customers</a>
    <div style="border-top:1px solid rgba(255,255,255,0.08);margin:16px 12px 8px">
    <a href="<?= SITE_URL ?>" target="_blank" style="display:flex;align-items:center;gap:12px;padding:11px 14px;color:rgba(255,255,255,0.5);font-size:0.85rem"><i class="fas fa-external-link-alt"></i> View Site</a>
    <a href="<?= SITE_URL ?>/logout.php" style="display:flex;align-items:center;gap:12px;padding:11px 14px;color:rgba(255,255,255,0.5);font-size:0.85rem"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
</aside>
