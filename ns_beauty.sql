-- NSS Skin & Beauty E-Commerce Database
-- Created: 2026-03-09

CREATE DATABASE IF NOT EXISTS `ns_beauty` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ns_beauty`;

-- Users Table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `status` enum('active','inactive') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories Table
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products Table
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `how_to_use` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `stock` int(11) DEFAULT 0,
  `sku` varchar(100) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `brand` varchar(100) DEFAULT 'NSS Skin & Beauty',
  `image` varchar(255) DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_bestseller` tinyint(1) DEFAULT 0,
  `is_new` tinyint(1) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `views` int(11) DEFAULT 0,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product Images
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Addresses Table
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `country` varchar(100) DEFAULT 'Pakistan',
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Coupons Table
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` enum('percent','flat') DEFAULT 'percent',
  `value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) DEFAULT 0,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders Table
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip` varchar(20) NOT NULL,
  `country` varchar(100) DEFAULT 'Pakistan',
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT 0,
  `shipping` decimal(10,2) DEFAULT 0,
  `total` decimal(10,2) NOT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `payment_method` enum('cod','card','online') DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `status` enum('pending','processing','shipped','delivered','cancelled','returned') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order Items Table
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart Table
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wishlist Table
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews Table
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Banners Table
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` varchar(300) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `position` enum('hero','promo','sidebar') DEFAULT 'hero',
  `status` enum('active','inactive') DEFAULT 'active',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Newsletter Subscribers
