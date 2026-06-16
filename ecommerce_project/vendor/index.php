<?php
require_once 'includes/db.php';

if (isVendorLoggedIn()) {
    header("Location: pages/dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $sql    = "SELECT * FROM vendors WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $vendor = mysqli_fetch_assoc($result);
        if (password_verify($password, $vendor['password'])) {
            $_SESSION['vendor_id']       = $vendor['id'];
            $_SESSION['vendor_name']     = $vendor['name'];
            $_SESSION['vendor_email']    = $vendor['email'];
            $_SESSION['vendor_business'] = $vendor['business_name'];
            $_SESSION['vendor_status']   = $vendor['status'];
            header("Location: pages/dashboard.php");
            exit();
        }
    }
    $error = 'Invalid email or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Login | MultiVendor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/vendor.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo"><i class="fas fa-store-alt me-2"></i>MultiVendor</div>
        <p class="auth-subtitle">Vendor Login Portal</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 text-center small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="vendor@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control border-start-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-light border-start-0" style="cursor:pointer" onclick="togglePassword()"><i class="fas fa-eye text-muted" id="eyeIcon"></i></span>
                </div>
            </div>
            <button type="submit" class="btn btn-vendor">
                <i class="fas fa-sign-in-alt me-2"></i>Login to Vendor Panel
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            New vendor? <a href="register.php" class="text-success fw-semibold">Register here</a>
        </p>
        <p class="text-center text-muted small mt-1 mb-0">© <?php echo date('Y'); ?> MultiVendor E-Commerce</p>
    </div>
</div>
<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
