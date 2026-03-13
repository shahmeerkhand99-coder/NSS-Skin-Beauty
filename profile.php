<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireLogin();
$user = getLoggedUser();
$success = '';
$error = '';
if ($_POST) {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $newPw = $_POST['new_password'] ?? '';
    $curPw = $_POST['current_password'] ?? '';
    if (!$name) { $error = 'Name is required.'; }
    else {
        db()->execute("UPDATE users SET name=?, phone=? WHERE id=?",[$name,$phone,$_SESSION['user_id']]);
        if ($newPw) {
            if (!password_verify($curPw, $user['password'])) { $error = 'Current password is incorrect.'; }
            elseif (strlen($newPw)<6) { $error = 'New password must be at least 6 characters.'; }
            else { db()->execute("UPDATE users SET password=? WHERE id=?",[password_hash($newPw,PASSWORD_DEFAULT),$_SESSION['user_id']]); }
        }
        if (!$error) { $_SESSION['user_name']=$name; $success='Profile updated successfully!'; $user=getLoggedUser(); }
    }
}
$pageTitle = 'My Profile - NSS Skin & Beauty';
include '../includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>My Profile</h1></div></div>
<section class="section"><div class="container"><div class="dashboard-layout">
  <?php include 'sidebar.php'; ?>
  <div>
    <?php if ($success): ?><div style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:16px"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div style="background:#fde8e8;color:#c0392b;padding:12px 16px;border-radius:8px;margin-bottom:16px"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
    <div class="form-card">
      <h3 style="font-family:'Playfair Display',serif;margin-bottom:24px">Personal Information</h3>
      <form method="POST">
        <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" value="<?= sanitize($user['name']) ?>" required></div>
        <div class="form-group"><label>Email Address</label><input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" disabled></div>
        <div class="form-group"><label>Phone Number</label><input type="text" name="phone" class="form-control" value="<?= sanitize($user['phone']??'') ?>"></div>
        <hr style="margin:24px 0;border-color:var(--gray-200)">
        <h4 style="margin-bottom:16px">Change Password</h4>
        <div class="form-group"><label>Current Password</label><input type="password" name="current_password" class="form-control" placeholder="Leave blank to keep current"></div>
        <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control" placeholder="Min. 6 characters"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      </form>
    </div>
  </div>
</div></div></section>
<?php include '../includes/footer.php'; ?>
