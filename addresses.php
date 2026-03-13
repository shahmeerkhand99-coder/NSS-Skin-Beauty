<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
requireLogin();
$user = getLoggedUser();
$msg = '';

// Handle add/edit address
if ($_POST) {
    $fullName = sanitize($_POST['full_name'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');
    $addr1    = sanitize($_POST['address_line1'] ?? '');
    $addr2    = sanitize($_POST['address_line2'] ?? '');
    $city     = sanitize($_POST['city'] ?? '');
    $state    = sanitize($_POST['state'] ?? '');
    $zip      = sanitize($_POST['zip'] ?? '');
    $country  = sanitize($_POST['country'] ?? 'Pakistan');
    $isDefault = isset($_POST['is_default']) ? 1 : 0;
    $editId   = (int)($_POST['edit_id'] ?? 0);

    if (!$fullName || !$phone || !$addr1 || !$city) {
        $msg = ['type' => 'error', 'text' => 'Please fill all required fields.'];
    } else {
        if ($isDefault) {
            db()->execute("UPDATE addresses SET is_default=0 WHERE user_id=?", [$_SESSION['user_id']]);
        }
        if ($editId) {
            db()->execute(
                "UPDATE addresses SET full_name=?,phone=?,address_line1=?,address_line2=?,city=?,state=?,zip=?,country=?,is_default=? WHERE id=? AND user_id=?",
                [$fullName, $phone, $addr1, $addr2, $city, $state, $zip, $country, $isDefault, $editId, $_SESSION['user_id']]
            );
            $msg = ['type' => 'success', 'text' => 'Address updated successfully!'];
        } else {
            db()->insert(
                "INSERT INTO addresses(user_id,full_name,phone,address_line1,address_line2,city,state,zip,country,is_default) VALUES(?,?,?,?,?,?,?,?,?,?)",
                [$_SESSION['user_id'], $fullName, $phone, $addr1, $addr2, $city, $state, $zip, $country, $isDefault]
            );
            $msg = ['type' => 'success', 'text' => 'Address added successfully!'];
        }
    }
}

// Delete address
if (isset($_GET['delete'])) {
    db()->execute("DELETE FROM addresses WHERE id=? AND user_id=?", [(int)$_GET['delete'], $_SESSION['user_id']]);
    $msg = ['type' => 'success', 'text' => 'Address deleted.'];
}

// Set default
if (isset($_GET['default'])) {
    db()->execute("UPDATE addresses SET is_default=0 WHERE user_id=?", [$_SESSION['user_id']]);
    db()->execute("UPDATE addresses SET is_default=1 WHERE id=? AND user_id=?", [(int)$_GET['default'], $_SESSION['user_id']]);
    $msg = ['type' => 'success', 'text' => 'Default address updated.'];
}

$editId      = (int)($_GET['edit'] ?? 0);
$editAddress = $editId ? db()->fetchOne("SELECT * FROM addresses WHERE id=? AND user_id=?", [$editId, $_SESSION['user_id']]) : null;
$addresses   = db()->fetchAll("SELECT * FROM addresses WHERE user_id=? ORDER BY is_default DESC, id DESC", [$_SESSION['user_id']]);
$pageTitle   = 'My Addresses - NSS Skin & Beauty';
include '../includes/header.php';
?>

<div class="page-banner">
  <div class="container">
    <h1>My Addresses</h1>
    <nav class="breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a>
      <i class="fas fa-chevron-right" style="font-size:0.7rem"></i>
      <a href="dashboard.php">Dashboard</a>
      <i class="fas fa-chevron-right" style="font-size:0.7rem"></i>
      <span>Addresses</span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="dashboard-layout">
      <?php include 'sidebar.php'; ?>
      <div>

        <?php if ($msg): ?>
        <div style="background:<?= $msg['type']==='success'?'#d4edda':'#fde8e8' ?>;color:<?= $msg['type']==='success'?'#155724':'#721c24' ?>;padding:12px 18px;border-radius:10px;margin-bottom:20px;display:flex;align-items:center;gap:10px">
          <i class="fas fa-<?= $msg['type']==='success'?'check':'exclamation' ?>-circle"></i>
          <?= $msg['text'] ?>
        </div>
        <?php endif; ?>

        <div class="address-grid" style="display:grid;grid-template-columns:1fr <?= ($editAddress || empty($addresses)) ? '380px' : '' ?>;gap:24px;align-items:start">

          <!-- Address List -->
          <div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
              <h3 style="font-family:'Playfair Display',serif;font-size:1.3rem">Saved Addresses</h3>
              <a href="addresses.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New</a>
            </div>

            <?php if ($addresses): ?>
            <div style="display:flex;flex-direction:column;gap:16px">
              <?php foreach ($addresses as $addr): ?>
              <div style="background:#fff;border:2px solid <?= $addr['is_default']?'var(--pink)':'var(--gray-200)' ?>;border-radius:14px;padding:20px;position:relative;transition:border-color 0.3s">
                <?php if ($addr['is_default']): ?>
                <span style="position:absolute;top:14px;right:14px;background:var(--pink);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:1px">Default</span>
                <?php endif; ?>

                <div style="display:flex;align-items:flex-start;gap:14px">
                  <div style="width:44px;height:44px;background:var(--pink-light);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--pink);flex-shrink:0">
                    <i class="fas fa-home"></i>
                  </div>
                  <div style="flex:1">
                    <div style="font-weight:700;font-size:0.95rem;margin-bottom:2px"><?= sanitize($addr['full_name']) ?></div>
                    <div style="font-size:0.85rem;color:var(--gray-600);margin-bottom:6px"><?= sanitize($addr['phone']) ?></div>
                    <div style="font-size:0.88rem;color:var(--gray-700);line-height:1.6">
                      <?= sanitize($addr['address_line1']) ?>
                      <?php if ($addr['address_line2']): ?>, <?= sanitize($addr['address_line2']) ?><?php endif; ?><br>
                      <?= sanitize($addr['city']) ?><?= $addr['state'] ? ', '.sanitize($addr['state']) : '' ?>
                      <?php if ($addr['zip']): ?> - <?= sanitize($addr['zip']) ?><?php endif; ?><br>
                      <?= sanitize($addr['country']) ?>
                    </div>
                    <div style="display:flex;gap:8px;margin-top:14px;flex-wrap:wrap">
                      <a href="?edit=<?= $addr['id'] ?>" class="btn btn-edit btn-sm"><i class="fas fa-pen"></i> Edit</a>
                      <?php if (!$addr['is_default']): ?>
                      <a href="?default=<?= $addr['id'] ?>" class="btn btn-sm" style="background:var(--pink-light);color:var(--pink);border:1px solid rgba(233,30,140,0.2)">
                        <i class="fas fa-check"></i> Set Default
                      </a>
                      <?php endif; ?>
                      <a href="?delete=<?= $addr['id'] ?>" class="btn btn-delete btn-sm" onclick="return confirm('Delete this address?')"><i class="fas fa-trash"></i> Delete</a>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>

            <?php else: ?>
            <div style="text-align:center;padding:60px 20px;background:#fff;border-radius:14px;border:2px dashed var(--gray-200)">
              <i class="fas fa-map-marker-alt" style="font-size:3rem;color:var(--gray-400);display:block;margin-bottom:16px"></i>
              <h4 style="margin-bottom:8px;color:var(--dark)">No Saved Addresses</h4>
              <p style="color:var(--gray-600);font-size:0.88rem">Add your first delivery address below</p>
            </div>
            <?php endif; ?>
          </div>

          <!-- Add / Edit Form -->
          <div class="form-card address-form-card" style="position:sticky;top:100px">
            <h3 style="font-family:'Playfair Display',serif;font-size:1.2rem;margin-bottom:20px">
              <i class="fas fa-<?= $editAddress ? 'pen' : 'plus' ?>" style="color:var(--pink)"></i>
              <?= $editAddress ? 'Edit Address' : 'Add New Address' ?>
            </h3>
            <form method="POST">
              <?php if ($editAddress): ?>
              <input type="hidden" name="edit_id" value="<?= $editAddress['id'] ?>">
              <?php endif; ?>

              <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" class="form-control" value="<?= sanitize($editAddress['full_name'] ?? '') ?>" placeholder="Recipient's full name" required>
              </div>
              <div class="form-group">
                <label>Phone Number *</label>
                <input type="text" name="phone" class="form-control" value="<?= sanitize($editAddress['phone'] ?? '') ?>" placeholder="+92-300-..." required>
              </div>
              <div class="form-group">
                <label>Address Line 1 *</label>
                <input type="text" name="address_line1" class="form-control" value="<?= sanitize($editAddress['address_line1'] ?? '') ?>" placeholder="Street, House/Flat number" required>
              </div>
              <div class="form-group">
                <label>Address Line 2</label>
                <input type="text" name="address_line2" class="form-control" value="<?= sanitize($editAddress['address_line2'] ?? '') ?>" placeholder="Apartment, Area, Landmark (optional)">
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>City *</label>
                  <input type="text" name="city" class="form-control" value="<?= sanitize($editAddress['city'] ?? '') ?>" placeholder="e.g. Karachi" required>
                </div>
                <div class="form-group">
                  <label>State / Province</label>
                  <input type="text" name="state" class="form-control" value="<?= sanitize($editAddress['state'] ?? '') ?>" placeholder="e.g. Sindh">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>ZIP / Postal Code</label>
                  <input type="text" name="zip" class="form-control" value="<?= sanitize($editAddress['zip'] ?? '') ?>" placeholder="75500">
                </div>
                <div class="form-group">
                  <label>Country</label>
                  <input type="text" name="country" class="form-control" value="<?= sanitize($editAddress['country'] ?? 'Pakistan') ?>">
                </div>
              </div>
              <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:20px;font-size:0.9rem;font-weight:500">
                <input type="checkbox" name="is_default" <?= ($editAddress['is_default'] ?? 0) ? 'checked' : '' ?> style="accent-color:var(--pink);width:16px;height:16px">
                Set as default delivery address
              </label>
              <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                <i class="fas fa-save"></i>
                <?= $editAddress ? 'Update Address' : 'Save Address' ?>
              </button>
              <?php if ($editAddress): ?>
              <a href="addresses.php" class="btn btn-outline" style="width:100%;justify-content:center;margin-top:10px">
                <i class="fas fa-times"></i> Cancel Edit
              </a>
              <?php endif; ?>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
