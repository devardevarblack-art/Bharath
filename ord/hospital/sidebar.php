<div class="col-md-3 col-lg-2 bg-dark sidebar min-vh-100 p-3">
  <h5 class="text-white text-center mb-4">Hospital Panel</h5>
  <ul class="nav flex-column">
    <li><a href="dashboard.php" class="<?php echo $page=='dashboard'?'active':''; ?>">📊 Dashboard</a></li>
    <li><a href="verify_patients.php" class="<?php echo $page=='vpatients'?'active':''; ?>">✅ Verify Patients</a></li>
    <li><a href="verify_donors.php" class="<?php echo $page=='vdonors'?'active':''; ?>">✅ Verify Donors</a></li>
    <li><a href="organ_availability.php" class="<?php echo $page=='availability'?'active':''; ?>">🩺 Organ Availability</a></li>
    <li><a href="transplant_approval.php" class="<?php echo $page=='transplant'?'active':''; ?>">🏥 Transplant Approval</a></li>
    <li><a href="logout.php">🚪 Logout</a></li>
  </ul>
</div>
