<?php
/**
 * Home Page - Organ Donate System
 */

require_once 'config/constants.php';
require_once 'config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_DESCRIPTION; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: #dc3545 !important;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">❤️ <?php echo SITE_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown">
                            Login
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                            <li><a class="dropdown-item" href="auth/admin_login.php">Admin Login</a></li>
                            <li><a class="dropdown-item" href="auth/donor_login.php">Donor Login</a></li>
                            <li><a class="dropdown-item" href="auth/patient_login.php">Patient Login</a></li>
                            <li><a class="dropdown-item" href="auth/hospital_login.php">Hospital Login</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="registerDropdown" role="button" data-bs-toggle="dropdown">
                            Register
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="registerDropdown">
                            <li><a class="dropdown-item" href="auth/donor_register.php">Register as Donor</a></li>
                            <li><a class="dropdown-item" href="auth/patient_register.php">Register as Patient</a></li>
                            <li><a class="dropdown-item" href="auth/hospital_register.php">Register as Hospital</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Save Lives Through Organ Donation</h1>
            <p class="lead mb-4">Smart Organ Donation and Transplant Management System</p>
            <div>
                <a href="auth/donor_register.php" class="btn btn-light btn-lg me-2">Become a Donor</a>
                <a href="auth/patient_register.php" class="btn btn-outline-light btn-lg">Request an Organ</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">About Our System</h2>
            <div class="row">
                <div class="col-md-6">
                    <h4>Our Mission</h4>
                    <p>To provide a seamless platform that connects donors, patients, hospitals, and medical professionals to facilitate timely and efficient organ transplantation.</p>
                </div>
                <div class="col-md-6">
                    <h4>Key Features</h4>
                    <ul>
                        <li>Real-time Organ Matching</li>
                        <li>Secure Data Management</li>
                        <li>Hospital Verification</li>
                        <li>Donor & Patient Tracking</li>
                        <li>Comprehensive Reporting</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">🏥 Hospital Management</h5>
                            <p class="card-text">Verified hospital network with secure verification process</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">🔍 Smart Matching</h5>
                            <p class="card-text">Intelligent algorithm matching donors with patients</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">📊 Analytics</h5>
                            <p class="card-text">Comprehensive reports and analytics dashboard</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About</h5>
                    <p><?php echo SITE_DESCRIPTION; ?></p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50">Privacy Policy</a></li>
                        <li><a href="#" class="text-white-50">Terms of Service</a></li>
                        <li><a href="#" class="text-white-50">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p class="text-white-50">
                        Email: info@organdonare.com<br>
                        Phone: +91 1234567890
                    </p>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center text-white-50">
                <p>&copy; 2026 <?php echo SITE_NAME; ?>. All rights reserved. | College Final Year Project</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>