CREATE TABLE `subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `status` enum('active','unsubscribed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings Table
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===========================
-- SAMPLE DATA
-- ===========================

-- Admin User (password: admin123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `status`, `email_verified`) VALUES
('NSS Skin & Beauty Admin', 'admin@nssskinbeauty.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 1),
('Sara Khan', 'sara@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', 1),
('Ayesha Ahmed', 'ayesha@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', 1),
('Fatima Ali', 'fatima@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', 'active', 1);

-- Categories
INSERT INTO `categories` (`name`, `slug`, `description`, `icon`, `status`, `sort_order`) VALUES
('Makeup', 'makeup', 'Enhance your natural beauty with our premium makeup collection', 'fas fa-paint-brush', 'active', 1),
('Skincare', 'skincare', 'Nourish and protect your skin with our skincare essentials', 'fas fa-leaf', 'active', 2),
('Haircare', 'haircare', 'Achieve gorgeous, healthy hair with our haircare range', 'fas fa-spa', 'active', 3),
('Beauty Tools', 'beauty-tools', 'Professional beauty tools for the perfect look', 'fas fa-magic', 'active', 4),
('Fragrances', 'fragrances', 'Captivating scents that leave a lasting impression', 'fas fa-star', 'active', 5);

-- Products
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `short_description`, `price`, `sale_price`, `discount_percent`, `stock`, `sku`, `brand`, `is_featured`, `is_bestseller`, `is_new`, `status`) VALUES
(1, 'Velvet Matte Lipstick - Rose Red', 'velvet-matte-lipstick-rose-red', 'Our signature Velvet Matte Lipstick delivers rich, long-lasting color with a luxurious matte finish. Enriched with vitamin E and jojoba oil, it keeps lips moisturized all day. The pigment-packed formula provides full coverage in just one swipe. Available in 20 stunning shades.', 'Rich, long-lasting matte lipstick with vitamin E', 1299.00, 999.00, 23, 150, 'NSS-LIP-001', 'NSS Skin & Beauty', 1, 1, 0, 'active'),
(1, 'Glow Foundation SPF 30', 'glow-foundation-spf30', 'Achieve a flawless, radiant complexion with our Glow Foundation. This lightweight, buildable formula offers medium to full coverage while protecting your skin with SPF 30. Enriched with hyaluronic acid for 24-hour hydration. Suitable for all skin types.', 'Lightweight foundation with SPF 30 and 24hr hydration', 2499.00, 1999.00, 20, 80, 'NSS-FND-001', 'NSS Skin & Beauty', 1, 1, 0, 'active'),
(1, 'Nude Eyeshadow Palette - 12 Shades', 'nude-eyeshadow-palette-12-shades', 'Create endless eye looks with our 12-shade Nude Eyeshadow Palette. From matte to shimmer, this palette features a curated selection of neutral tones that complement every skin tone. Long-lasting formula with superior pigmentation.', '12-shade palette with matte and shimmer finishes', 3499.00, 2799.00, 20, 60, 'NSS-EYE-001', 'NSS Skin & Beauty', 1, 0, 1, 'active'),
(1, 'Waterproof Mascara - Volume & Length', 'waterproof-mascara-volume-length', 'Define and volumize your lashes with our Waterproof Mascara. The unique wand separates and coats each lash for dramatic volume and length without clumping. Waterproof formula lasts all day through heat and humidity.', 'Volumizing waterproof mascara for dramatic lashes', 899.00, NULL, 0, 200, 'NSS-MAS-001', 'NSS Skin & Beauty', 0, 1, 1, 'active'),
(2, 'Rose Gold Vitamin C Serum', 'rose-gold-vitamin-c-serum', 'Reveal brighter, more youthful skin with our Rose Gold Vitamin C Serum. Formulated with 20% stabilized Vitamin C, hyaluronic acid, and rose hip oil, this serum targets dark spots, uneven skin tone, and fine lines. Dermatologist tested and suitable for all skin types.', 'Brightening serum with 20% Vitamin C and hyaluronic acid', 2999.00, 2499.00, 17, 100, 'NSS-SER-001', 'NSS Skin & Beauty', 1, 1, 0, 'active'),
(2, 'Pearl Glow Moisturizer SPF 20', 'pearl-glow-moisturizer-spf20', 'Lock in hydration for up to 48 hours with our Pearl Glow Moisturizer. This rich yet lightweight cream absorbs quickly and leaves skin feeling soft and luminous. With SPF 20 sun protection and skin-loving pearl extract.', 'Hydrating moisturizer with SPF 20 and pearl extract', 1799.00, 1499.00, 17, 120, 'NSS-MOI-001', 'NSS Skin & Beauty', 0, 1, 0, 'active'),
(2, 'Charcoal Deep Cleansing Mask', 'charcoal-deep-cleansing-mask', 'Purify and detoxify your skin with our Charcoal Deep Cleansing Mask. Activated charcoal draws out impurities, excess oil, and blackheads from deep within pores. Leave on for 15 minutes for visibly cleaner, smoother skin.', 'Activated charcoal mask for deep pore cleansing', 1299.00, NULL, 0, 90, 'NSS-MSK-001', 'NSS Skin & Beauty', 0, 0, 1, 'active'),
(2, 'Anti-Aging Night Cream - 50ml', 'anti-aging-night-cream-50ml', 'Wake up to younger-looking skin with our Anti-Aging Night Cream. This rich, restorative formula works overnight to reduce the appearance of fine lines and wrinkles. Enriched with retinol, peptides, and collagen-boosting ingredients.', 'Overnight anti-aging cream with retinol and peptides', 3499.00, 2999.00, 14, 75, 'NSS-NIG-001', 'NSS Skin & Beauty', 1, 0, 1, 'active'),
(3, 'Keratin Hair Mask - Repair & Shine', 'keratin-hair-mask-repair-shine', 'Restore damaged hair to its former glory with our Keratin Hair Mask. This intensive treatment penetrates deep into the hair shaft to repair damage, reduce frizz, and add brilliant shine. Enriched with keratin protein, argan oil, and biotin.', 'Intensive keratin treatment for damaged hair', 1999.00, 1599.00, 20, 85, 'NSS-HAI-001', 'NSS Skin & Beauty', 0, 1, 0, 'active'),
(3, 'Argan Oil Hair Serum - Frizz Control', 'argan-oil-hair-serum-frizz-control', 'Tame frizz and add brilliant shine with our Argan Oil Hair Serum. Lightweight and non-greasy, this serum coats each strand with pure Moroccan argan oil for an incredibly smooth, lustrous finish. Works on all hair types.', 'Lightweight argan oil serum for frizz-free shine', 1199.00, NULL, 0, 130, 'NSS-HAI-002', 'NSS Skin & Beauty', 0, 1, 1, 'active'),
(4, 'Rose Quartz Facial Roller', 'rose-quartz-facial-roller', 'Experience the ancient beauty benefits of rose quartz with our Facial Roller. This dual-ended roller helps to depuff, improve circulation, and enhance product absorption. The cooling stone helps reduce inflammation and boost lymphatic drainage.', 'Dual-ended rose quartz roller for facial massage', 1499.00, 1199.00, 20, 110, 'NSS-TOO-001', 'NSS Skin & Beauty', 1, 0, 1, 'active'),
(4, 'Electric Face Massager & Cleansing Brush', 'electric-face-massager-cleansing-brush', 'Elevate your skincare routine with our Electric Face Massager & Cleansing Brush. With 3 speeds and 5 brush heads, it provides a deep, yet gentle cleanse while the sonic vibration stimulates collagen production and improves blood circulation.', 'Sonic cleansing brush with 3 speeds and 5 attachments', 4999.00, 3999.00, 20, 45, 'NSS-TOO-002', 'NSS Skin & Beauty', 1, 0, 1, 'active'),
(5, 'Bloom - Floral Eau de Parfum 50ml', 'bloom-floral-eau-de-parfum-50ml', 'Bloom is a captivating floral fragrance that opens with notes of fresh bergamot and pink pepper. The heart reveals a bouquet of jasmine, rose, and peony, while the base of warm musk and sandalwood lingers beautifully on the skin.', 'Floral fragrance with jasmine, rose, and warm musk', 3999.00, 3499.00, 13, 60, 'NSS-FRA-001', 'NSS Skin & Beauty', 1, 1, 0, 'active'),
(5, 'Gold Oud - Oriental Perfume 100ml', 'gold-oud-oriental-perfume-100ml', 'Gold Oud is a rich, luxurious oriental fragrance that commands attention. Opening with spicy cardamom and saffron, it transitions to a heart of dark roses and precious oud wood, finishing with warm amber and vanilla.', 'Luxurious oriental perfume with oud and amber', 5999.00, NULL, 0, 35, 'NSS-FRA-002', 'NSS Skin & Beauty', 1, 0, 1, 'active');

-- Coupons
INSERT INTO `coupons` (`code`, `type`, `value`, `min_order`, `max_discount`, `usage_limit`, `start_date`, `end_date`, `status`) VALUES
('WELCOME10', 'percent', 10.00, 500.00, 200.00, 1000, '2026-01-01', '2026-12-31', 'active'),
('SAVE200', 'flat', 200.00, 2000.00, NULL, 500, '2026-01-01', '2026-12-31', 'active'),
('BEAUTY20', 'percent', 20.00, 1500.00, 500.00, 200, '2026-01-01', '2026-06-30', 'active'),
('NSBEAUTY', 'percent', 15.00, 1000.00, 300.00, NULL, '2026-01-01', '2026-12-31', 'active');

-- Banners
INSERT INTO `banners` (`title`, `subtitle`, `image`, `link`, `button_text`, `position`, `status`, `sort_order`) VALUES
('Discover Your Beauty', 'Premium beauty products crafted with love for your skin', 'banner1.jpg', 'shop.php', 'Shop Now', 'hero', 'active', 1),
('New Spring Collection', 'Fresh arrivals - Embrace the season with NSS Skin & Beauty', 'banner2.jpg', 'shop.php?filter=new', 'Explore Now', 'hero', 'active', 2),
('Up to 30% Off Skincare', 'Limited time offer on our bestselling skincare range', 'banner3.jpg', 'category.php?slug=skincare', 'Shop Skincare', 'promo', 'active', 1);

-- Settings
INSERT INTO `settings` (`key`, `value`) VALUES
('site_name', 'NSS Skin & Beauty'),
('site_tagline', 'Embrace Your Natural Beauty'),
('site_email', 'info@nssskinbeauty.com'),
('site_phone', '+92-300-1234567'),
('site_address', 'Gulshan-e-Iqbal, Karachi, Pakistan'),
('currency', 'PKR'),
('currency_symbol', 'Rs.'),
('shipping_charge', '200'),
('free_shipping_min', '3000'),
('facebook_url', 'https://facebook.com/nsbeauty'),
('instagram_url', 'https://instagram.com/nsbeauty'),
('twitter_url', 'https://twitter.com/nsbeauty'),
('youtube_url', 'https://youtube.com/nsbeauty'),
('tiktok_url', 'https://tiktok.com/@nsbeauty');

-- Sample Reviews
INSERT INTO `reviews` (`product_id`, `user_id`, `rating`, `title`, `comment`, `status`) VALUES
(1, 2, 5, 'Absolutely love this lipstick!', 'This is hands down the best lipstick I have ever tried. The color is so rich and pigmented, and it lasts all day without fading. Love the matte finish!', 'approved'),
(1, 3, 4, 'Great color, slightly drying', 'Beautiful color and great pigmentation. Only giving 4 stars because after a few hours it becomes slightly drying. Overall very happy with my purchase!', 'approved'),
(5, 2, 5, 'Transformed my skin!', 'I have been using this serum for 3 weeks and my skin is already noticeably brighter. Dark spots are fading and my skin feels so hydrated. Highly recommend!', 'approved'),
(5, 4, 5, 'Worth every penny', 'Amazing serum! My skin looks so much healthier and glowing. I have tried many vitamin C serums but this one is by far the best.', 'approved'),
(11, 3, 5, 'Best beauty tool purchase ever!', 'I love this facial roller so much! I use it every morning with my serum and my face looks so depuffed and glowing. Perfect gift for a beauty lover.', 'approved'),
(13, 4, 5, 'This fragrance is stunning!', 'Bloom is absolutely divine. It lasts on my skin for easily 8 hours and I get so many compliments. The floral notes are perfectly balanced.', 'approved');
