<?php
include '../includes/auth.php';
require_login('donor');
include '../includes/db.php';
$page = 'profile';

$id = $_SESSION['id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE donors SET name=?, age=?, gender=?, blood_group=?, phone=? WHERE donor_id=?");
    $stmt->bind_param("sisssi", $name, $age, $gender, $blood_group, $phone, $id);
    $stmt->execute();
    $_SESSION['name'] = $name;
    $msg = "Profile updated successfully!";
}

$stmt = $conn->prepare("SELECT * FROM donors WHERE donor_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">My Profile</h3>
      <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

      <div class="card shadow-sm p-4" style="max-width:600px;">
        <form method="POST">
          <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($donor['name']); ?>" required>
          </div>
          <div class="mb-3">
            <label>Age</label>
            <input type="number" name="age" class="form-control" value="<?php echo $donor['age']; ?>" required>
          </div>
          <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control" required>
              <option <?php if($donor['gender']=='Male') echo 'selected'; ?>>Male</option>
              <option <?php if($donor['gender']=='Female') echo 'selected'; ?>>Female</option>
              <option <?php if($donor['gender']=='Other') echo 'selected'; ?>>Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Blood Group</label>
            <select name="blood_group" class="form-control" required>
              <?php foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg): ?>
                <option <?php if($donor['blood_group']==$bg) echo 'selected'; ?>><?php echo $bg; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo $donor['phone']; ?>" required>
          </div>
          <div class="mb-3">
            <label>Email (cannot be changed)</label>
            <input type="email" class="form-control" value="<?php echo $donor['email']; ?>" disabled>
          </div>
          <button class="btn btn-danger w-100">Update Profile</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
