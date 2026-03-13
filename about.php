<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$pageTitle = 'About Us - NSS Skin & Beauty';
include 'includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>Our Story</h1><p style="color:var(--gray-600);margin-top:8px">Beauty with a purpose</p></div></div>
<section class="section"><div class="container" style="max-width:900px;margin:0 auto;text-align:center">
  <div class="subtitle">Who We Are</div>
  <h2 style="font-family:'Playfair Display',serif;font-size:2.2rem;margin-bottom:20px">Crafted with Love for Every Beauty Lover</h2>
  <p style="color:var(--gray-600);font-size:1.05rem;line-height:1.8;margin-bottom:40px">NSS Skin & Beauty was founded in 2020 with one mission: to make luxury beauty accessible to every woman. We curate and craft premium beauty products — from bold lipsticks to nourishing skincare — using only the finest, skin-friendly ingredients. Our products are cruelty-free, dermatologist-tested, and designed to celebrate your natural beauty.</p>
  <div class="about-feature-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:30px;margin-bottom:60px">
    <?php foreach([['fas fa-leaf','Cruelty Free','All products are 100% cruelty-free with no animal testing'],['fas fa-flask','Dermatologist Tested','Formulated with skin experts for safe, effective results'],['fas fa-heart','Made with Love','Every product is crafted with passion and care for our customers']] as $v): ?>
    <div style="background:var(--off-white);border-radius:16px;padding:30px;border:1px solid var(--gray-200)">
      <div style="width:56px;height:56px;background:var(--pink-light);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--pink);font-size:1.3rem;margin:0 auto 16px"><i class="<?= $v[0] ?>"></i></div>
      <h4 style="margin-bottom:10px"><?= $v[1] ?></h4>
      <p style="color:var(--gray-600);font-size:0.88rem"><?= $v[2] ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <div style="background:linear-gradient(135deg,var(--pink),var(--pink-dark));border-radius:20px;padding:50px;color:#fff">
    <h2 style="font-family:'Playfair Display',serif;font-size:2rem;margin-bottom:12px">Our Mission</h2>
    <p style="opacity:0.9;font-size:1.05rem;max-width:600px;margin:0 auto">To empower every woman to express her unique beauty with confidence through high-quality, affordable products that are kind to both skin and soul.</p>
  </div>
</div></section>
<?php include 'includes/footer.php'; ?>
