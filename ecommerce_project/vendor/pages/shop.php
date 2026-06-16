<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'Shop Profile';
$vendor_id = getVendorId();

$msg   = '';
$error = '';

// Fetch vendor
$vendor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM vendors WHERE id=$vendor_id LIMIT 1"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $phone    = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $biz_name = mysqli_real_escape_string($conn, trim($_POST['business_name']));
    $biz_addr = mysqli_real_escape_string($conn, trim($_POST['business_address']));

    mysqli_query($conn, "UPDATE vendors SET name='$name', phone='$phone', business_name='$biz_name', business_address='$biz_addr' WHERE id=$vendor_id");
    $_SESSION['vendor_name']     = $name;
    $_SESSION['vendor_business'] = $biz_name;

    // Password change
    if (!empty($_POST['new_password'])) {
        $cur = $_POST['current_password'];
        $new = $_POST['new_password'];
        if (password_verify($cur, $vendor['password'])) {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE vendors SET password='$hashed' WHERE id=$vendor_id");
            $msg = 'Profile and password updated successfully!';
        } else {
            $error = 'Current password is incorrect.';
        }
    } else {
        $msg = 'Profile updated successfully!';
    }

    $vendor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM vendors WHERE id=$vendor_id LIMIT 1"));
}

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="page-title">Shop Profile</h4>
        <p class="page-subtitle">Manage your shop information</p>
    </div>
</div>

<?php if ($msg): ?><div class="alert alert-success py-2 small"><?php echo $msg; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger py-2 small"><?php echo $error; ?></div><?php endif; ?>

<div class="row g-3">
    <div class="col-lg-4">
        <!-- Shop Card -->
        <div class="card mb-3">
            <div class="shop-banner"></div>
            <div class="shop-info">
                <div class="shop-logo-wrap"><i class="fas fa-store"></i></div>
                <h5 class="fw-bold mt-1"><?php echo htmlspecialchars($vendor['business_name']); ?></h5>
                <p class="text-muted small mb-1"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($vendor['name']); ?></p>
                <p class="text-muted small mb-1"><i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($vendor['email']); ?></p>
                <p class="text-muted small mb-2"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($vendor['phone'] ?? '-'); ?></p>
                <span class="status-badge badge-<?php echo $vendor['status']; ?>"><?php echo ucfirst($vendor['status']); ?></span>
            </div>
        </div>
        <!-- Account Status -->
        <div class="card">
            <div class="card-header"><h6><i class="fas fa-info-circle me-2 text-success"></i>Account Status</h6></div>
            <div class="card-body">
                <?php if ($vendor['status'] === 'approved'): ?>
                    <div class="alert-info-box"><i class="fas fa-check-circle me-2 text-success"></i>Your account is <strong>Approved</strong>. You can list products and manage orders.</div>
                <?php elseif ($vendor['status'] === 'pending'): ?>
                    <div class="pending-badge"><i class="fas fa-hourglass-half fa-2x text-warning mb-2 d-block"></i><strong>Pending Approval</strong><br><small class="text-muted">Admin will review your account shortly.</small></div>
                <?php else: ?>
                    <div class="alert alert-danger small">Your account has been <strong>rejected</strong>. Contact admin for help.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h6><i class="fas fa-edit me-2 text-success"></i>Edit Profile</h6></div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($vendor['name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email (read-only)</label>
                            <input type="email" class="form-control bg-light" value="<?php echo htmlspecialchars($vendor['email']); ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($vendor['phone'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Business Name</label>
                            <input type="text" name="business_name" class="form-control" value="<?php echo htmlspecialchars($vendor['business_name']); ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Business Address</label>
                            <textarea name="business_address" class="form-control" rows="2"><?php echo htmlspecialchars($vendor['business_address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <hr class="my-3">
                    <p class="fw-semibold small text-muted mb-2"><i class="fas fa-key me-1"></i>Change Password (leave blank to keep current)</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Current password">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control" placeholder="New password">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Confirm New Password</label>
                            <input type="password" name="confirm_new" class="form-control" placeholder="Repeat new password">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success px-4 rounded-pill">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
