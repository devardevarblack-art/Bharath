<?php
include '../includes/auth.php';
require_login('donor');
include '../includes/db.php';
$page = 'donation';

$id = $_SESSION['id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $organ_type = $_POST['organ_type'];
    $stmt = $conn->prepare("UPDATE donors SET organ_type=?, status='pending' WHERE donor_id=?");
    $stmt->bind_param("si", $organ_type, $id);
    $stmt->execute();
    $msg = "Organ donation details updated! Awaiting admin re-approval.";
}

$stmt = $conn->prepare("SELECT * FROM donors WHERE donor_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
$organs = ['Kidney','Liver','Heart','Lungs','Pancreas','Cornea','Skin','Bone Marrow'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organ Donation - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Organ Donation Registration</h3>
      <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

      <div class="card shadow-sm p-4" style="max-width:600px;">
        <p>Current Pledged Organ: <b><?php echo $donor['organ_type']; ?></b></p>
        <p>Status: 
          <?php
            $badge = $donor['status']=='approved' ? 'success' : ($donor['status']=='rejected' ? 'danger' : 'warning');
            echo "<span class='badge bg-$badge'>".ucfirst($donor['status'])."</span>";
          ?>
        </p>
        <form method="POST">
          <div class="mb-3">
            <label>Select Organ to Donate</label>
            <select name="organ_type" class="form-control" required>
              <?php foreach($organs as $o): ?>
                <option <?php if($donor['organ_type']==$o) echo 'selected'; ?>><?php echo $o; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="btn btn-danger w-100">Update Donation</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
