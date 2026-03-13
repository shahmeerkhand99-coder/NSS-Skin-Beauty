<?php
// NS Beauty - Site Footer
?>
<footer class="site-footer">
    <div class="footer-top">
        <div class="container">
            <div class="footer-grid">
                <!-- Brand Column -->
                <div class="footer-col footer-brand">
                    <div class="footer-logo">
                        <span class="logo-ns">NSS</span>
                        <span class="logo-beauty">Skin & Beauty</span>
                    </div>
                    <p>Crafting luxurious beauty experiences since 2020. At NSS Skin & Beauty, we believe every woman deserves to feel beautiful, confident, and empowered.</p>
                    <div class="social-links">
                        <a href="<?= getSetting('facebook_url') ?? '#' ?>" target="_blank" aria-label="Facebook" class="social-fb"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= getSetting('instagram_url') ?? '#' ?>" target="_blank" aria-label="Instagram" class="social-ig"><i class="fab fa-instagram"></i></a>
                        <a href="<?= getSetting('twitter_url') ?? '#' ?>" target="_blank" aria-label="Twitter" class="social-tw"><i class="fab fa-twitter"></i></a>
                        <a href="<?= getSetting('youtube_url') ?? '#' ?>" target="_blank" aria-label="YouTube" class="social-yt"><i class="fab fa-youtube"></i></a>
                        <a href="<?= getSetting('tiktok_url') ?? '#' ?>" target="_blank" aria-label="TikTok" class="social-tt"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Shop Column -->
                <div class="footer-col">
                    <h4>Shop</h4>
                    <ul>
                        <?php foreach (getAllCategories() as $cat): ?>
                        <li><a href="<?= SITE_URL ?>/category.php?slug=<?= $cat['slug'] ?>"><?= sanitize($cat['name']) ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="<?= SITE_URL ?>/shop.php?filter=new">New Arrivals</a></li>
                        <li><a href="<?= SITE_URL ?>/shop.php?filter=bestseller">Best Sellers</a></li>
                    </ul>
                </div>

                <!-- Help Column -->
                <div class="footer-col">
                    <h4>Help</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
                        <li><a href="<?= SITE_URL ?>/contact.php">Contact Us</a></li>
                        <li><a href="<?= SITE_URL ?>/faq.php">FAQ</a></li>
                        <li><a href="<?= SITE_URL ?>/return-policy.php">Return Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/terms.php">Terms & Conditions</a></li>
                    </ul>
                </div>

                <!-- Newsletter Column -->
                <div class="footer-col footer-newsletter">
                    <h4>Stay Beautiful</h4>
                    <p>Subscribe to get exclusive offers, beauty tips & new arrivals straight to your inbox.</p>
                    <form class="newsletter-form" id="footerNewsletter">
                        <div class="newsletter-input-wrap">
                            <input type="email" name="email" placeholder="Enter your email" required>
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                    <div class="payment-icons">
                        <span title="Cash on Delivery"><i class="fas fa-money-bill-wave"></i> COD</span>
                        <span title="Visa"><i class="fab fa-cc-visa"></i></span>
                        <span title="Mastercard"><i class="fab fa-cc-mastercard"></i></span>
                        <span title="JazzCash"><i class="fas fa-mobile-alt"></i> JazzCash</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <strong>NSS Skin & Beauty</strong>. All rights reserved. Made with <i class="fas fa-heart text-pink"></i> for beauty lovers.</p>
            <div class="footer-bottom-links">
                <a href="<?= SITE_URL ?>/privacy-policy.php">Privacy</a>
                <a href="<?= SITE_URL ?>/terms.php">Terms</a>
                <a href="<?= SITE_URL ?>/return-policy.php">Returns</a>
            </div>
        </div>
    </div>
<!-- Mobile Bottom Nav -->
<div class="mobile-bottom-nav">
    <a href="<?= SITE_URL ?>/index.php" class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="<?= SITE_URL ?>/shop.php" class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : '' ?>">
        <i class="fas fa-shopping-bag"></i>
        <span>Shop</span>
    </a>
    <a href="javascript:void(0)" class="mobile-nav-item" id="mobileSearchToggle">
        <i class="fas fa-search"></i>
        <span>Search</span>
    </a>
    <a href="<?= SITE_URL ?>/wishlist.php" class="mobile-nav-item <?= basename($_SERVER['PHP_SELF']) == 'wishlist.php' ? 'active' : '' ?>">
        <i class="fas fa-heart"></i>
        <span>Wishlist</span>
    </a>
    <a href="<?= SITE_URL ?>/login.php" class="mobile-nav-item <?= in_array(basename($_SERVER['PHP_SELF']), ['login.php', 'register.php', 'dashboard.php', 'profile.php']) ? 'active' : '' ?>">
        <i class="fas fa-user"></i>
        <span>Account</span>
    </a>
</div>

<div class="toast-container" id="toastContainer"></div>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<?php if (isset($extraJS)) echo (string)$extraJS; ?>
</body>
</html>
