<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organ Donate - Smart Organ Donation & Transplant Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🫀 Organ Donate</a>
    <div class="ms-auto">
      <a href="#login" class="btn btn-light btn-sm">Login</a>
    </div>
  </div>
</nav>

<div class="hero text-center text-white py-5">
  <div class="container">
    <h1 class="fw-bold">Smart Organ Donation & Transplant Management System</h1>
    <p class="lead">Connecting Donors, Patients and Hospitals to Save Lives</p>
  </div>
</div>

<div class="container my-5" id="login">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-body">

          <?php if (isset($_GET['err'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['err']); ?></div>
          <?php endif; ?>
          <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
          <?php endif; ?>

          <ul class="nav nav-tabs" id="loginTab" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#admin-tab">Admin</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#donor-tab">Donor</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#patient-tab">Patient</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#hospital-tab">Hospital</button></li>
          </ul>

          <div class="tab-content p-3">
            <!-- ADMIN -->
            <div class="tab-pane fade show active" id="admin-tab">
              <form action="login.php" method="POST">
                <input type="hidden" name="role" value="admin">
                <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <button class="btn btn-danger w-100">Admin Login</button>
              </form>
            </div>

            <!-- DONOR -->
            <div class="tab-pane fade" id="donor-tab">
              <form action="login.php" method="POST">
                <input type="hidden" name="role" value="donor">
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <button class="btn btn-danger w-100">Donor Login</button>
              </form>
              <p class="mt-2 text-center">New Donor? <a href="donor/register.php">Register here</a></p>
            </div>

            <!-- PATIENT -->
            <div class="tab-pane fade" id="patient-tab">
              <form action="login.php" method="POST">
                <input type="hidden" name="role" value="patient">
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <button class="btn btn-danger w-100">Patient Login</button>
              </form>
              <p class="mt-2 text-center">New Patient? <a href="patient/register.php">Register here</a></p>
            </div>

            <!-- HOSPITAL -->
            <div class="tab-pane fade" id="hospital-tab">
              <form action="login.php" method="POST">
                <input type="hidden" name="role" value="hospital">
                <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                <button class="btn btn-danger w-100">Hospital Login</button>
              </form>
              <p class="mt-2 text-center">New Hospital? <a href="hospital/register.php">Register here</a></p>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<footer class="bg-dark text-white text-center py-3">
  &copy; <?php echo date("Y"); ?> Organ Donate. All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
