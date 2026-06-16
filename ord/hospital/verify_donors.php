<?php
include '../includes/auth.php';
require_login('hospital');
include '../includes/db.php';
$page = 'vdonors';

$result = $conn->query("SELECT * FROM donors WHERE status='approved' ORDER BY donor_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Donors - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Verify Donors</h3>
      <p class="text-muted">View admin-approved donors available for organ donation.</p>
      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr><th>ID</th><th>Name</th><th>Age</th><th>Gender</th><th>Blood Group</th><th>Organ Type</th><th>Phone</th><th>Email</th></tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['donor_id']; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo $row['age']; ?></td>
              <td><?php echo $row['gender']; ?></td>
              <td><?php echo $row['blood_group']; ?></td>
              <td><?php echo $row['organ_type']; ?></td>
              <td><?php echo $row['phone']; ?></td>
              <td><?php echo $row['email']; ?></td>
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
