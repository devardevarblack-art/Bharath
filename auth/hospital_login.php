<?php
/**
 * Hospital Login Page
 */

require_once '../config/constants.php';
require_once '../config/db_config.php';
require_once '../config/session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $stmt = $conn->prepare("SELECT * FROM hospitals WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $hospital = $result->fetch_assoc();
            if ($hospital['status'] != 'approved') {
                $error = 'Your account is pending admin approval';
            } else if (password_verify($password, $hospital['password'])) {
                $_SESSION['user_id'] = $hospital['hospital_id'];
                $_SESSION['user_role'] = 'hospital';
                $_SESSION['user_name'] = $hospital['hospital_name'];
                header('Location: ' . BASE_URL . 'pages/hospital/dashboard.php');
                exit();
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'Hospital not found';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Hospital Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        Don't have an account? <a href="hospital_register.php">Register here</a> | <a href="<?php echo BASE_URL; ?>index.php">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>