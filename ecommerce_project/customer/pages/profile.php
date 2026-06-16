<?php
require_once '../includes/db.php';
redirectIfNotLoggedIn();

$pageTitle = 'My Profile';
$cid = getCustomerId();

$customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id=$cid LIMIT 1"));

$msg   = '';
$error = '';

// Update profile info
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name    = mysqli_real_escape_string($conn, trim($_POST['name']));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));

    if ($name === '') {
        $error = 'Name cannot be empty.';
    } else {
        mysqli_query($conn, "UPDATE customers SET name='$name', phone='$phone', address='$address' WHERE id=$cid");
        $_SESSION['customer_name'] = $name;
        $msg = 'Profile updated successfully!';
        $customer = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM customers WHERE id=$cid LIMIT 1"));
    }
}

// Change password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $newpass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $customer['password'])) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($newpass) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($newpass !== $confirm) {
        $error = 'New passwords do not match.';
    } else {
        $hashed = password_hash($newpass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE customers SET password='$hashed' WHERE id=$cid");
        $msg = 'Password changed successfully!';
    }
}

require_once '../includes/header.php';
?>

<h4 class="page-title mb-4"><i class="fas fa-id-card me-2"></i>My Profile</h4>

<?php if ($msg): ?><div class="alert alert-success small"><?php echo $msg; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger small"><?php echo $error; ?></div><?php endif; ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="profile-card text-center">
            <div class="profile-avatar mx-auto mb-3">
                <?php echo strtoupper(substr($customer['name'], 0, 1)); ?>
            </div>
            <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($customer['name']); ?></h5>
            <p class="text-muted small mb-1"><?php echo htmlspecialchars($customer['email']); ?></p>
            <p class="text-muted small">Member since <?php echo date('M Y', strtotime($customer['created_at'])); ?></p>
        </div>
    </div>

    <div class="col-md-8">
        <div class="profile-card mb-4">
            <h6 class="fw-bold mb-3">Profile Information</h6>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($customer['name']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($customer['email']); ?>" disabled>
                        <div class="form-text">Email cannot be changed.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Delivery Address</label>
                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                    </div>
                </div>
                <button type="submit" name="update_profile" class="btn-store mt-3">
                    <i class="fas fa-save me-2"></i>Save Changes
                </button>
            </form>
        </div>

        <div class="profile-card">
            <h6 class="fw-bold mb-3">Change Password</h6>
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Current Password</label>
                        <div class="input-group">
                            <input type="password" name="current_password" id="cp" class="form-control" required>
                            <span class="input-group-text" style="cursor:pointer" onclick="togglePassword('cp','cpEye')"><i class="fas fa-eye" id="cpEye"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">New Password</label>
                        <div class="input-group">
                            <input type="password" name="new_password" id="np" class="form-control" required minlength="6">
                            <span class="input-group-text" style="cursor:pointer" onclick="togglePassword('np','npEye')"><i class="fas fa-eye" id="npEye"></i></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="ncp" class="form-control" required minlength="6">
                            <span class="input-group-text" style="cursor:pointer" onclick="togglePassword('ncp','ncpEye')"><i class="fas fa-eye" id="ncpEye"></i></span>
                        </div>
                    </div>
                </div>
                <button type="submit" name="change_password" class="btn-store mt-3">
                    <i class="fas fa-key me-2"></i>Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
