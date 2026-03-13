<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireAdmin();
$msg = '';
if (isset($_GET['toggle'])) {
    $u = db()->fetchOne("SELECT status FROM users WHERE id=?",[(int)$_GET['toggle']]);
    $newStatus = $u['status']==='active' ? 'inactive' : 'active';
    db()->execute("UPDATE users SET status=? WHERE id=?",[$newStatus,(int)$_GET['toggle']]);
    $msg='User status updated.';
}
$q = sanitize($_GET['q'] ?? '');
$sql = "SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users u WHERE u.role = 'customer'";
$params = [];
if ($q) {
    $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
$sql .= " ORDER BY u.created_at DESC";
$users = db()->fetchAll($sql, $params);
$adminUser = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Users Admin - NS Beauty</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
<style>body{overflow-x:hidden;background:#f4f6fb;}</style>
</head><body>
<div class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<div class="admin-main">
<?php include 'includes/topbar.php'; ?>
<div class="admin-content">
<?php if ($msg): ?><div style="background:#d4edda;color:#155724;padding:12px 18px;border-radius:10px;margin-bottom:16px"><?= $msg ?></div><?php endif; ?>

<div class="admin-card" style="margin-bottom:24px; padding:20px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.05)">
  <form method="GET" style="display:flex; gap:12px; align-items:center">
    <div style="position:relative; flex:1">
      <i class="fas fa-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--gray-400); font-size:1rem"></i>
      <input type="text" name="q" class="form-control" value="<?= sanitize($q) ?>" placeholder="Search customers by name, email, or phone..." style="padding:14px 14px 14px 45px; border-radius:12px; border:1px solid var(--gray-200); font-size:0.95rem">
    </div>
    <button type="submit" class="btn btn-primary" style="padding:14px 28px; border-radius:12px">Search</button>
    <?php if ($q): ?>
    <a href="users.php" class="btn btn-outline" style="padding:14px 24px; border-radius:12px; border-color:var(--gray-400); color:var(--gray-600)">Clear</a>
    <?php endif; ?>
  </form>
</div>

<div class="admin-card" style="border:none; box-shadow:0 10px 30px rgba(0,0,0,0.04); padding:0; overflow:hidden">
  <div class="admin-card-header" style="padding:24px 28px; border-bottom:1px solid var(--gray-100); margin-bottom:0">
    <h3 style="font-size:1.2rem; font-weight:700; color:var(--dark)">Customers <span style="font-size:0.9rem; font-weight:400; color:var(--gray-600); margin-left:8px">(<?= count($users) ?> total)</span></h3>
    <?php if ($q): ?><span style="padding:6px 14px; background:var(--pink-light); color:var(--pink); border-radius:20px; font-size:0.8rem; font-weight:600">Results for "<?= sanitize($q) ?>"</span><?php endif; ?>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table" style="width:100%; border-spacing:0">
      <thead>
        <tr style="background:var(--gray-100)">
          <th style="padding:16px 28px; border:none">Customer</th>
          <th style="padding:16px; border:none">Contact Info</th>
          <th style="padding:16px; border:none">Activity</th>
          <th style="padding:16px; border:none">Joined Date</th>
          <th style="padding:16px; border:none">Status</th>
          <th style="padding:16px 28px; border:none; text-align:right">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($users as $u):
          $initials = strtoupper(substr($u['name'], 0, 1));
          $bgColors = ['#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3', '#FF9800', '#F44336'];
          $bgColor = $bgColors[ord($initials) % count($bgColors)];
      ?>
      <tr style="transition:all 0.2s">
        <td style="padding:18px 28px">
          <div style="display:flex; align-items:center; gap:14px">
            <div style="width:44px; height:44px; border-radius:12px; background:<?= $bgColor ?>; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.1rem; box-shadow:0 4px 10px rgba(0,0,0,0.1)">
              <?= $initials ?>
            </div>
            <div>
              <div style="font-weight:700; font-size:0.95rem; color:var(--dark)"><?= sanitize($u['name']) ?></div>
              <div style="font-size:0.75rem; color:var(--gray-600); text-transform:uppercase; letter-spacing:1px; font-weight:600"><?= ucfirst($u['role']) ?></div>
            </div>
          </div>
        </td>
        <td style="padding:18px 16px">
          <div style="display:flex; flex-direction:column; gap:4px">
            <div style="font-size:0.9rem; color:var(--gray-800)"><i class="far fa-envelope" style="width:16px; color:var(--pink)"></i> <?= sanitize($u['email']) ?></div>
            <div style="font-size:0.85rem; color:var(--gray-600)"><i class="fas fa-phone-alt" style="width:16px; color:var(--gray-400)"></i> <?= sanitize($u['phone'] ?? '-') ?></div>
          </div>
        </td>
        <td style="padding:18px 16px">
          <a href="orders.php?user_id=<?= $u['id'] ?>" style="display:inline-flex; align-items:center; gap:8px; padding:6px 12px; background:var(--pink-light); color:var(--pink); border-radius:8px; font-weight:700; font-size:0.85rem; transition:all 0.2s" class="user-order-link">
            <i class="fas fa-shopping-bag"></i> <?= $u['order_count'] ?> Orders
          </a>
        </td>
        <td style="padding:18px 16px; color:var(--gray-600); font-size:0.88rem">
          <i class="far fa-calendar-alt" style="margin-right:6px"></i> <?= date('M d, Y', strtotime($u['created_at'])) ?>
        </td>
        <td style="padding:18px 16px">
          <span style="display:inline-flex; align-items:center; gap:6px; padding:5px 12px; border-radius:30px; font-size:0.75rem; font-weight:700; background:<?= $u['status']==='active'?'#e1f7e9':'#ffebeb' ?>; color:<?= $u['status']==='active'?'#1db954':'#f22c3d' ?>">
            <span style="width:6px; height:6px; border-radius:50%; background:currentColor"></span>
            <?= strtoupper($u['status']) ?>
          </span>
        </td>
        <td style="padding:18px 28px; text-align:right">
          <div style="display:flex; gap:8px; justify-content:flex-end">
            <a href="orders.php?user_id=<?= $u['id'] ?>" class="btn btn-sm btn-view" title="View Order History" style="padding:8px 12px; border-radius:8px; border:none; background:var(--gray-100); color:var(--gray-800)">
              <i class="fas fa-eye"></i>
            </a>
            <a href="?toggle=<?= $u['id'] ?>" class="btn btn-sm <?= $u['status']==='active'?'btn-delete':'btn-edit' ?>" title="<?= $u['status']==='active'?'Deactivate':'Activate' ?> User" style="padding:8px 12px; border-radius:8px; border:none; <?= $u['status']==='active'?'background:#ffebeb; color:#f22c3d':'background:#e1f7e9; color:#1db954' ?>">
              <i class="fas fa-user-<?= $u['status']==='active'?'slash':'check' ?>"></i>
            </a>
          </div>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($users)): ?>
      <tr>
        <td colspan="6" style="padding:100px 0; text-align:center">
          <div style="display:flex; flex-direction:column; align-items:center; gap:16px">
            <div style="width:80px; height:80px; background:var(--gray-100); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--gray-400)">
              <i class="fas fa-users-slash" style="font-size:2rem"></i>
            </div>
            <div style="color:var(--gray-600); font-weight:500">No customers found matching your search.</div>
            <a href="users.php" class="btn btn-outline" style="font-size:0.85rem">View All Customers</a>
          </div>
        </td>
      </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<style>
  .user-order-link:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(233,30,140,0.15); }
  .data-table tr:hover { background: rgba(248,215,234,0.05); }
</style>
</div></div></div>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body></html>
