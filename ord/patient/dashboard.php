<?php
include '../includes/auth.php';
require_login('patient');
include '../includes/db.php';
$page = 'dashboard';

$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

$stmt2 = $conn->prepare("SELECT COUNT(*) c FROM organ_requests WHERE patient_id=?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$requestCount = $stmt2->get_result()->fetch_assoc()['c'];

$stmt3 = $conn->prepare("SELECT COUNT(*) c FROM matching WHERE patient_id=?");
$stmt3->bind_param("i", $id);
$stmt3->execute();
$matchCount = $stmt3->get_result()->fetch_assoc()['c'];

$stmt4 = $conn->prepare("SELECT COUNT(*) c FROM notifications WHERE patient_id=? AND is_read=0");
$stmt4->bind_param("i", $id);
$stmt4->execute();
$unreadNotif = $stmt4->get_result()->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($patient['name']); ?> 🙏</h3>

      <div class="row g-3">
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Required Organ</h6>
            <h4><?php echo $patient['required_organ']; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Total Requests</h6>
            <h4><?php echo $requestCount; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Matches Found</h6>
            <h4><?php echo $matchCount; ?></h4>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card shadow-sm p-3">
            <h6>Unread Notifications</h6>
            <h4><?php echo $unreadNotif; ?></h4>
          </div>
        </div>
      </div>

      <div class="card mt-4 p-3 shadow-sm">
        <h5>Your Details</h5>
        <p><b>Blood Group:</b> <?php echo $patient['blood_group']; ?></p>
        <p><b>Age:</b> <?php echo $patient['age']; ?></p>
        <p><b>Phone:</b> <?php echo $patient['phone']; ?> | <b>Email:</b> <?php echo $patient['email']; ?></p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
