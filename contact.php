<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
$msg = '';
if ($_POST) {
    $name = sanitize($_POST['name']??'');
    $email = sanitize($_POST['email']??'');
    $subject = sanitize($_POST['subject']??'');
    $message = sanitize($_POST['message']??'');
    if ($name && $email && $message) $msg = 'success';
    else $msg = 'error';
}
$pageTitle = 'Contact Us - NS Beauty';
include 'includes/header.php';
?>
<div class="page-banner"><div class="container"><h1>Contact Us</h1></div></div>
<section class="section"><div class="container" style="max-width:960px">
<div class="contact-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start">
  <div>
    <h2 style="font-family:'Playfair Display',serif;margin-bottom:16px;font-size:1.8rem">Get in Touch</h2>
    <p style="color:var(--gray-600);margin-bottom:32px;line-height:1.6">Have a question? We'd love to hear from you. Send us a message and we'll respond within 24 hours.</p>
    <?php foreach([['fas fa-map-marker-alt','Address','Gulshan-e-Iqbal, Karachi, Pakistan'],['fas fa-phone','Phone','+92-300-1234567'],['fas fa-envelope','Email','info@nsbeauty.com'],['fas fa-clock','Working Hours','Mon–Sat: 9AM – 7PM']] as $info): ?>
    <div style="display:flex;align-items:flex-start;gap:16px;margin-bottom:24px">
      <div style="width:44px;height:44px;background:var(--pink-light);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--pink);flex-shrink:0"><i class="<?= $info[0] ?>"></i></div>
      <div><div style="font-weight:600;margin-bottom:4px;font-size:0.95rem"><?= $info[1] ?></div><div style="color:var(--gray-600);font-size:0.88rem;line-height:1.4"><?= $info[2] ?></div></div>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="form-card" style="padding-top: 40px;">
    <?php if ($msg==='success'): ?><div style="background:#d4edda;color:#155724;padding:12px;border-radius:8px;margin-bottom:16px"><i class="fas fa-check-circle"></i> Message sent! We'll get back to you soon.</div><?php endif; ?>
    <?php if ($msg==='error'): ?><div style="background:#fde8e8;color:#c0392b;padding:12px;border-radius:8px;margin-bottom:16px">Please fill all required fields.</div><?php endif; ?>
    <form method="POST">
      <div class="form-row"><div class="form-group"><label>Name *</label><input type="text" name="name" class="form-control" required></div><div class="form-group"><label>Email *</label><input type="email" name="email" class="form-control" required></div></div>
      <div class="form-group"><label>Subject</label><input type="text" name="subject" class="form-control"></div>
      <div class="form-group"><label>Message *</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center"><i class="fas fa-paper-plane"></i> Send Message</button>
    </form>
  </div>
</div>
</div></section>
<?php include 'includes/footer.php'; ?>
