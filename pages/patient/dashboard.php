<?php
/**
 * Patient Dashboard
 */

require_once '../../config/constants.php';
require_once '../../config/db_config.php';
require_once '../../config/session.php';

if (!check_role('patient')) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$patient_id = $_SESSION['user_id'];
$patient = $conn->query("SELECT * FROM patients WHERE patient_id = $patient_id")->fetch_assoc();
$request_count = $conn->query("SELECT COUNT(*) as count FROM organ_requests WHERE patient_id = $patient_id")->fetch_assoc()['count'];
$matched_count = $conn->query("SELECT COUNT(*) as count FROM matching WHERE patient_id = $patient_id AND status = 'matched'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">❤️ <?php echo SITE_NAME; ?></a>
            <span class="navbar-text text-white">Patient: <?php echo $_SESSION['user_name']; ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Logout</a>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h2>Welcome, <?php echo $patient['name']; ?>!</h2>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Your Requests</h5>
                        <p class="card-text display-4"><?php echo $request_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Matched Donors</h5>
                        <p class="card-text display-4"><?php echo $matched_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Required Organ</h5>
                        <p class="card-text"><?php echo $patient['required_organ']; ?> (<?php echo $patient['blood_group']; ?>)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Actions</h4>
                <div class="btn-group" role="group">
                    <a href="request_organ.php" class="btn btn-primary">Request Organ</a>
                    <a href="track_request.php" class="btn btn-info">Track Requests</a>
                    <a href="matched_donors.php" class="btn btn-success">View Matched Donors</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>