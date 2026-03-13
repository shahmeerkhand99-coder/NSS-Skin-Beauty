<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
header('Content-Type: application/json');

try {
    // Sample products to import
    $products = [
        [
            'id' => 1,
            'category_id' => 1,
            'name' => 'Velvet Matte Lipstick - Rose Red',
            'slug' => 'velvet-matte-lipstick-rose-red',
            'description' => 'Rich, long-lasting matte lipstick with vitamin E',
            'short_description' => 'Luxurious matte finish lipstick',
            'price' => 1299.00,
            'sale_price' => 999.00,
            'stock' => 150,
            'sku' => 'NS-LIP-001',
            'brand' => 'NS Beauty',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 0,
            'status' => 'active'
        ],
        [
            'id' => 2,
            'category_id' => 1,
            'name' => 'Glow Foundation SPF 30',
            'slug' => 'glow-foundation-spf30',
            'description' => 'Lightweight foundation with SPF 30 and 24hr hydration',
            'short_description' => 'Radiant coverage with sun protection',
            'price' => 2499.00,
            'sale_price' => 1999.00,
            'stock' => 80,
            'sku' => 'NS-FND-001',
            'brand' => 'NS Beauty',
            'is_featured' => 1,
            'is_bestseller' => 1,
            'is_new' => 0,
            'status' => 'active'
        ],
        [
            'id' => 3,
            'category_id' => 2,
            'name' => 'Charcoal Deep Cleansing Mask',
            'slug' => 'charcoal-deep-cleansing-mask',
            'description' => 'Deep cleansing mask with activated charcoal',
            'short_description' => 'Purifying face mask for all skin types',
            'price' => 899.00,
            'sale_price' => null,
            'stock' => 100,
            'sku' => 'NS-MASK-001',
            'brand' => 'NS Beauty',
            'is_featured' => 0,
            'is_bestseller' => 0,
            'is_new' => 1,
            'status' => 'active'
        ],
        [
            'id' => 4,
            'category_id' => 3,
            'name' => 'Rose Gold Eyeshadow Palette',
            'slug' => 'rose-gold-eyeshadow-palette',
            'description' => '12-shade palette with matte and shimmer finishes',
            'short_description' => 'Versatile eyeshadow palette',
            'price' => 3499.00,
            'sale_price' => 2799.00,
            'stock' => 60,
            'sku' => 'NS-EYE-001',
            'brand' => 'NS Beauty',
            'is_featured' => 1,
            'is_bestseller' => 0,
            'is_new' => 1,
            'status' => 'active'
        ]
    ];
    
    $imported = 0;
    foreach ($products as $product) {
        // Check if product exists
        $existing = db()->fetchOne("SELECT id FROM products WHERE id = ?", [$product['id']]);
        
        if (!$existing) {
            // Insert new product
            db()->insert("INSERT INTO products (id, category_id, name, slug, description, short_description, price, sale_price, stock, sku, brand, is_featured, is_bestseller, is_new, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())", 
                [
                    $product['id'], $product['category_id'], $product['name'], $product['slug'], 
                    $product['description'], $product['short_description'], $product['price'], 
                    $product['sale_price'], $product['stock'], $product['sku'], $product['brand'], 
                    $product['is_featured'], $product['is_bestseller'], $product['is_new'], 
                    $product['status']
                ]
            );
            $imported++;
        }
    }
    
    echo json_encode(['success' => true, 'message' => "Successfully imported {$imported} sample products!"]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
