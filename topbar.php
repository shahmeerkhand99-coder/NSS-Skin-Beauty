<?php $adminUser = $_SESSION['user_name'] ?? 'Admin'; ?>
<div class="admin-topbar">
  <div>
    <h2 style="font-size:1.1rem;font-weight:700"><?php
      $titles = ['index'=>'Dashboard','products'=>'Products','categories'=>'Categories','orders'=>'Orders','users'=>'Customers','coupons'=>'Coupons','banners'=>'Banners','reviews'=>'Reviews'];
      echo $titles[basename($_SERVER['PHP_SELF'],'.php')] ?? 'Admin';
    ?></h2>
  </div>
  <div style="display:flex;align-items:center;gap:16px">
    <a href="<?= SITE_URL ?>" target="_blank" style="font-size:0.85rem;color:var(--gray-600)"><i class="fas fa-external-link-alt"></i> View Site</a>
    <div style="display:flex;align-items:center;gap:8px">
      <div style="width:36px;height:36px;background:var(--pink);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700"><?= strtoupper(substr($adminUser,0,1)) ?></div>
      <span style="font-weight:600;font-size:0.9rem"><?= sanitize($adminUser) ?></span>
    </div>
  </div>
</div>
