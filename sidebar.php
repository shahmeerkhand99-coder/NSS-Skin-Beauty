<?php
$dashPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<aside class="dashboard-sidebar">
  <div style="text-align:center;padding:16px 0 24px;border-bottom:1px solid var(--gray-200);margin-bottom:12px">
    <div style="width:60px;height:60px;background:var(--pink-light);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--pink);font-size:1.4rem;font-weight:700;margin:0 auto 10px"><?= strtoupper(substr($user['name'],0,1)) ?></div>
    <div style="font-weight:600;font-size:0.95rem"><?= sanitize($user['name']) ?></div>
    <div style="font-size:0.78rem;color:var(--gray-600)"><?= sanitize($user['email']) ?></div>
  </div>
  <nav class="dashboard-nav">
    <a href="dashboard.php" class="<?= $dashPage==='dashboard'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="orders.php" class="<?= $dashPage==='orders'?'active':'' ?>"><i class="fas fa-box"></i> My Orders</a>
    <a href="<?= SITE_URL ?>/wishlist.php" class=""><i class="far fa-heart"></i> Wishlist</a>
    <a href="profile.php" class="<?= $dashPage==='profile'?'active':'' ?>"><i class="fas fa-user-edit"></i> Profile</a>
    <a href="addresses.php" class="<?= $dashPage==='addresses'?'active':'' ?>"><i class="fas fa-map-marker-alt"></i> My Addresses</a>
    <?php if (isAdmin()): ?><a href="<?= SITE_URL ?>/admin/" style="color:var(--pink)"><i class="fas fa-shield-alt"></i> Admin Panel</a><?php endif; ?>
    <div style="border-top:1px solid var(--gray-200);margin:12px 0"></div>
    <a href="<?= SITE_URL ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </nav>
</aside>
