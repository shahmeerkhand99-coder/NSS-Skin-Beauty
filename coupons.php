<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireAdmin();
$msg = '';
if (isset($_GET['delete'])) { db()->execute("DELETE FROM coupons WHERE id=?",[(int)$_GET['delete']]); $msg='Coupon deleted.'; }
if ($_POST) {
    $code = strtoupper(sanitize($_POST['code']));
    if (!db()->fetchOne("SELECT id FROM coupons WHERE code=?",[$code])) {
        $maxDiscount = !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null;
        $usageLimit = !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null;
        $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        db()->insert("INSERT INTO coupons(code,type,value,min_order,max_discount,usage_limit,start_date,end_date,status) VALUES(?,?,?,?,?,?,?,?,'active')",
            [$code, $_POST['type'], (float)$_POST['value'], (float)($_POST['min_order']??0), $maxDiscount, $usageLimit, $startDate, $endDate]);
        $msg = 'Coupon created!';
    } else { $msg = 'Coupon code already exists.'; }
}
$coupons = db()->fetchAll("SELECT * FROM coupons ORDER BY created_at DESC");
$adminUser = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Manage Coupons - NSS Skin & Beauty Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        body { background: #f8fafc; }
        .coupon-code-pill { background: #fdf2f8; color: var(--pink); border: 1px dashed var(--pink); padding: 4px 12px; border-radius: 8px; font-family: 'Courier New', Courier, monospace; font-weight: 700; letter-spacing: 1px; }
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
                            <h3>Active Coupons</h3>
                            <p style="color:var(--gray-500);font-size:0.85rem">Control discounts and promotional offers</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Value</th>
                                    <th>Details</th>
                                    <th>Usage</th>
                                    <th>Expiry</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coupons as $c): ?>
                                    <tr>
                                        <td><span class="coupon-code-pill"><?= $c['code'] ?></span></td>
                                        <td>
                                            <div style="font-weight:700;color:var(--dark)">
                                                <?= $c['type'] === 'percent' ? $c['value'].'%' : formatPrice($c['value']) ?>
                                            </div>
                                            <div style="font-size:0.75rem;color:var(--gray-500)"><?= ucfirst($c['type']) ?> Discount</div>
                                        </td>
                                        <td>
                                            <div style="font-size:0.8rem">Min: <?= formatPrice($c['min_order']) ?></div>
                                            <?php if($c['max_discount']): ?><div style="font-size:0.75rem;color:var(--gray-500)">Max: <?= formatPrice($c['max_discount']) ?></div><?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="font-weight:600"><?= $c['used_count'] ?><?= $c['usage_limit'] ? '<span style="color:var(--gray-400)"> / '.$c['usage_limit'].'</span>' : '' ?></div>
                                            <div style="font-size:0.75rem;color:var(--gray-500)">Redemptions</div>
                                        </td>
                                        <td>
                                            <div style="font-size:0.85rem"><?= $c['end_date'] ? date('M d, Y', strtotime($c['end_date'])) : 'Never' ?></div>
                                        </td>
                                        <td><span class="order-status status-<?= $c['status'] ?>"><?= ucfirst($c['status']) ?></span></td>
                                        <td>
                                            <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete coupon?')" title="Delete"><i class="fas fa-trash"></i></a>
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
                        <h3>Create Coupon</h3>
                    </div>
                    <form method="POST" class="mt-16">
                        <div class="form-group">
                            <label>Coupon Code *</label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. WELCOME30" style="text-transform:uppercase" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Type *</label>
                                <select name="type" class="form-control">
                                    <option value="percent">Percentage (%)</option>
                                    <option value="flat">Fixed (Rs.)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Value *</label>
                                <input type="number" name="value" class="form-control" placeholder="20" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Minimum Purchase (Rs.)</label>
                            <input type="number" name="min_order" class="form-control" value="0" step="0.01">
                        </div>

                        <div class="form-group">
                            <label>Max Discount (Optional)</label>
                            <input type="number" name="max_discount" class="form-control" step="0.01">
                        </div>

                        <div class="form-group">
                            <label>End Date</label>
                            <input type="date" name="end_date" class="form-control">
                        </div>

                        <div style="margin-top:20px">
                            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><i class="fas fa-plus"></i> Create Coupon</button>
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
