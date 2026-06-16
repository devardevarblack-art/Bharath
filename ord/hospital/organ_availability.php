<?php
include '../includes/auth.php';
require_login('hospital');
include '../includes/db.php';
$page = 'availability';

$result = $conn->query("SELECT organ_type, COUNT(*) as available_count FROM donors WHERE status='approved' GROUP BY organ_type ORDER BY organ_type");
$requestResult = $conn->query("SELECT organ_type, COUNT(*) as required_count FROM organ_requests WHERE status='Pending' GROUP BY organ_type ORDER BY organ_type");

$required = [];
while ($r = $requestResult->fetch_assoc()) {
    $required[$r['organ_type']] = $r['required_count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organ Availability - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Organ Availability Management</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr><th>Organ Type</th><th>Available Donors</th><th>Pending Requests</th><th>Status</th></tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): 
              $organ = $row['organ_type'];
              $req = $required[$organ] ?? 0;
            ?>
            <tr>
              <td><?php echo $organ; ?></td>
              <td><?php echo $row['available_count']; ?></td>
              <td><?php echo $req; ?></td>
              <td>
                <?php if ($row['available_count'] >= $req && $req > 0): ?>
                  <span class="badge bg-success">Sufficient</span>
                <?php elseif ($req > 0): ?>
                  <span class="badge bg-danger">Shortage</span>
                <?php else: ?>
                  <span class="badge bg-secondary">No Demand</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
