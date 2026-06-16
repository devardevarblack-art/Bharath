<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];

    if ($role == 'admin') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM admin WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['role'] = 'admin';
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['username'];
                header("Location: admin/dashboard.php");
                exit();
            }
        }
        header("Location: index.php?err=Invalid Admin Credentials");
        exit();
    }

    elseif ($role == 'donor') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM donors WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                if ($row['status'] != 'approved') {
                    header("Location: index.php?err=Account pending Admin approval");
                    exit();
                }
                $_SESSION['role'] = 'donor';
                $_SESSION['id'] = $row['donor_id'];
                $_SESSION['name'] = $row['name'];
                header("Location: donor/dashboard.php");
                exit();
            }
        }
        header("Location: index.php?err=Invalid Donor Credentials");
        exit();
    }

    elseif ($role == 'patient') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM patients WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['role'] = 'patient';
                $_SESSION['id'] = $row['patient_id'];
                $_SESSION['name'] = $row['name'];
                header("Location: patient/dashboard.php");
                exit();
            }
        }
        header("Location: index.php?err=Invalid Patient Credentials");
        exit();
    }

    elseif ($role == 'hospital') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT * FROM hospitals WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                if ($row['status'] != 'approved') {
                    header("Location: index.php?err=Account pending Admin approval");
                    exit();
                }
                $_SESSION['role'] = 'hospital';
                $_SESSION['id'] = $row['hospital_id'];
                $_SESSION['name'] = $row['hospital_name'];
                header("Location: hospital/dashboard.php");
                exit();
            }
        }
        header("Location: index.php?err=Invalid Hospital Credentials");
        exit();
    }
}
?>
