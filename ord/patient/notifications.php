<?php
include '../includes/auth.php';
require_login('patient');
include '../includes/db.php';
$page = 'notif';

$id = $_SESSION['id'];

// Mark all as read
$stmt = $conn->prepare("UPDATE notifications SET is_read=1 WHERE patient_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt2 = $conn->prepare("SELECT * FROM notifications WHERE patient_id=? ORDER BY created_at DESC");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$result = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifications - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Notifications</h3>

      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="alert alert-info">
            <?php echo htmlspecialchars($row['message']); ?>
            <br><small class="text-muted"><?php echo $row['created_at']; ?></small>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="alert alert-secondary">No notifications yet.</div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
