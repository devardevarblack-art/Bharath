<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">🫀 Organ Donate</a>
    <div class="ms-auto">
      <span class="text-white me-3"><?php echo isset($_SESSION['name']) ? 'Welcome, '.htmlspecialchars($_SESSION['name']) : ''; ?></span>
      <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
