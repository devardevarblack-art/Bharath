<?php
include '../includes/auth.php';
require_login('donor');
include '../includes/db.php';
$page = 'status';

$id = $_SESSION['id'];
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
<title>Status Tracking - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Status Tracking</h3>

      <div class="card shadow-sm p-4">
        <h5>Registration Status</h5>
        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between">
            Registration Submitted <span class="badge bg-success">Done</span>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            Admin Review
            <?php
              if ($donor['status']=='pending') echo "<span class='badge bg-warning'>In Progress</span>";
              else echo "<span class='badge bg-success'>Done</span>";
            ?>
          </li>
          <li class="list-group-item d-flex justify-content-between">
            Final Status
            <?php
              $badge = $donor['status']=='approved' ? 'success' : ($donor['status']=='rejected' ? 'danger' : 'secondary');
              echo "<span class='badge bg-$badge'>".ucfirst($donor['status'])."</span>";
            ?>
          </li>
        </ul>

        <?php if ($donor['status']=='pending'): ?>
          <div class="alert alert-warning">Your account is awaiting admin approval. You can login once approved.</div>
        <?php elseif ($donor['status']=='approved'): ?>
          <div class="alert alert-success">Your account is approved! You are eligible for organ matching.</div>
        <?php else: ?>
          <div class="alert alert-danger">Your registration was rejected. Please contact support.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
