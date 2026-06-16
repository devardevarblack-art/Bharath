<?php
require_once 'includes/db.php';

if (isCustomerLoggedIn()) {
    header("Location: pages/home.php");
    exit();
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone    = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $address  = mysqli_real_escape_string($conn, trim($_POST['address']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $check = mysqli_query($conn, "SELECT id FROM customers WHERE email='$email' LIMIT 1");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Email already registered. Please login instead.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO customers (name, email, phone, password, address, status)
                    VALUES ('$name','$email','$phone','$hashed','$address','active')";
            if (mysqli_query($conn, $sql)) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | MultiVendor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/customer.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card" style="width:520px;">
        <div class="auth-logo"><i class="fas fa-store me-2"></i>MultiVendor</div>
        <p class="auth-subtitle">Create your shopping account</p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 text-center small"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success py-2 text-center small"><?php echo $success; ?></div>
            <p class="text-center mt-2"><a href="index.php" class="text-primary fw-semibold">Go to Login</a></p>
        <?php else: ?>

        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="name" class="form-control border-start-0" placeholder="Your Name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                        <input type="email" name="email" class="form-control border-start-0" placeholder="you@example.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-muted"></i></span>
                        <input type="text" name="phone" class="form-control border-start-0" placeholder="9XXXXXXXXX" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="password" id="pass1" class="form-control border-start-0" placeholder="Min 6 characters" required>
                        <span class="input-group-text bg-light border-start-0" style="cursor:pointer" onclick="togglePassword('pass1','eye1')"><i class="fas fa-eye text-muted" id="eye1"></i></span>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Delivery Address</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="House no, street, city, state, pincode..."><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                        <input type="password" name="confirm_password" id="pass2" class="form-control border-start-0" placeholder="Repeat password" required>
                        <span class="input-group-text bg-light border-start-0" style="cursor:pointer" onclick="togglePassword('pass2','eye2')"><i class="fas fa-eye text-muted" id="eye2"></i></span>
                    </div>
                </div>
            </div>
            <button type="submit" name="register" class="btn btn-customer mt-4">
                <i class="fas fa-user-plus me-2"></i>Create Account
            </button>
        </form>

        <?php endif; ?>

        <p class="text-center text-muted small mt-3 mb-0">
            Already have an account? <a href="index.php" class="text-primary fw-semibold">Login here</a>
        </p>
    </div>
</div>
<script src="js/customer.js"></script>
</body>
</html>
