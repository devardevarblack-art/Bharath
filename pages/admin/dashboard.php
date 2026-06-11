<?php
/**
 * Admin Dashboard
 */

require_once '../../config/constants.php';
require_once '../../config/db_config.php';
require_once '../../config/session.php';

if (!check_role('admin')) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Get statistics
$donors_count = $conn->query("SELECT COUNT(*) as count FROM donors")->fetch_assoc()['count'];
$patients_count = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
$hospitals_count = $conn->query("SELECT COUNT(*) as count FROM hospitals")->fetch_assoc()['count'];
$pending_donors = $conn->query("SELECT COUNT(*) as count FROM donors WHERE status = 'pending'")->fetch_assoc()['count'];
$pending_hospitals = $conn->query("SELECT COUNT(*) as count FROM hospitals WHERE status = 'pending'")->fetch_assoc()['count'];
$matched_requests = $conn->query("SELECT COUNT(*) as count FROM matching WHERE status = 'matched'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">❤️ <?php echo SITE_NAME; ?></a>
            <span class="navbar-text text-white">Admin: <?php echo $_SESSION['username']; ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Logout</a>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Donors</h5>
                        <p class="card-text display-4"><?php echo $donors_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Total Patients</h5>
                        <p class="card-text display-4"><?php echo $patients_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Total Hospitals</h5>
                        <p class="card-text display-4"><?php echo $hospitals_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Matched Requests</h5>
                        <p class="card-text display-4"><?php echo $matched_requests; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Pending Approvals</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-warning" role="alert">
                            <h5>Pending Donors: <span class="badge bg-warning"><?php echo $pending_donors; ?></span></h5>
                            <a href="manage_donors.php" class="btn btn-sm btn-warning">View & Approve</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-warning" role="alert">
                            <h5>Pending Hospitals: <span class="badge bg-warning"><?php echo $pending_hospitals; ?></span></h5>
                            <a href="manage_hospitals.php" class="btn btn-sm btn-warning">View & Approve</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Management Links</h3>
                <div class="btn-group" role="group">
                    <a href="manage_donors.php" class="btn btn-primary">Manage Donors</a>
                    <a href="manage_patients.php" class="btn btn-info">Manage Patients</a>
                    <a href="manage_hospitals.php" class="btn btn-warning">Manage Hospitals</a>
                    <a href="manage_requests.php" class="btn btn-success">Manage Requests</a>
                    <a href="reports.php" class="btn btn-secondary">Reports</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>