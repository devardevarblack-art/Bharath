<?php
include '../includes/auth.php';
require_login('admin');
include '../includes/db.php';
$page = 'reports';

$totalDonors = $conn->query("SELECT COUNT(*) c FROM donors")->fetch_assoc()['c'];
$approvedDonors = $conn->query("SELECT COUNT(*) c FROM donors WHERE status='approved'")->fetch_assoc()['c'];
$pendingDonors = $conn->query("SELECT COUNT(*) c FROM donors WHERE status='pending'")->fetch_assoc()['c'];
$rejectedDonors = $conn->query("SELECT COUNT(*) c FROM donors WHERE status='rejected'")->fetch_assoc()['c'];

$totalPatients = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$totalHospitals = $conn->query("SELECT COUNT(*) c FROM hospitals")->fetch_assoc()['c'];
$approvedHospitals = $conn->query("SELECT COUNT(*) c FROM hospitals WHERE status='approved'")->fetch_assoc()['c'];

$totalRequests = $conn->query("SELECT COUNT(*) c FROM organ_requests")->fetch_assoc()['c'];
$pendingRequests = $conn->query("SELECT COUNT(*) c FROM organ_requests WHERE status='Pending'")->fetch_assoc()['c'];
$matchedRequests = $conn->query("SELECT COUNT(*) c FROM organ_requests WHERE status='Matched'")->fetch_assoc()['c'];
$completedRequests = $conn->query("SELECT COUNT(*) c FROM organ_requests WHERE status='Completed'")->fetch_assoc()['c'];

$totalMatches = $conn->query("SELECT COUNT(*) c FROM matching")->fetch_assoc()['c'];

// Organ-wise donor count
$organStats = $conn->query("SELECT organ_type, COUNT(*) c FROM donors WHERE status='approved' GROUP BY organ_type");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reports - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Reports & Statistics</h3>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card p-3 shadow-sm">
            <h5>Donors</h5>
            <p>Total: <?php echo $totalDonors; ?></p>
            <p>Approved: <span class="text-success"><?php echo $approvedDonors; ?></span></p>
            <p>Pending: <span class="text-warning"><?php echo $pendingDonors; ?></span></p>
            <p>Rejected: <span class="text-danger"><?php echo $rejectedDonors; ?></span></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 shadow-sm">
            <h5>Patients</h5>
            <p>Total Registered: <?php echo $totalPatients; ?></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 shadow-sm">
            <h5>Hospitals</h5>
            <p>Total: <?php echo $totalHospitals; ?></p>
            <p>Approved: <span class="text-success"><?php echo $approvedHospitals; ?></span></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 shadow-sm">
            <h5>Organ Requests</h5>
            <p>Total: <?php echo $totalRequests; ?></p>
            <p>Pending: <span class="text-warning"><?php echo $pendingRequests; ?></span></p>
            <p>Matched: <span class="text-info"><?php echo $matchedRequests; ?></span></p>
            <p>Completed: <span class="text-success"><?php echo $completedRequests; ?></span></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card p-3 shadow-sm">
            <h5>Total Matches Made</h5>
            <h3><?php echo $totalMatches; ?></h3>
          </div>
        </div>
      </div>

      <h5>Donor Count by Organ Type (Approved)</h5>
      <div class="table-responsive">
        <table class="table table-bordered bg-white">
          <thead class="table-dark"><tr><th>Organ Type</th><th>Available Donors</th></tr></thead>
          <tbody>
            <?php while ($r = $organStats->fetch_assoc()): ?>
            <tr><td><?php echo $r['organ_type']; ?></td><td><?php echo $r['c']; ?></td></tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <button class="btn btn-danger" onclick="window.print()">🖨️ Print Report</button>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
