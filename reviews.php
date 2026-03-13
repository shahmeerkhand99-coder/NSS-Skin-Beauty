<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireAdmin();
$msg = '';
// Delete review
if (isset($_GET['delete'])) { db()->execute("DELETE FROM reviews WHERE id=?",[(int)$_GET['delete']]); $msg='Review deleted.'; }
// Approve
if (isset($_GET['approve'])) { db()->execute("UPDATE reviews SET status='approved' WHERE id=?",[(int)$_GET['approve']]); $msg='Review approved.'; }
$reviews = db()->fetchAll("SELECT r.*, u.name as user_name, p.name as product_name FROM reviews r JOIN users u ON u.id=r.user_id JOIN products p ON p.id=r.product_id ORDER BY r.created_at DESC");
$adminUser = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Manage Reviews - NSS Skin & Beauty Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        body { background: #f8fafc; }
        .review-text { color: var(--gray-600); font-size: 0.85rem; line-height: 1.6; font-style: italic; background: #f9fafb; padding: 12px; border-radius: 12px; border-left: 4px solid var(--pink-light); }
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

            <div class="admin-card shadow-premium">
                <div class="admin-card-header">
                    <div>
                        <h3>Customer Reviews</h3>
                        <p style="color:var(--gray-500);font-size:0.85rem">Moderate product feedback and ratings</p>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product & Customer</th>
                                <th>Rating</th>
                                <th>Review Content</th>
                                <th>Status</th>
                                <th>Posted On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $r): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:700;color:var(--dark);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="<?= sanitize($r['product_name']) ?>">
                                            <?= sanitize($r['product_name']) ?>
                                        </div>
                                        <div style="font-size:0.75rem;color:var(--pink);font-weight:600">by <?= sanitize($r['user_name']) ?></div>
                                    </td>
                                    <td>
                                        <div style="color:var(--gold);font-size:0.8rem"><?= getStarRating($r['rating']) ?></div>
                                        <div style="font-size:0.7rem;font-weight:700;margin-top:2px"><?= $r['rating'] ?>.0 / 5</div>
                                    </td>
                                    <td>
                                        <div class="review-text">
                                            <?php if($r['title']): ?><div style="font-weight:700;color:var(--dark);margin-bottom:4px"><?= sanitize($r['title']) ?></div><?php endif; ?>
                                            "<?= sanitize(substr($r['comment'], 0, 100)) ?><?= strlen($r['comment']) > 100 ? '...' : '' ?>"
                                        </div>
                                    </td>
                                    <td>
                                        <span class="order-status status-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span>
                                    </td>
                                    <td>
                                        <div style="font-size:0.85rem"><?= date('M d, Y', strtotime($r['created_at'])) ?></div>
                                        <div style="font-size:0.75rem;color:var(--gray-400)"><?= date('h:i A', strtotime($r['created_at'])) ?></div>
                                    </td>
                                    <td class="table-actions">
                                        <?php if ($r['status'] === 'pending'): ?>
                                            <a href="?approve=<?= $r['id'] ?>" class="btn btn-sm btn-edit" title="Approve"><i class="fas fa-check"></i></a>
                                        <?php endif; ?>
                                        <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete this review?')" title="Delete"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
