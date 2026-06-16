<?php
include '../includes/auth.php';
require_login('admin');
include '../includes/db.php';
$page = 'patients';

if (isset($_GET['action']) && $_GET['action']=='delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM patients WHERE patient_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_patients.php");
    exit();
}

$result = $conn->query("SELECT * FROM patients ORDER BY patient_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Patients - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Manage Patients</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr>
              <th>ID</th><th>Name</th><th>Age</th><th>Blood Group</th>
              <th>Required Organ</th><th>Phone</th><th>Email</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['patient_id']; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo $row['age']; ?></td>
              <td><?php echo $row['blood_group']; ?></td>
              <td><?php echo $row['required_organ']; ?></td>
              <td><?php echo $row['phone']; ?></td>
              <td><?php echo $row['email']; ?></td>
              <td>
                <a href="?action=delete&id=<?php echo $row['patient_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this patient?')">Delete</a>
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
