<?php
/**
 * Hospital Registration Page
 */

require_once '../config/constants.php';
require_once '../config/db_config.php';
require_once '../config/session.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hospital_name = $_POST['hospital_name'] ?? '';
    $reg_no = $_POST['reg_no'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($hospital_name) || empty($reg_no) || empty($email) || empty($phone) || empty($password)) {
        $error = 'All fields are required';
    } else if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $stmt = $conn->prepare("SELECT hospital_id FROM hospitals WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $status = 'pending';
            $stmt = $conn->prepare("INSERT INTO hospitals (hospital_name, reg_no, email, phone, password, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $hospital_name, $reg_no, $email, $phone, $hashed_password, $status);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! Please wait for admin approval.';
            } else {
                $error = 'Registration failed';
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
    <title>Hospital Registration - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">Hospital Registration</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hospital_name" class="form-label">Hospital Name</label>
                                    <input type="text" class="form-control" id="hospital_name" name="hospital_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="reg_no" class="form-label">Registration Number</label>
                                    <input type="text" class="form-control" id="reg_no" name="reg_no" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-warning w-100">Register</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        Already have an account? <a href="hospital_login.php">Login here</a> | <a href="<?php echo BASE_URL; ?>index.php">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>