<?php
include '../backend/config.php';
include '../backend/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $age = sanitize($_POST['age']);
    $gender = sanitize($_POST['gender']);
    $blood_group = sanitize($_POST['blood_group']);
    $organ_type = sanitize($_POST['organ_type']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $state = sanitize($_POST['state']);
    
    if (empty($name) || empty($age) || empty($gender) || empty($blood_group) || 
        empty($organ_type) || empty($phone) || empty($email) || empty($password)) {
        set_error("All fields are required!");
    } elseif ($password !== $confirm_password) {
        set_error("Passwords do not match!");
    } else {
        // Check if email/phone already exists
        $check_query = "SELECT * FROM donors WHERE email = ? OR phone = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ss", $email, $phone);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            set_error("Email or Phone number already registered!");
        } else {
            $hashed_password = hash_password($password);
            $insert_query = "INSERT INTO donors (name, age, gender, blood_group, organ_type, phone, email, password, address, city, state, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("sisssssssss", $name, $age, $gender, $blood_group, $organ_type, $phone, $email, $hashed_password, $address, $city, $state);
            
            if ($insert_stmt->execute()) {
                set_success("Registration successful! Wait for admin approval.");
                header("Location: index.php");
                exit();
            } else {
                set_error("Registration failed! Try again.");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Registration - Organ Donate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h3>Donor Registration</h3>
                    </div>
                    <div class="card-body">
                        <?php display_message(); ?>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="age" class="form-label">Age</label>
                                        <input type="number" class="form-control" id="age" name="age" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-control" id="gender" name="gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="blood_group" class="form-label">Blood Group</label>
                                        <select class="form-control" id="blood_group" name="blood_group" required>
                                            <option value="">Select Blood Group</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="organ_type" class="form-label">Organ Type to Donate</label>
                                        <select class="form-control" id="organ_type" name="organ_type" required>
                                            <option value="">Select Organ</option>
                                            <option value="Kidney">Kidney</option>
                                            <option value="Liver">Liver</option>
                                            <option value="Heart">Heart</option>
                                            <option value="Pancreas">Pancreas</option>
                                            <option value="Lung">Lung</option>
                                            <option value="Cornea">Cornea</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Register</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p>Already have an account? <a href="donor-login.php" class="text-decoration-none">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
