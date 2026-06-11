<?php
/**
 * Donor Dashboard
 */

require_once '../../config/constants.php';
require_once '../../config/db_config.php';
require_once '../../config/session.php';

if (!check_role('donor')) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

$donor_id = $_SESSION['user_id'];
$donor = $conn->query("SELECT * FROM donors WHERE donor_id = $donor_id")->fetch_assoc();
$donation_count = $conn->query("SELECT COUNT(*) as count FROM organ_requests WHERE donor_id = $donor_id")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">❤️ <?php echo SITE_NAME; ?></a>
            <span class="navbar-text text-white">Donor: <?php echo $_SESSION['user_name']; ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Logout</a>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h2>Welcome, <?php echo $donor['name']; ?>!</h2>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Your Information</h5>
                        <p><strong>Age:</strong> <?php echo $donor['age']; ?></p>
                        <p><strong>Gender:</strong> <?php echo $donor['gender']; ?></p>
                        <p><strong>Blood Group:</strong> <?php echo $donor['blood_group']; ?></p>
                        <p><strong>Organ Type:</strong> <?php echo $donor['organ_type']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $donor['phone']; ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-success"><?php echo $donor['status']; ?></span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Donation Statistics</h5>
                        <p class="display-4"><?php echo $donation_count; ?></p>
                        <p>Total Donation Requests</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Actions</h4>
                <div class="btn-group" role="group">
                    <a href="profile.php" class="btn btn-primary">View Profile</a>
                    <a href="donation_history.php" class="btn btn-info">Donation History</a>
                    <a href="status_tracking.php" class="btn btn-success">Track Status</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>