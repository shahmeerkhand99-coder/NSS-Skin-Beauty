<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
if (isLoggedIn()) redirect(SITE_URL . '/user/dashboard.php');
$error = '';
if ($_POST) {
    $name = sanitize($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (!$name || !$email || !$password) { $error = 'Please fill all required fields.'; }
    elseif ($password !== $confirm) { $error = 'Passwords do not match.'; }
    elseif (strlen($password) < 6) { $error = 'Password must be at least 6 characters.'; }
    elseif (db()->fetchOne("SELECT id FROM users WHERE email=?",[$email])) { $error = 'Email already registered.'; }
    else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $id = db()->insert("INSERT INTO users(name,email,phone,password,role,status,email_verified) VALUES(?,?,?,?,'customer','active',1)",[$name,$email,$phone,$hash]);
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'customer';
        redirect(SITE_URL . '/user/dashboard.php');
    }
}
$pageTitle = 'Register - NS Beauty';
include 'includes/header.php';
?>
<div class="auth-container">
  <div class="auth-box" style="max-width:520px">
    <div class="logo" style="justify-content:center;margin-bottom:20px;display:flex"><span class="logo-ns">NS</span><span class="logo-beauty">Beauty</span></div>
    <h1>Join NS Beauty</h1>
    <p class="sub">Create your account and start shopping</p>
    <?php if ($error): ?><div style="background:#fde8e8;color:#c0392b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.88rem"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" placeholder="Sara Khan" required></div>
      <div class="form-row">
        <div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" placeholder="your@email.com" required></div>
        <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" placeholder="+92-300-..."></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Password *</label><input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required></div>
        <div class="form-group"><label>Confirm Password *</label><input type="password" name="confirm_password" class="form-control" placeholder="Repeat password" required></div>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px"><i class="fas fa-user-plus"></i> Create Account</button>
    </form>
    <div class="auth-divider">or</div>
    <p style="text-align:center;font-size:0.88rem">Already have an account? <a href="login.php" style="color:var(--pink);font-weight:600">Sign in</a></p>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
