<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

echo "<h2>Updating Database for NSS Skin & Beauty...</h2>";

try {
    // 1. Update Admin User Name and Email
    db()->execute("UPDATE users SET name = 'NSS Skin & Beauty Admin', email = 'admin@nssskinbeauty.com' WHERE role = 'admin'");
    echo "<p>✓ Admin user updated.</p>";

    // 2. Update Settings Table
    db()->execute("UPDATE settings SET value = 'NSS Skin & Beauty' WHERE `key` = 'site_name'");
    db()->execute("UPDATE settings SET value = 'Nurturing Skin & Soul' WHERE `key` = 'site_tagline'");
    db()->execute("UPDATE settings SET value = 'info@nssskinbeauty.com' WHERE `key` = 'site_email'");
    db()->execute("UPDATE settings SET value = 'https://instagram.com/nssskinbeauty' WHERE `key` = 'instagram_url'");
    echo "<p>✓ Settings table updated.</p>";

    // 3. Update Brands in Products (optional but good for consistency)
    db()->execute("UPDATE products SET brand = 'NSS Skin & Beauty' WHERE brand = 'NS Beauty'");
    echo "<p>✓ Product brands updated.</p>";

    echo "<h3>All updates completed successfully!</h3>";
    echo "<p>Please refresh your dashboard. You can delete this file now.</p>";

} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
