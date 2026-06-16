<?php
include '../includes/db.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $blood_group = $_POST['blood_group'];
    $required_organ = $_POST['required_organ'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM patients WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Email already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO patients (name, age, blood_group, required_organ, phone, email, password) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sisssss", $name, $age, $blood_group, $required_organ, $phone, $email, $password);
        if ($stmt->execute()) {
            $success = "Registration successful! You can login now.";
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
<title>Patient Registration - Organ Donate</title>
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
          <h4 class="mb-3 text-center">Patient Registration</h4>

          <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
          <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <p class="text-center"><a href="../index.php" class="btn btn-danger">Go to Login</a></p>
          <?php else: ?>

          <form method="POST">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Age</label>
                <input type="number" name="age" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Blood Group</label>
                <select name="blood_group" class="form-control" required>
                  <option value="">Select</option>
                  <option>A+</option><option>A-</option>
                  <option>B+</option><option>B-</option>
                  <option>O+</option><option>O-</option>
                  <option>AB+</option><option>AB-</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label>Required Organ</label>
                <select name="required_organ" class="form-control" required>
                  <option value="">Select</option>
                  <option>Kidney</option>
                  <option>Liver</option>
                  <option>Heart</option>
                  <option>Lungs</option>
                  <option>Pancreas</option>
                  <option>Cornea</option>
                  <option>Skin</option>
                  <option>Bone Marrow</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
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
