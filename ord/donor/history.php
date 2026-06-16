<?php
include '../includes/auth.php';
require_login('donor');
include '../includes/db.php';
$page = 'history';

$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT matching.*, patients.name AS patient_name FROM matching JOIN patients ON matching.patient_id=patients.patient_id WHERE matching.donor_id=? ORDER BY match_date DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donation History - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Donation History</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr><th>Match ID</th><th>Patient</th><th>Organ Type</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['match_id']; ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo $row['organ_type']; ?></td>
                <td><span class="badge bg-info"><?php echo $row['status']; ?></span></td>
                <td><?php echo $row['match_date']; ?></td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">No donation history yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
