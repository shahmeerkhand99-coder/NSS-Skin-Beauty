<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireAdmin();
$msg = '';
if (isset($_GET['delete'])) { db()->execute("DELETE FROM categories WHERE id=?",[(int)$_GET['delete']]); $msg='Category deleted.'; }
if ($_POST) {
    $name = sanitize($_POST['name']);
    $slug = makeSlug($name);
    $desc = sanitize($_POST['description']);
    $icon = sanitize($_POST['icon']);
    $id = (int)($_POST['id']??0);
    if ($id) {
        db()->execute("UPDATE categories SET name=?, slug=?, description=?, icon=? WHERE id=?",[$name,$slug,$desc,$icon,$id]);
        $msg = 'Category updated!';
    } else {
        db()->insert("INSERT INTO categories(name,slug,description,icon,status,sort_order) VALUES(?,?,?,?,'active',99)",[$name,$slug,$desc,$icon]);
        $msg = 'Category added!';
    }
}
$editId = (int)($_GET['edit']??0);
$editCat = $editId ? db()->fetchOne("SELECT * FROM categories WHERE id=?",[$editId]) : null;
$categories = db()->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id=c.id) as pcount FROM categories c ORDER BY c.sort_order");
$adminUser = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Manage Categories - NSS Skin & Beauty Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        body { background: #f8fafc; }
        .category-icon-preview { width: 44px; height: 44px; background: var(--pink-light); color: var(--pink); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
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

            <div style="display:grid;grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
                <!-- List -->
                <div class="admin-card shadow-premium">
                    <div class="admin-card-header">
                        <div>
                            <h3>Categories</h3>
                            <p style="color:var(--gray-500);font-size:0.85rem">Organize your products into logical groups</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Icon</th>
                                    <th>Category Name</th>
                                    <th>Products</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $c): ?>
                                    <tr>
                                        <td><div class="category-icon-preview"><i class="<?= $c['icon'] ?: 'fas fa-folder' ?>"></i></div></td>
                                        <td>
                                            <div style="font-weight:700;color:var(--dark)"><?= sanitize($c['name']) ?></div>
                                            <div style="font-size:0.75rem;color:var(--gray-400)"><?= $c['slug'] ?></div>
                                        </td>
                                        <td><span style="font-weight:600"><?= $c['pcount'] ?></span> <span style="font-size:0.8rem;color:var(--gray-500)">Products</span></td>
                                        <td><span class="order-status status-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span></td>
                                        <td class="table-actions">
                                            <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete? This may affect products in this category.')" title="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form -->
                <div class="admin-card shadow-premium stick-top">
                    <div class="admin-card-header">
                        <h3><?= $editCat ? 'Edit Category' : 'Add Category' ?></h3>
                    </div>
                    <form method="POST" class="mt-16">
                        <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
                        
                        <div class="form-group">
                            <label>Display Name *</label>
                            <input type="text" name="name" class="form-control" value="<?= sanitize($editCat['name']??'') ?>" placeholder="e.g. Skin Care" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Icon Class <a href="https://fontawesome.com/v5/search?m=free" target="_blank" style="font-size:0.7rem;color:var(--pink)">(Search Icons)</a></label>
                            <input type="text" name="icon" class="form-control" value="<?= sanitize($editCat['icon']??'') ?>" placeholder="fas fa-leaf">
                        </div>
                        
                        <div class="form-group">
                            <label>Description (Optional)</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Briefly describe what goes in here..."><?= sanitize($editCat['description']??'') ?></textarea>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:10px;margin-top:20px">
                            <button type="submit" class="btn btn-primary" style="justify-content:center"><i class="fas fa-save"></i> <?= $editCat ? 'Update' : 'Create' ?> Category</button>
                            <?php if ($editCat): ?>
                                <a href="categories.php" class="btn btn-outline" style="justify-content:center">Cancel Edit</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
