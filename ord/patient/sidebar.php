<div class="col-md-3 col-lg-2 bg-dark sidebar min-vh-100 p-3">
  <h5 class="text-white text-center mb-4">Patient Panel</h5>
  <ul class="nav flex-column">
    <li><a href="dashboard.php" class="<?php echo $page=='dashboard'?'active':''; ?>">📊 Dashboard</a></li>
    <li><a href="request_organ.php" class="<?php echo $page=='request'?'active':''; ?>">📝 Request Organ</a></li>
    <li><a href="track_request.php" class="<?php echo $page=='track'?'active':''; ?>">📍 Track Request</a></li>
    <li><a href="matched_donors.php" class="<?php echo $page=='matched'?'active':''; ?>">🤝 Matched Donors</a></li>
    <li><a href="notifications.php" class="<?php echo $page=='notif'?'active':''; ?>">🔔 Notifications</a></li>
    <li><a href="logout.php">🚪 Logout</a></li>
  </ul>
</div>
