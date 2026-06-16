<?php
include '../includes/auth.php';
require_login('patient');
include '../includes/db.php';
$page = 'request';

$id = $_SESSION['id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $organ_type = $_POST['organ_type'];
    $priority = $_POST['priority_level'];
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO organ_requests (patient_id, organ_type, request_date, priority_level, status) VALUES (?,?,?,?,'Pending')");
    $stmt->bind_param("isss", $id, $organ_type, $date, $priority);
    $stmt->execute();
    $msg = "Organ request submitted successfully!";
}

$patient = $conn->query("SELECT * FROM patients WHERE patient_id=$id")->fetch_assoc();
$organs = ['Kidney','Liver','Heart','Lungs','Pancreas','Cornea','Skin','Bone Marrow'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Organ - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Request Organ</h3>
      <?php if ($msg): ?><div class="alert alert-success"><?php echo $msg; ?></div><?php endif; ?>

      <div class="card shadow-sm p-4" style="max-width:600px;">
        <form method="POST">
          <div class="mb-3">
            <label>Organ Type Required</label>
            <select name="organ_type" class="form-control" required>
              <?php foreach($organs as $o): ?>
                <option <?php if($patient['required_organ']==$o) echo 'selected'; ?>><?php echo $o; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Priority Level</label>
            <select name="priority_level" class="form-control" required>
              <option value="Critical">Critical</option>
              <option value="High">High</option>
              <option value="Normal" selected>Normal</option>
              <option value="Low">Low</option>
            </select>
          </div>
          <p>Your Blood Group: <b><?php echo $patient['blood_group']; ?></b> (used for matching)</p>
          <button class="btn btn-danger w-100">Submit Request</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
