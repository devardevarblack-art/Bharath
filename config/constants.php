<?php
/**
 * Application Constants
 * Organ Donate System
 */

// Base URL
define('BASE_URL', 'http://localhost/Bharath/');

// Site Name
define('SITE_NAME', 'Organ Donate');
define('SITE_DESCRIPTION', 'Smart Organ Donation and Transplant Management System');

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_DONOR', 'donor');
define('ROLE_PATIENT', 'patient');
define('ROLE_HOSPITAL', 'hospital');

// Status Constants
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_REJECTED', 'rejected');
define('STATUS_MATCHED', 'matched');
define('STATUS_COMPLETED', 'completed');

// Blood Groups
$BLOOD_GROUPS = array('O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-');

// Organ Types
$ORGAN_TYPES = array('Heart', 'Lungs', 'Liver', 'Kidney', 'Pancreas', 'Cornea', 'Bone');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Session timeout (in minutes)
define('SESSION_TIMEOUT', 30);

?>