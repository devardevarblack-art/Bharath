# Organ Donate – Smart Organ Donation and Transplant Management System

## Tech Stack
- Frontend: HTML5, CSS3, Bootstrap 5, JavaScript
- Backend: PHP 8
- Database: MySQL
- Server: XAMPP

## Setup Instructions

1. **Install XAMPP** (with PHP 8 and MySQL) and start Apache + MySQL.

2. **Copy project folder**
   - Extract this ZIP into `C:/xampp/htdocs/organdonate` (Windows) 
     or `/Applications/XAMPP/htdocs/organdonate` (Mac).

3. **Run setup script**
   - Open browser: `http://localhost/organdonate/setup.php`
   - This creates the `organ_donate` database with all tables and a default Admin account.

4. **Default Admin Login**
   - Username: `admin`
   - Password: `admin123`

5. **Access the system**
   - Open `http://localhost/organdonate/index.php`
   - Login as Admin / Donor / Patient / Hospital using the tabs.

## Login Flow
- **Admin**: Login only (no registration). Use default credentials above.
- **Donor**: Register → Wait for Admin Approval → Login → Dashboard.
- **Patient**: Register → Login (instant access) → Dashboard.
- **Hospital**: Register → Wait for Admin Approval → Login → Dashboard.

## Folder Structure
```
organdonate/
├── index.php              # Landing page with login tabs
├── login.php              # Login processing
├── setup.php              # One-time DB setup script
├── database.sql           # Database schema
├── includes/
│   ├── db.php              # DB connection
│   ├── auth.php            # Session/login guard
│   └── header.php          # Common navbar
├── assets/css/style.css   # Custom styles
├── admin/                  # Admin module
├── donor/                  # Donor module
├── patient/                # Patient module
└── hospital/               # Hospital module
```

## Modules Implemented
1. **Admin**: Dashboard, Manage Donors/Patients/Hospitals, Approve Donors/Hospitals, Organ Request Monitoring & Matching, Reports
2. **Donor**: Registration, Login, Profile, Organ Donation Registration, Donation History, Status Tracking
3. **Patient**: Registration, Login, Organ Request, Track Request, Matched Donors, Notifications
4. **Hospital**: Registration, Login, Verify Patients/Donors, Organ Availability, Transplant Approval
5. **Organ Matching System**: Matches based on Blood Group + Organ Type + Donor Availability + Priority Level (admin-triggered, notifies patient)
