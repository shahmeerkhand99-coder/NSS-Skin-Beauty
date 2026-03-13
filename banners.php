<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireAdmin();

$msg = '';
$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);

// Basic layout variables
$adminUser = $_SESSION['user_name'] ?? 'Admin';

// Delete banner
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $banner = db()->fetchOne("SELECT image FROM banners WHERE id = ?", [$did]);
    if ($banner) {
        $imgPath = UPLOAD_PATH . 'banners/' . $banner['image'];
        if (file_exists($imgPath)) @unlink($imgPath);
        db()->execute("DELETE FROM banners WHERE id = ?", [$did]);
        $msg = 'Banner deleted successfully.';
    }
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $subtitle = sanitize($_POST['subtitle']);
    $link = sanitize($_POST['link']);
    $buttonText = sanitize($_POST['button_text']);
    $position = sanitize($_POST['position']);
    $status = sanitize($_POST['status']);
    $sortOrder = (int)$_POST['sort_order'];
    $bid = (int)($_POST['id'] ?? 0);

    $oldImage = '';
    if ($bid) {
        $existing = db()->fetchOne("SELECT image FROM banners WHERE id = ?", [$bid]);
        $oldImage = $existing['image'];
    }

    $imageName = $oldImage;

    // Handle Image Upload
    if (!empty($_FILES['banner_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($ext, $allowed)) {
            $imageName = 'banner_' . time() . '.' . $ext;
            $uploadPath = UPLOAD_PATH . 'banners/' . $imageName;
            
            if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $uploadPath)) {
                // Delete old image if editing
                if ($oldImage && file_exists(UPLOAD_PATH . 'banners/' . $oldImage)) {
                    @unlink(UPLOAD_PATH . 'banners/' . $oldImage);
                }
            } else {
                $msg = 'Failed to upload image.';
            }
        } else {
            $msg = 'Invalid image format. Only JPG, PNG, and WEBP allowed.';
        }
    }

    if (!$msg) {
        if ($bid) {
            db()->execute(
                "UPDATE banners SET title=?, subtitle=?, image=?, link=?, button_text=?, position=?, status=?, sort_order=? WHERE id=?",
                [$title, $subtitle, $imageName, $link, $buttonText, $position, $status, $sortOrder, $bid]
            );
            $msg = 'Banner updated successfully!';
        } else {
            db()->insert(
                "INSERT INTO banners (title, subtitle, image, link, button_text, position, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$title, $subtitle, $imageName, $link, $buttonText, $position, $status, $sortOrder]
            );
            $msg = 'Banner added successfully!';
        }
        $action = 'list';
    }
}

$editBanner = ($action === 'edit' && $editId) ? db()->fetchOne("SELECT * FROM banners WHERE id = ?", [$editId]) : null;
$banners = db()->fetchAll("SELECT * FROM banners ORDER BY position, sort_order ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Banners - NSS Skin & Beauty Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>body{overflow-x:hidden;background:#f4f6fb;}</style>
</head>
<body>
<div class="admin-body">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include 'includes/topbar.php'; ?>
        <div class="admin-content">
            
            <?php if ($msg): ?>
            <div style="background:#d4edda;color:#155724;padding:12px 18px;border-radius:10px;margin-bottom:16px"><i class="fas fa-check-circle"></i> <?= $msg ?></div>
            <?php endif; ?>

            <?php if ($action === 'list'): ?>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3>All Banners (<?= count($banners) ?>)</h3>
                    <a href="?action=add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Banner</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title / Subtitle</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Sort</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($banners as $b): ?>
                        <tr>
                            <td>
                                <img src="<?= UPLOAD_URL ?>banners/<?= $b['image'] ?>" alt="" style="width:100px; height:50px; object-fit:cover; border-radius:4px; border:1px solid var(--gray-200)">
                            </td>
                            <td>
                                <strong style="display:block; font-size:0.9rem"><?= sanitize($b['title']) ?></strong>
                                <span style="font-size:0.75rem; color:var(--gray-600)"><?= sanitize($b['subtitle']) ?></span>
                            </td>
                            <td><span class="badge" style="background:var(--pink-light); color:var(--pink); padding:4px 8px; border-radius:4px; font-size:0.7rem; font-weight:700; text-transform:uppercase"><?= $b['position'] ?></span></td>
                            <td><span style="font-size:0.8rem; font-weight:600; color:<?= $b['status']==='active'?'#27ae60':'#e74c3c' ?>"><?= ucfirst($b['status']) ?></span></td>
                            <td><?= $b['sort_order'] ?></td>
                            <td class="table-actions">
                                <a href="?action=edit&id=<?= $b['id'] ?>" class="btn btn-edit btn-sm">Edit</a>
                                <a href="?delete=<?= $b['id'] ?>" class="btn btn-delete btn-sm" onclick="return confirm('Delete this banner?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php else: ?>
            <div class="admin-card" style="max-width:800px; margin: 0 auto;">
                <h3 style="margin-bottom:24px"><?= $editBanner ? 'Edit Banner' : 'Add New Banner' ?></h3>
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($editBanner): ?><input type="hidden" name="id" value="<?= $editBanner['id'] ?>"><?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Banner Title</label>
                            <input type="text" name="title" class="form-control" value="<?= sanitize($editBanner['title'] ?? '') ?>" placeholder="e.g. Discover Your Beauty">
                        </div>
                        <div class="form-group">
                            <label>Subtitle / Description</label>
                            <input type="text" name="subtitle" class="form-control" value="<?= sanitize($editBanner['subtitle'] ?? '') ?>" placeholder="e.g. Premium products for your skin">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Button Text</label>
                            <input type="text" name="button_text" class="form-control" value="<?= sanitize($editBanner['button_text'] ?? 'Shop Now') ?>" placeholder="e.g. Shop Now">
                        </div>
                        <div class="form-group">
                            <label>Link (URL)</label>
                            <input type="text" name="link" class="form-control" value="<?= sanitize($editBanner['link'] ?? 'shop.php') ?>" placeholder="e.g. shop.php">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Position</label>
                            <select name="position" class="form-control">
                                <option value="hero" <?= ( ($editBanner['position'] ?? '') === 'hero' ) ? 'selected' : '' ?>>Hero Slider (Home)</option>
                                <option value="promo" <?= ( ($editBanner['position'] ?? '') === 'promo' ) ? 'selected' : '' ?>>Promotional Middle</option>
                                <option value="sidebar" <?= ( ($editBanner['position'] ?? '') === 'sidebar' ) ? 'selected' : '' ?>>Sidebar Ad</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?= ( ($editBanner['status'] ?? 'active') === 'active' ) ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ( ($editBanner['status'] ?? '') === 'inactive' ) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= $editBanner['sort_order'] ?? 0 ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Banner Image (Recommended size for Hero: 1920x800px)</label>
                        <input type="file" name="banner_image" class="form-control" accept="image/*" <?= $editBanner ? '' : 'required' ?>>
                        <?php if ($editBanner): ?>
                        <div style="margin-top:10px">
                            <p style="font-size:0.8rem; color:var(--gray-600); margin-bottom:4px">Current Image:</p>
                            <img src="<?= UPLOAD_URL ?>banners/<?= $editBanner['image'] ?>" alt="" style="width:200px; height:100px; object-fit:cover; border-radius:8px; border:1px solid var(--gray-200)">
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="display:flex; gap:12px; margin-top:24px">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Banner</button>
                        <a href="banners.php" class="btn btn-outline">Cancel</a>
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
