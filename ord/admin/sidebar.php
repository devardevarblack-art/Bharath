<div class="col-md-3 col-lg-2 bg-dark sidebar min-vh-100 p-3">
  <h5 class="text-white text-center mb-4">Admin Panel</h5>
  <ul class="nav flex-column">
    <li><a href="dashboard.php" class="<?php echo $page=='dashboard'?'active':''; ?>">📊 Dashboard</a></li>
    <li><a href="manage_donors.php" class="<?php echo $page=='donors'?'active':''; ?>">🧑‍🤝‍🧑 Manage Donors</a></li>
    <li><a href="manage_patients.php" class="<?php echo $page=='patients'?'active':''; ?>">🏥 Manage Patients</a></li>
    <li><a href="manage_hospitals.php" class="<?php echo $page=='hospitals'?'active':''; ?>">🏨 Manage Hospitals</a></li>
    <li><a href="manage_requests.php" class="<?php echo $page=='requests'?'active':''; ?>">📋 Organ Requests</a></li>
    <li><a href="reports.php" class="<?php echo $page=='reports'?'active':''; ?>">📈 Reports</a></li>
    <li><a href="logout.php">🚪 Logout</a></li>
  </ul>
</div>
