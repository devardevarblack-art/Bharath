<?php
require_once 'includes/db.php';

if (isset($_GET['redirect'])) {
    $allowedRedirects = ['pages/checkout.php', 'pages/home.php'];
    $requestedRedirect = trim($_GET['redirect']);
    if (in_array($requestedRedirect, $allowedRedirects, true)) {
        $_SESSION['redirect_after_login'] = $requestedRedirect;
    }
}

if (isCustomerLoggedIn()) {
    header("Location: pages/home.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $sql    = "SELECT * FROM customers WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
        if ($customer['status'] !== 'active') {
            $error = 'Your account has been deactivated. Please contact support.';
        } elseif (password_verify($password, $customer['password'])) {
            $_SESSION['customer_id']    = $customer['id'];
            $_SESSION['customer_name']  = $customer['name'];
            $_SESSION['customer_email'] = $customer['email'];

            $redirect = $_SESSION['redirect_after_login'] ?? 'pages/home.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit();
        } else {
            $error = 'Invalid email or password.';
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login | MultiVendor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/customer.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo"><i class="fas fa-store me-2"></i>MultiVendor</div>
        <p class="auth-subtitle">Login to your shopping account</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 text-center small"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="you@example.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" id="passwordInput" class="form-control border-start-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-light border-start-0" style="cursor:pointer" onclick="togglePassword('passwordInput','eyeIcon')"><i class="fas fa-eye text-muted" id="eyeIcon"></i></span>
                </div>
            </div>
            <button type="submit" class="btn btn-customer">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>

        <p class="text-center text-muted small mt-4 mb-0">
            New here? <a href="register.php" class="text-primary fw-semibold">Create an account</a>
        </p>
        <p class="text-center text-muted small mt-1 mb-0">
            <a href="../vendor/index.php" class="text-muted">Vendor Login</a> ·
            <a href="../admin/index.php" class="text-muted">Admin Login</a>
        </p>
        <p class="text-center text-muted small mt-2 mb-0">© <?php echo date('Y'); ?> MultiVendor E-Commerce</p>
    </div>
</div>
<script src="js/customer.js"></script>
</body>
</html>
