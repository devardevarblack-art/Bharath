<?php
// Run this once via browser: http://localhost/organdonate/setup.php
$host = "localhost";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$conn->query("CREATE DATABASE IF NOT EXISTS organ_donate");
$conn->select_db("organ_donate");

$sql = file_get_contents(__DIR__ . '/database.sql');
// Remove the database creation/use/insert lines since we'll handle manually
$sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
$sql = preg_replace('/USE organ_donate;/is', '', $sql);
$sql = preg_replace('/INSERT INTO admin.*?;/is', '', $sql);
$sql = preg_replace('/--.*$/m', '', $sql);

foreach (array_filter(array_map('trim', explode(';', $sql))) as $q) {
    if ($q) {
        if (!$conn->query($q)) {
            echo "Error: " . $conn->error . "<br>SQL: " . $q . "<br>";
        }
    }
}

// Insert or repair admin with proper hashed password
$adminUser = "admin";
$adminPlainPassword = "admin123";
$adminHash = password_hash($adminPlainPassword, PASSWORD_DEFAULT);

$check = $conn->query("SELECT * FROM admin WHERE username='admin'");
if ($check->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminUser, $adminHash);
    $stmt->execute();
} else {
    $row = $check->fetch_assoc();
    // Repair the default placeholder hash or plain-text default password if found.
    if ($row['password'] === '$2y$10$92IXUNpkjO0rOQ5byMi.YeYBz3J0r9q7c.4S6e3kF8d8d8d8d8d8d.' || $row['password'] === $adminPlainPassword) {
        $stmt = $conn->prepare("UPDATE admin SET password=? WHERE username=?");
        $stmt->bind_param("ss", $adminHash, $adminUser);
        $stmt->execute();
    }
}

echo "<h2>Setup Complete!</h2>";
echo "<p>Database 'organ_donate' created with all tables.</p>";
echo "<p><b>Admin Login:</b><br>Username: admin<br>Password: admin123</p>";
echo "<p><a href='index.php'>Go to Login Page</a></p>";
?>
