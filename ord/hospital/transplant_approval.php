<?php
include '../includes/auth.php';
require_login('hospital');
include '../includes/db.php';
$page = 'transplant';

if (isset($_GET['action']) && $_GET['action']=='complete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE matching SET status='Completed' WHERE match_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt2 = $conn->prepare("SELECT * FROM matching WHERE match_id=?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $row = $stmt2->get_result()->fetch_assoc();
    if ($row) {
        $stmt3 = $conn->prepare("UPDATE organ_requests SET status='Completed' WHERE patient_id=? AND organ_type=?");
        $stmt3->bind_param("is", $row['patient_id'], $row['organ_type']);
        $stmt3->execute();
    }
    header("Location: transplant_approval.php?msg=Transplant marked as completed");
    exit();
}

$result = $conn->query("SELECT matching.*, donors.name AS donor_name, patients.name AS patient_name 
                         FROM matching 
                         JOIN donors ON matching.donor_id=donors.donor_id 
                         JOIN patients ON matching.patient_id=patients.patient_id 
                         ORDER BY match_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transplant Approval - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Transplant Approval</h3>
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
      <?php endif; ?>

      <div class="table-responsive">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr><th>Match ID</th><th>Donor</th><th>Patient</th><th>Organ Type</th><th>Status</th><th>Date</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?php echo $row['match_id']; ?></td>
                <td><?php echo htmlspecialchars($row['donor_name']); ?></td>
                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                <td><?php echo $row['organ_type']; ?></td>
                <td>
                  <?php
                    $badge = $row['status']=='Completed' ? 'success' : 'info';
                    echo "<span class='badge bg-$badge'>".$row['status']."</span>";
                  ?>
                </td>
                <td><?php echo $row['match_date']; ?></td>
                <td>
                  <?php if ($row['status']=='Matched'): ?>
                    <a href="?action=complete&id=<?php echo $row['match_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Approve and complete this transplant?')">Approve & Complete</a>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="7" class="text-center">No transplant matches yet.</td></tr>
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
