<?php
include '../includes/auth.php';
require_login('patient');
include '../includes/db.php';
$page = 'matched';

$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT matching.*, donors.name AS donor_name, donors.phone, donors.blood_group, donors.email FROM matching JOIN donors ON matching.donor_id=donors.donor_id WHERE matching.patient_id=? ORDER BY match_date DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Matched Donors - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Matched Donors</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr><th>Match ID</th><th>Donor Name</th><th>Organ Type</th><th>Blood Group</th><th>Contact</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['match_id']; ?></td>
                <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                <td><?php echo $row['organ_type']; ?></td>
                <td><?php echo $row['blood_group']; ?></td>
                <td><?php echo $row['phone']; ?> / <?php echo $row['email']; ?></td>
                <td><span class="badge bg-info"><?php echo $row['status']; ?></span></td>
                <td><?php echo $row['match_date']; ?></td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center">No matched donors yet.</td></tr>
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
