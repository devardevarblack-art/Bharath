<?php
include '../includes/db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hospital_name = $_POST['hospital_name'];
    $reg_no = $_POST['reg_no'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM hospitals WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO hospitals (hospital_name, reg_no, email, phone, password, status) VALUES (?,?,?,?,?,'pending')");
        $stmt->bind_param("sssss", $hospital_name, $reg_no, $email, $phone, $password);
        if ($stmt->execute()) {
            $success = "Registration successful! Please wait for Admin approval before logging in.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hospital Registration - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-dark bg-danger">
  <div class="container"><a class="navbar-brand fw-bold" href="../index.php">🫀 Organ Donate</a></div>
</nav>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="mb-3 text-center">Hospital Registration</h4>

          <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
          <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <p class="text-center"><a href="../index.php" class="btn btn-danger">Go to Login</a></p>
          <?php else: ?>

          <form method="POST">
            <div class="mb-3">
              <label>Hospital Name</label>
              <input type="text" name="hospital_name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Registration Number</label>
              <input type="text" name="reg_no" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Phone</label>
              <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-danger w-100">Register</button>
            <p class="text-center mt-3">Already registered? <a href="../index.php">Login here</a></p>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
