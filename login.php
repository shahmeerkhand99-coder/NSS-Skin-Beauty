<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
if (isLoggedIn()) redirect(SITE_URL . '/user/dashboard.php');
$error = '';
if ($_POST) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = db()->fetchOne("SELECT * FROM users WHERE email=? AND status='active'", [$email]);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $redirect = $_SESSION['redirect_after_login'] ?? ($user['role']==='admin' ? SITE_URL.'/admin/' : SITE_URL.'/user/dashboard.php');
        unset($_SESSION['redirect_after_login']);
        redirect($redirect);
    } else { $error = 'Invalid email or password.'; }
}
$pageTitle = 'Login - NS Beauty';
include 'includes/header.php';
?>
<div class="auth-container">
  <div class="auth-box">
    <div class="logo" style="justify-content:center;margin-bottom:20px;display:flex">
      <span class="logo-ns">NS</span><span class="logo-beauty">Beauty</span>
    </div>
    <h1>Welcome Back</h1>
    <p class="sub">Sign in to your NS Beauty account</p>
    <?php if ($error): ?><div style="background:#fde8e8;color:#c0392b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.88rem"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Email Address</label><input type="email" name="email" class="form-control" placeholder="your@email.com" required autofocus></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" placeholder="••••••••" required></div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px">Sign In</button>
    </form>
    <div class="auth-divider">or</div>
    <p style="text-align:center;font-size:0.88rem">Don't have an account? <a href="register.php" style="color:var(--pink);font-weight:600">Create one</a></p>
    <p style="text-align:center;font-size:0.78rem;color:var(--gray-600);margin-top:16px">
      <strong>Demo Admin:</strong> admin@nsbeauty.com / password<br>
      <strong>Demo User:</strong> sara@example.com / password
    </p>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
