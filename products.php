<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireAdmin();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);
$msg = '';

// Delete
if ($_GET['delete'] ?? false) {
    db()->execute("DELETE FROM products WHERE id=?",[(int)$_GET['delete']]);
    $msg = 'Product deleted.';
}

// Save (add/edit)
if ($_POST) {
    $data = [
        'category_id' => (int)$_POST['category_id'],
        'name' => sanitize($_POST['name']),
        'slug' => makeSlug($_POST['name']),
        'description' => sanitize($_POST['description']),
        'short_description' => sanitize($_POST['short_description']),
        'ingredients' => sanitize($_POST['ingredients']),
        'how_to_use' => sanitize($_POST['how_to_use']),
        'price' => (float)$_POST['price'],
        'sale_price' => !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null,
        'discount_percent' => !empty($_POST['sale_price']) ? round((($_POST['price']-$_POST['sale_price'])/$_POST['price'])*100) : 0,
        'stock' => (int)$_POST['stock'],
        'sku' => sanitize($_POST['sku']),
        'weight' => sanitize($_POST['weight']),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'is_bestseller' => isset($_POST['is_bestseller']) ? 1 : 0,
        'is_new' => isset($_POST['is_new']) ? 1 : 0,
        'status' => $_POST['status'],
    ];
    // Handle image upload
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . 'products/' . $image);
        $data['image'] = $image;
    }
    if ($id) {
        $sets = implode(',', array_map(fn($k) => "`$k`=?", array_keys($data)));
        db()->execute("UPDATE products SET $sets WHERE id=?", [...array_values($data), $id]);
        $msg = 'Product updated!';
    } else {
        $cols = implode(',', array_map(fn($k) => "`$k`", array_keys($data)));
        $vals = implode(',', array_fill(0, count($data), '?'));
        $id = db()->insert("INSERT INTO products($cols) VALUES($vals)", array_values($data));
        $msg = 'Product added!';
    }
    $action = 'list';
}

