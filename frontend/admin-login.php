<?php
include '../backend/config.php';
include '../backend/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];
    
    // Validate input
    if (empty($username) || empty($password)) {
        set_error("Username and Password are required!");
    } else {
        // Query database
        $query = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (verify_password($password, $admin['password'])) {
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['username'] = $admin['username'];
                set_success("Admin Login Successful!");
                header("Location: admin-dashboard.php");
                exit();
            } else {
                set_error("Invalid Password!");
            }
        } else {
            set_error("Admin not found!");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Organ Donate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white text-center">
                        <h3>Admin Login</h3>
                    </div>
                    <div class="card-body">
                        <?php display_message(); ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p><a href="index.php" class="text-decoration-none">Back to Home</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
