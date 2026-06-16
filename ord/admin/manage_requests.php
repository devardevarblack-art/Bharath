<?php
include '../includes/auth.php';
require_login('admin');
include '../includes/db.php';
$page = 'requests';

// Handle status change
if (isset($_GET['action']) && $_GET['action']=='complete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE organ_requests SET status='Completed' WHERE request_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_requests.php");
    exit();
}

// Find matches for a given request
$matchResults = [];
$searchedRequest = null;
if (isset($_GET['find_match']) && isset($_GET['rid'])) {
    $rid = intval($_GET['rid']);
    $stmt = $conn->prepare("SELECT * FROM organ_requests JOIN patients ON organ_requests.patient_id=patients.patient_id WHERE request_id=?");
    $stmt->bind_param("i", $rid);
    $stmt->execute();
    $reqRow = $stmt->get_result()->fetch_assoc();
    $searchedRequest = $reqRow;
    if ($reqRow) {
        $bg = $reqRow['blood_group'];
        $organ = $reqRow['organ_type'];
        $stmt = $conn->prepare("SELECT * FROM donors WHERE blood_group=? AND organ_type=? AND status='approved'");
        $stmt->bind_param("ss", $bg, $organ);
        $stmt->execute();
        $matchResults = $stmt->get_result();
    }
}

// Create a match record
if (isset($_GET['create_match']) && isset($_GET['donor_id']) && isset($_GET['patient_id']) && isset($_GET['organ_type']) && isset($_GET['rid'])) {
    $donor_id = intval($_GET['donor_id']);
    $patient_id = intval($_GET['patient_id']);
    $organ_type = $_GET['organ_type'];
    $rid = intval($_GET['rid']);

    $stmt = $conn->prepare("INSERT INTO matching (donor_id, patient_id, organ_type, status) VALUES (?, ?, ?, 'Matched')");
    $stmt->bind_param("iis", $donor_id, $patient_id, $organ_type);
    $stmt->execute();

    $conn->query("UPDATE organ_requests SET status='Matched' WHERE request_id=$rid");

    $msg = "Good news! A matching donor has been found for your $organ_type request.";
    $stmt2 = $conn->prepare("INSERT INTO notifications (patient_id, message) VALUES (?, ?)");
    $stmt2->bind_param("is", $patient_id, $msg);
    $stmt2->execute();

    header("Location: manage_requests.php?msg=Match created and patient notified");
    exit();
}

$result = $conn->query("SELECT organ_requests.*, patients.name, patients.blood_group FROM organ_requests JOIN patients ON organ_requests.patient_id=patients.patient_id ORDER BY request_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Organ Requests - Organ Donate</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-9 col-lg-10 p-4">
      <h3 class="mb-4">Organ Request Monitoring & Matching</h3>

      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
      <?php endif; ?>

      <div class="table-responsive mb-5">
        <table class="table table-bordered table-striped bg-white">
          <thead class="table-dark">
            <tr>
              <th>Req ID</th><th>Patient</th><th>Blood Group</th><th>Organ Type</th>
              <th>Priority</th><th>Date</th><th>Status</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['request_id']; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo $row['blood_group']; ?></td>
              <td><?php echo $row['organ_type']; ?></td>
              <td><?php echo $row['priority_level']; ?></td>
              <td><?php echo $row['request_date']; ?></td>
              <td>
                <?php
                  $badge = $row['status']=='Completed' ? 'success' : ($row['status']=='Matched' ? 'info' : 'warning');
                  echo "<span class='badge bg-$badge'>".$row['status']."</span>";
                ?>
              </td>
              <td>
                <?php if ($row['status']=='Pending'): ?>
                  <a href="?find_match=1&rid=<?php echo $row['request_id']; ?>" class="btn btn-primary btn-sm">Find Match</a>
                <?php endif; ?>
                <?php if ($row['status']=='Matched'): ?>
                  <a href="?action=complete&id=<?php echo $row['request_id']; ?>" class="btn btn-success btn-sm">Mark Completed</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>

      <?php if ($searchedRequest): ?>
        <h4>Matching Donors for Request #<?php echo $searchedRequest['request_id']; ?> 
          (Blood Group: <?php echo $searchedRequest['blood_group']; ?>, Organ: <?php echo $searchedRequest['organ_type']; ?>)
        </h4>
        <div class="table-responsive">
          <table class="table table-bordered bg-white">
            <thead class="table-dark">
              <tr><th>Donor ID</th><th>Name</th><th>Age</th><th>Blood Group</th><th>Organ Type</th><th>Phone</th><th>Action</th></tr>
            </thead>
            <tbody>
              <?php if ($matchResults && $matchResults->num_rows > 0): ?>
                <?php while ($d = $matchResults->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $d['donor_id']; ?></td>
                  <td><?php echo htmlspecialchars($d['name']); ?></td>
                  <td><?php echo $d['age']; ?></td>
                  <td><?php echo $d['blood_group']; ?></td>
                  <td><?php echo $d['organ_type']; ?></td>
                  <td><?php echo $d['phone']; ?></td>
                  <td>
                    <a href="?create_match=1&rid=<?php echo $searchedRequest['request_id']; ?>&donor_id=<?php echo $d['donor_id']; ?>&patient_id=<?php echo $searchedRequest['patient_id']; ?>&organ_type=<?php echo urlencode($d['organ_type']); ?>" 
                       class="btn btn-success btn-sm" onclick="return confirm('Confirm this match?')">Confirm Match</a>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="7" class="text-center">No matching donors found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