$categories = getAllCategories();
$editProduct = $action === 'edit' && $id ? getProductById($id) : null;
$products = db()->fetchAll("SELECT p.*, c.name as cat_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Manage Products - NSS Skin & Beauty Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        body { background: #f8fafc; }
        .product-img-admin { width: 50px; height: 50px; border-radius: 12px; object-fit: cover; border: 1px solid var(--gray-200); }
        .stock-badge { padding: 4px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .stock-low { background: #fff1f0; color: #cf1322; border: 1px solid #ffa39e; }
        .stock-mid { background: #fff7e6; color: #d46b08; border: 1px solid #ffd591; }
        .stock-ok { background: #f6ffed; color: #389e0d; border: 1px solid #b7eb8f; }
    </style>
</head>
<body>
<div class="admin-body">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include 'includes/topbar.php'; ?>
        <div class="admin-content">
            <?php if ($msg): ?>
                <div class="toast show shadow-premium" style="position:static;margin-bottom:20px;width:100%;max-width:none">
                    <i class="fas fa-check-circle"></i> <span><?= $msg ?></span>
                </div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
                <div class="admin-card shadow-premium">
                    <div class="admin-card-header">
                        <div>
                            <h3>Product Inventory</h3>
                            <p style="color:var(--gray-500);font-size:0.85rem">Manage your beauty products and stock levels</p>
                        </div>
                        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Product</a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product Details</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <?php 
                                        $stockClass = $p['stock'] <= 5 ? 'stock-low' : ($p['stock'] <= 20 ? 'stock-mid' : 'stock-ok');
                                        $priceHtml = $p['sale_price'] ? 
                                            '<div style="font-weight:700;color:var(--pink)">'.formatPrice($p['sale_price']).'</div><del style="font-size:0.75rem;color:var(--gray-400)">'.formatPrice($p['price']).'</del>' : 
                                            '<div style="font-weight:700">'.formatPrice($p['price']).'</div>';
                                    ?>
                                    <tr>
                                        <td>
                                            <img src="<?= $p['image'] ? UPLOAD_URL.'products/'.$p['image'] : 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=80&q=80' ?>" class="product-img-admin" alt="">
                                        </td>
                                        <td>
                                            <div style="font-weight:600;color:var(--dark)"><?= sanitize($p['name']) ?></div>
                                            <div style="font-size:0.75rem;color:var(--gray-500)">SKU: <?= $p['sku'] ?: 'N/A' ?></div>
                                        </td>
                                        <td><span class="badge-pill" style="background:var(--gray-100);color:var(--gray-700);box-shadow:none"><?= sanitize($p['cat_name']) ?></span></td>
                                        <td><?= $priceHtml ?></td>
                                        <td><span class="stock-badge <?= $stockClass ?>"><?= $p['stock'] ?> in stock</span></td>
                                        <td>
                                            <span class="order-status status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span>
                                        </td>
                                        <td class="table-actions">
                                            <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="<?= SITE_URL ?>/product.php?slug=<?= $p['slug'] ?>" target="_blank" class="btn btn-sm btn-view" title="View"><i class="fas fa-eye"></i></a>
                                            <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Archive this product?')" title="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php else: ?>
                <div class="admin-card shadow-premium" style="max-width:900px;margin:0 auto">
                    <div class="admin-card-header">
                        <h3><?= $id ? 'Edit Product' : 'Create New Product' ?></h3>
                    </div>
                    <form method="POST" enctype="multipart/form-data" class="mt-24">
                        <div class="form-row">
                            <div class="form-group" style="flex:2">
                                <label>Product Title *</label>
                                <input type="text" name="name" class="form-control" value="<?= sanitize($editProduct['name']??'') ?>" placeholder="e.g. Radiant Glow Serum" required>
                            </div>
                            <div class="form-group">
                                <label>Category *</label>
                                <select name="category_id" class="form-control">
                                    <?php foreach($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= ($editProduct['category_id']??0)==$c['id']?'selected':'' ?>><?= $c['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Short Description (Displays in card)</label>
                            <input type="text" name="short_description" class="form-control" value="<?= sanitize($editProduct['short_description']??'') ?>" placeholder="Brief 1-line summary...">
                        </div>

                        <div class="form-group">
                            <label>Product Description</label>
                            <textarea name="description" class="form-control" rows="5"><?= sanitize($editProduct['description']??'') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Price (Rs.) *</label>
                                <input type="number" name="price" class="form-control" value="<?= $editProduct['price']??'' ?>" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label>Sale Price (Optional)</label>
                                <input type="number" name="sale_price" class="form-control" value="<?= $editProduct['sale_price']??'' ?>" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Stock Level *</label>
                                <input type="number" name="stock" class="form-control" value="<?= $editProduct['stock']??0 ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>SKU Label</label>
                                <input type="text" name="sku" class="form-control" value="<?= sanitize($editProduct['sku']??'') ?>" placeholder="NS-001">
                            </div>
                            <div class="form-group">
                                <label>Weight/Size</label>
                                <input type="text" name="weight" class="form-control" value="<?= sanitize($editProduct['weight']??'') ?>" placeholder="e.g. 30ml / 50g">
                            </div>
                        </div>

                        <div class="form-row mt-16">
                            <div class="form-group" style="flex:2">
                                <label>Product Images</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <?php if($editProduct['image']??null): ?>
                                    <div style="margin-top:10px">
                                        <img src="<?= UPLOAD_URL.'products/'.$editProduct['image'] ?>" style="width:100px;border-radius:10px;border:1px solid #ddd">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="form-group" style="background:#f9f9f9;padding:15px;border-radius:12px;display:flex;flex-direction:column;gap:12px">
                                <label style="font-weight:700;font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;color:var(--gray-500)">Badges & Status</label>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                                    <label class="checkbox-label"><input type="checkbox" name="is_featured" <?= ($editProduct['is_featured']??0)?'checked':'' ?>> Featured</label>
                                    <label class="checkbox-label"><input type="checkbox" name="is_bestseller" <?= ($editProduct['is_bestseller']??0)?'checked':'' ?>> Best Seller</label>
                                    <label class="checkbox-label"><input type="checkbox" name="is_new" <?= ($editProduct['is_new']??1)?'checked':'' ?>> New Arrival</label>
                                </div>
                                <select name="status" class="form-control mt-8">
                                    <option value="active" <?= ($editProduct['status']??'active')==='active'?'selected':'' ?>>Active</option>
                                    <option value="inactive" <?= ($editProduct['status']??'')==='inactive'?'selected':'' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row mt-16">
                            <div class="form-group">
                                <label>Ingredients</label>
                                <textarea name="ingredients" class="form-control" rows="3"><?= sanitize($editProduct['ingredients']??'') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>How to Use</label>
                                <textarea name="how_to_use" class="form-control" rows="3"><?= sanitize($editProduct['how_to_use']??'') ?></textarea>
                            </div>
                        </div>

                        <div style="display:flex;gap:12px;margin-top:32px;padding-top:24px;border-top:1px solid var(--gray-100)">
                            <button type="submit" class="btn btn-primary" style="padding:12px 30px"><i class="fas fa-save"></i> Save Changes</button>
                            <a href="products.php" class="btn btn-outline" style="padding:12px 30px">Discard</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
