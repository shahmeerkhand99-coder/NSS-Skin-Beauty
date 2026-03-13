<?php
// NS Beauty - Main Configuration
session_start();

// Site Settings
define('SITE_NAME', 'NSS Skin & Beauty');
define('SITE_TAGLINE', 'Nurturing Skin & Soul');
define('SITE_URL', 'http://localhost/app.index');
define('SITE_EMAIL', 'info@nssskinbeauty.com');
define('SITE_PHONE', '+92-300-1234567');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/assets/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/uploads/');
define('IMAGES_URL', SITE_URL . '/assets/images/');

// Currency
define('CURRENCY', 'PKR');
define('CURRENCY_SYMBOL', 'Rs.');

// Shipping
define('SHIPPING_CHARGE', 200);
define('FREE_SHIPPING_MIN', 3000);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);

// Admin credentials check
define('ADMIN_EMAIL', 'admin@nssskinbeauty.com');

// Include database
require_once ROOT_PATH . '/config/database.php';

// Error reporting (development: show all errors)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Timezone
date_default_timezone_set('Asia/Karachi');
