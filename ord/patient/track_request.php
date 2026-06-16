<?php
include '../includes/auth.php';
require_login('patient');
include '../includes/db.php';
$page = 'track';

$id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM organ_requests WHERE patient_id=? ORDER BY request_date DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Track Request - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Track Organ Requests</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr><th>Request ID</th><th>Organ Type</th><th>Priority</th><th>Request Date</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['request_id']; ?></td>
                <td><?php echo $row['organ_type']; ?></td>
                <td><?php echo $row['priority_level']; ?></td>
                <td><?php echo $row['request_date']; ?></td>
                <td>
                  <?php
                    $badge = $row['status']=='Completed' ? 'success' : ($row['status']=='Matched' ? 'info' : 'warning');
                    echo "<span class='badge bg-$badge'>".$row['status']."</span>";
                  ?>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center">No requests submitted yet.</td></tr>
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
