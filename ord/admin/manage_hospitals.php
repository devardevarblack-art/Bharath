<?php
include '../includes/auth.php';
require_login('admin');
include '../includes/db.php';
$page = 'hospitals';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == 'approve') {
        $stmt = $conn->prepare("UPDATE hospitals SET status='approved' WHERE hospital_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($_GET['action'] == 'reject') {
        $stmt = $conn->prepare("UPDATE hospitals SET status='rejected' WHERE hospital_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    } elseif ($_GET['action'] == 'delete') {
        $stmt = $conn->prepare("DELETE FROM hospitals WHERE hospital_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: manage_hospitals.php");
    exit();
}

$result = $conn->query("SELECT * FROM hospitals ORDER BY hospital_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Hospitals - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Manage Hospitals</h3>
      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr>
              <th>ID</th><th>Hospital Name</th><th>Reg No</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['hospital_id']; ?></td>
              <td><?php echo htmlspecialchars($row['hospital_name']); ?></td>
              <td><?php echo $row['reg_no']; ?></td>
              <td><?php echo $row['email']; ?></td>
              <td><?php echo $row['phone']; ?></td>
              <td>
                <?php
                  $badge = $row['status']=='approved' ? 'success' : ($row['status']=='rejected' ? 'danger' : 'warning');
                  echo "<span class='badge bg-$badge'>".ucfirst($row['status'])."</span>";
                ?>
              </td>
              <td>
                <?php if ($row['status'] != 'approved'): ?>
                  <a href="?action=approve&id=<?php echo $row['hospital_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                <?php endif; ?>
                <?php if ($row['status'] != 'rejected'): ?>
                  <a href="?action=reject&id=<?php echo $row['hospital_id']; ?>" class="btn btn-warning btn-sm">Reject</a>
                <?php endif; ?>
                <a href="?action=delete&id=<?php echo $row['hospital_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this hospital?')">Delete</a>
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
