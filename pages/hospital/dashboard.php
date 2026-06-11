<?php
/**
 * Hospital Dashboard
 */

require_once '../../config/constants.php';
require_once '../../config/db_config.php';
require_once '../../config/session.php';

if (!check_role('hospital')) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$hospital_id = $_SESSION['user_id'];
$hospital = $conn->query("SELECT * FROM hospitals WHERE hospital_id = $hospital_id")->fetch_assoc();
$verified_donors = $conn->query("SELECT COUNT(*) as count FROM donors WHERE hospital_id = $hospital_id")->fetch_assoc()['count'];
$transplants = $conn->query("SELECT COUNT(*) as count FROM matching WHERE hospital_id = $hospital_id AND status = 'completed'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning text-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">❤️ <?php echo SITE_NAME; ?></a>
            <span class="navbar-text text-dark">Hospital: <?php echo $_SESSION['user_name']; ?></span>
            <a href="logout.php" class="btn btn-outline-dark btn-sm ms-2">Logout</a>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h2>Welcome, <?php echo $hospital['hospital_name']; ?>!</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Verified Donors</h5>
                        <p class="card-text display-4"><?php echo $verified_donors; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Completed Transplants</h5>
                        <p class="card-text display-4"><?php echo $transplants; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Registration Number</h5>
                        <p class="card-text"><?php echo $hospital['reg_no']; ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-success"><?php echo $hospital['status']; ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Actions</h4>
                <div class="btn-group" role="group">
                    <a href="verify_patients.php" class="btn btn-primary">Verify Patients</a>
                    <a href="verify_donors.php" class="btn btn-info">Verify Donors</a>
                    <a href="organ_availability.php" class="btn btn-success">Organ Availability</a>
                    <a href="transplant_approval.php" class="btn btn-warning">Transplant Approval</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>