<?php
include '../includes/auth.php';
require_login('hospital');
include '../includes/db.php';
$page = 'dashboard';

$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM hospitals WHERE hospital_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$hospital = $stmt->get_result()->fetch_assoc();

$donorCount = $conn->query("SELECT COUNT(*) c FROM donors WHERE status='approved'")->fetch_assoc()['c'];
$patientCount = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$pendingMatches = $conn->query("SELECT COUNT(*) c FROM matching WHERE status='Matched'")->fetch_assoc()['c'];
$completedMatches = $conn->query("SELECT COUNT(*) c FROM matching WHERE status='Completed'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hospital Dashboard - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($hospital['hospital_name']); ?> 🏥</h3>

      <div class="row g-3">
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Approved Donors</h6>
            <h4><?php echo $donorCount; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Registered Patients</h6>
            <h4><?php echo $patientCount; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Pending Transplants</h6>
            <h4><?php echo $pendingMatches; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Completed Transplants</h6>
            <h4><?php echo $completedMatches; ?></h4>
          </div>
        </div>
      </div>

      <div class="card mt-4 p-3 shadow-sm">
        <h5>Hospital Details</h5>
        <p><b>Registration No:</b> <?php echo $hospital['reg_no']; ?></p>
        <p><b>Email:</b> <?php echo $hospital['email']; ?> | <b>Phone:</b> <?php echo $hospital['phone']; ?></p>
        <p><b>Status:</b> <span class="badge bg-success"><?php echo ucfirst($hospital['status']); ?></span></p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
