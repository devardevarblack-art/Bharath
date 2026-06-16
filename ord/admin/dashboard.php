<?php
include '../includes/auth.php';
require_login('admin');
include '../includes/db.php';

$donorCount = $conn->query("SELECT COUNT(*) c FROM donors")->fetch_assoc()['c'];
$patientCount = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$hospitalCount = $conn->query("SELECT COUNT(*) c FROM hospitals")->fetch_assoc()['c'];
$pendingDonors = $conn->query("SELECT COUNT(*) c FROM donors WHERE status='pending'")->fetch_assoc()['c'];
$pendingHospitals = $conn->query("SELECT COUNT(*) c FROM hospitals WHERE status='pending'")->fetch_assoc()['c'];
$requestCount = $conn->query("SELECT COUNT(*) c FROM organ_requests")->fetch_assoc()['c'];
$matchCount = $conn->query("SELECT COUNT(*) c FROM matching")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 col-lg-2 bg-dark sidebar min-vh-100 p-3">
      <h5 class="text-white text-center mb-4">Admin Panel</h5>
      <ul class="nav flex-column">
        <li><a class="active" href="dashboard.php">📊 Dashboard</a></li>
        <li><a href="manage_donors.php">🧑‍🤝‍🧑 Manage Donors</a></li>
        <li><a href="manage_patients.php">🏥 Manage Patients</a></li>
        <li><a href="manage_hospitals.php">🏨 Manage Hospitals</a></li>
        <li><a href="manage_requests.php">📋 Organ Requests</a></li>
        <li><a href="reports.php">📈 Reports</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
      </ul>
    </div>

    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Admin Dashboard</h3>
      <div class="row g-3">
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Total Donors</h6>
            <h3><?php echo $donorCount; ?></h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Total Patients</h6>
            <h3><?php echo $patientCount; ?></h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Total Hospitals</h6>
            <h3><?php echo $hospitalCount; ?></h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Organ Requests</h6>
            <h3><?php echo $requestCount; ?></h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3 border-warning">
            <h6>Pending Donor Approvals</h6>
            <h3><?php echo $pendingDonors; ?></h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3 border-warning">
            <h6>Pending Hospital Approvals</h6>
            <h3><?php echo $pendingHospitals; ?></h3>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Total Matches</h6>
            <h3><?php echo $matchCount; ?></h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
