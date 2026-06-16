<?php
include '../includes/auth.php';
require_login('donor');
include '../includes/db.php';
$page = 'dashboard';

$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM donors WHERE donor_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();

$stmt2 = $conn->prepare("SELECT COUNT(*) c FROM matching WHERE donor_id=?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$matchCount = $stmt2->get_result()->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donor Dashboard - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($donor['name']); ?> 🙏</h3>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card stat-card shadow-sm p-3">
            <h6>Account Status</h6>
            <h4>
              <?php
                $badge = $donor['status']=='approved' ? 'success' : ($donor['status']=='rejected' ? 'danger' : 'warning');
                echo "<span class='badge bg-$badge'>".ucfirst($donor['status'])."</span>";
              ?>
            </h4>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stat-card shadow-sm p-3">
            <h6>Organ Pledged</h6>
            <h4><?php echo $donor['organ_type']; ?></h4>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card stat-card shadow-sm p-3">
            <h6>Total Matches</h6>
            <h4><?php echo $matchCount; ?></h4>
          </div>
        </div>
      </div>

      <div class="card mt-4 p-3 shadow-sm">
        <h5>Your Donation Details</h5>
        <p><b>Blood Group:</b> <?php echo $donor['blood_group']; ?></p>
        <p><b>Age:</b> <?php echo $donor['age']; ?> | <b>Gender:</b> <?php echo $donor['gender']; ?></p>
        <p><b>Phone:</b> <?php echo $donor['phone']; ?> | <b>Email:</b> <?php echo $donor['email']; ?></p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
