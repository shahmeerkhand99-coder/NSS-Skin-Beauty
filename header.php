<?php
// NS Beauty - Site Header
if (!defined('SITE_URL')) {
    require_once dirname(__DIR__) . '/config/config.php';
}
require_once dirname(__DIR__) . '/includes/functions.php';

$categories = getAllCategories();
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$wishlistCount = 0;
if (isLoggedIn()) {
    $wl = db()->fetchOne("SELECT COUNT(*) as c FROM wishlist WHERE user_id = ?", [$_SESSION['user_id']]);
    $wishlistCount = $wl['c'];
}

$pageTitle = $pageTitle ?? SITE_NAME . ' - ' . SITE_TAGLINE;
$pageDesc = $pageDesc ?? 'Discover premium beauty products at NSS Skin & Beauty. Shop makeup, skincare, haircare, fragrances & beauty tools.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitize($pageDesc) ?>">
    <meta name="keywords" content="beauty products, makeup, skincare, haircare, NSS Skin & Beauty, fragrances">
    <meta property="og:title" content="<?= sanitize($pageTitle) ?>">
    <meta property="og:description" content="<?= sanitize($pageDesc) ?>">
    <meta property="og:type" content="website">
    <title><?= sanitize($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <?php if (isset($extraCSS)) echo (string)$extraCSS; ?>
</head>
<body>

<!-- Top announcement bar -->
<div class="announcement-bar">
    <div class="container">
        <p>🌸 Free shipping on orders over <?= formatPrice(FREE_SHIPPING_MIN) ?> | Use code <strong>WELCOME10</strong> for 10% off your first order 💄</p>
    </div>
</div>

<!-- Header -->
<header class="site-header" id="siteHeader">
    <div class="container">
        <div class="header-inner">
            <!-- Mobile menu toggle -->
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>

            <!-- Logo -->
            <div class="logo">
                <a href="<?= SITE_URL ?>/">
                    <span class="logo-ns">NSS</span>
                    <span class="logo-beauty">Skin & Beauty</span>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="main-nav" id="mainNav">
                <ul>
                    <li><a href="<?= SITE_URL ?>/" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Home</a></li>
                    <li class="has-dropdown">
                        <a href="<?= SITE_URL ?>/shop.php" class="<?= $currentPage === 'shop' ? 'active' : '' ?>">Shop <i class="fas fa-chevron-down"></i></a>
                        <div class="mega-menu">
                            <div class="mega-menu-inner">
                                <?php foreach ($categories as $cat): ?>
                                <a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>">
                                    <i class="<?= $cat['icon'] ?>"></i>
                                    <span><?= sanitize($cat['name']) ?></span>
                                </a>
                                <?php endforeach; ?>
                                <a href="<?= SITE_URL ?>/shop.php?filter=new">
                                    <i class="fas fa-fire"></i>
                                    <span>New Arrivals</span>
                                </a>
                                <a href="<?= SITE_URL ?>/shop.php?filter=bestseller">
                                    <i class="fas fa-crown"></i>
                                    <span>Best Sellers</span>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li><a href="<?= SITE_URL ?>/about.php" class="<?= $currentPage === 'about' ? 'active' : '' ?>">About</a></li>
                    <li><a href="<?= SITE_URL ?>/contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
                </ul>
            </nav>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Search -->
                <button class="action-btn search-toggle" id="searchToggle" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>

                <!-- Wishlist -->
                <a href="<?= SITE_URL ?>/wishlist.php" class="action-btn" aria-label="Wishlist">
                    <i class="far fa-heart"></i>
                    <?php if ($wishlistCount > 0): ?>
                        <span class="badge-count"><?= $wishlistCount ?></span>
                    <?php endif; ?>
                </a>

                <!-- User -->
                <?php if (isLoggedIn()): ?>
                <div class="user-dropdown">
                    <button class="action-btn" aria-label="Account">
                        <i class="far fa-user"></i>
                    </button>
                    <div class="user-menu">
                        <a href="<?= SITE_URL ?>/user/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="<?= SITE_URL ?>/user/orders.php"><i class="fas fa-box"></i> My Orders</a>
                        <a href="<?= SITE_URL ?>/wishlist.php"><i class="far fa-heart"></i> Wishlist</a>
                        <a href="<?= SITE_URL ?>/user/profile.php"><i class="fas fa-user-edit"></i> Profile</a>
                        <?php if (isAdmin()): ?>
                        <a href="<?= SITE_URL ?>/admin/" style="color:var(--pink)"><i class="fas fa-shield-alt"></i> Admin Panel</a>
                        <?php endif; ?>
                        <div class="divider"></div>
                        <a href="<?= SITE_URL ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                <?php else: ?>
                <a href="<?= SITE_URL ?>/login.php" class="action-btn" aria-label="Login">
                    <i class="far fa-user"></i>
                </a>
                <?php endif; ?>

                <!-- Cart -->
                <a href="<?= SITE_URL ?>/cart.php" class="action-btn cart-btn" aria-label="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="badge-count cart-count"><?= $cartCount > 0 ? $cartCount : '' ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="search-bar" id="searchBar">
        <div class="container">
            <form action="<?= SITE_URL ?>/search.php" method="GET" class="search-form">
                <input type="text" name="q" id="searchInput" placeholder="Search for lipstick, serum, perfume..." autocomplete="off">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div class="search-suggestions" id="searchSuggestions"></div>
        </div>
    </div>
</header>

<!-- Toast notifications -->
<div class="toast-container" id="toastContainer"></div>

<!-- Mobile Nav Overlay -->
<div class="nav-overlay" id="navOverlay"></div>
