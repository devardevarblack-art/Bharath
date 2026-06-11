-- Organ Donate and Transparent Management System Database Schema

-- Admin Table
CREATE TABLE Admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Donor Table
CREATE TABLE Donor (
    donor_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    medical_conditions TEXT,
    organ_donation_consent BOOLEAN DEFAULT FALSE,
    organs_to_donate VARCHAR(255),
    donation_status ENUM('Active', 'Inactive', 'Pending') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Patient Table
CREATE TABLE Patient (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    medical_conditions TEXT NOT NULL,
    required_organ VARCHAR(100) NOT NULL,
    urgency_level ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL,
    waiting_since DATE NOT NULL,
    patient_status ENUM('Active', 'Matched', 'Transplanted', 'Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hospital Table
CREATE TABLE Hospital (
    hospital_id INT PRIMARY KEY AUTO_INCREMENT,
    hospital_name VARCHAR(150) NOT NULL,
    registration_number VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    hospital_type ENUM('Government', 'Private', 'NGO') NOT NULL,
    beds_available INT NOT NULL,
    operating_theatres INT NOT NULL,
    approval_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    established_year YEAR,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Doctor Table
CREATE TABLE Doctor (
    doctor_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    hospital_id INT NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    registration_number VARCHAR(50) NOT NULL UNIQUE,
    qualification VARCHAR(255),
    experience_years INT,
    verification_status ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES Hospital(hospital_id)
);

-- Organ Table
CREATE TABLE Organ (
    organ_id INT PRIMARY KEY AUTO_INCREMENT,
    organ_name VARCHAR(50) NOT NULL,
    donor_id INT NOT NULL,
    hospital_id INT NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    collection_date DATETIME NOT NULL,
    expiry_date DATETIME NOT NULL,
    storage_location VARCHAR(100),
    organ_status ENUM('Available', 'Allocated', 'Expired', 'Discarded') DEFAULT 'Available',
    quality_score INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES Donor(donor_id),
    FOREIGN KEY (hospital_id) REFERENCES Hospital(hospital_id)
);

-- Organ_Request Table
CREATE TABLE Organ_Request (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    hospital_id INT NOT NULL,
    doctor_id INT NOT NULL,
    organ_requested VARCHAR(50) NOT NULL,
    request_date DATETIME NOT NULL,
    urgency_level ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL,
    medical_justification TEXT NOT NULL,
    request_status ENUM('Pending', 'Approved', 'Rejected', 'Fulfilled') DEFAULT 'Pending',
    approval_date DATETIME,
    approval_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES Patient(patient_id),
    FOREIGN KEY (hospital_id) REFERENCES Hospital(hospital_id),
    FOREIGN KEY (doctor_id) REFERENCES Doctor(doctor_id),
    FOREIGN KEY (approval_by) REFERENCES Doctor(doctor_id)
);

-- Organ_Match Table
CREATE TABLE Organ_Match (
    match_id INT PRIMARY KEY AUTO_INCREMENT,
    organ_id INT NOT NULL,
    patient_id INT NOT NULL,
    request_id INT NOT NULL,
    match_score INT,
    blood_group_match BOOLEAN,
    tissue_type_match BOOLEAN,
    size_compatibility BOOLEAN,
    distance_km INT,
    travel_time_minutes INT,
    match_status ENUM('Proposed', 'Accepted', 'Rejected', 'Completed') DEFAULT 'Proposed',
    match_date DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organ_id) REFERENCES Organ(organ_id),
    FOREIGN KEY (patient_id) REFERENCES Patient(patient_id),
    FOREIGN KEY (request_id) REFERENCES Organ_Request(request_id)
);

-- Transplant_Record Table
CREATE TABLE Transplant_Record (
    transplant_id INT PRIMARY KEY AUTO_INCREMENT,
    organ_id INT NOT NULL,
    donor_id INT NOT NULL,
    patient_id INT NOT NULL,
    hospital_id INT NOT NULL,
    surgeon_id INT NOT NULL,
    transplant_date DATETIME NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    transplant_status ENUM('Scheduled', 'In-Progress', 'Completed', 'Failed') DEFAULT 'Scheduled',
    surgery_duration_minutes INT,
    post_transplant_notes TEXT,
    patient_outcome ENUM('Successful', 'Complications', 'Failed') DEFAULT 'Successful',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (organ_id) REFERENCES Organ(organ_id),
    FOREIGN KEY (donor_id) REFERENCES Donor(donor_id),
    FOREIGN KEY (patient_id) REFERENCES Patient(patient_id),
    FOREIGN KEY (hospital_id) REFERENCES Hospital(hospital_id),
    FOREIGN KEY (surgeon_id) REFERENCES Doctor(doctor_id)
);

-- Notification Table
CREATE TABLE Notification (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_type ENUM('Donor', 'Patient', 'Hospital', 'Doctor', 'Admin') NOT NULL,
    recipient_id INT NOT NULL,
    notification_type ENUM('Match', 'Approval', 'Rejection', 'Emergency', 'Status') NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    email_sent BOOLEAN DEFAULT FALSE,
    sms_sent BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reports Table
CREATE TABLE Reports (
    report_id INT PRIMARY KEY AUTO_INCREMENT,
    report_type VARCHAR(100) NOT NULL,
    report_title VARCHAR(200) NOT NULL,
    generated_by INT,
    total_donors INT DEFAULT 0,
    total_patients INT DEFAULT 0,
    available_organs INT DEFAULT 0,
    pending_requests INT DEFAULT 0,
    successful_transplants INT DEFAULT 0,
    report_period_from DATE,
    report_period_to DATE,
    report_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES Admin(admin_id)
);

-- Create Indexes for better performance
CREATE INDEX idx_donor_email ON Donor(email);
CREATE INDEX idx_donor_blood_group ON Donor(blood_group);
CREATE INDEX idx_patient_email ON Patient(email);
CREATE INDEX idx_patient_blood_group ON Patient(blood_group);
CREATE INDEX idx_patient_required_organ ON Patient(required_organ);
CREATE INDEX idx_hospital_city ON Hospital(city);
CREATE INDEX idx_doctor_hospital ON Doctor(hospital_id);
CREATE INDEX idx_doctor_email ON Doctor(email);
CREATE INDEX idx_organ_donor ON Organ(donor_id);
CREATE INDEX idx_organ_hospital ON Organ(hospital_id);
CREATE INDEX idx_organ_status ON Organ(organ_status);
CREATE INDEX idx_request_patient ON Organ_Request(patient_id);
CREATE INDEX idx_request_status ON Organ_Request(request_status);
CREATE INDEX idx_match_organ ON Organ_Match(organ_id);
CREATE INDEX idx_match_patient ON Organ_Match(patient_id);
CREATE INDEX idx_transplant_organ ON Transplant_Record(organ_id);
CREATE INDEX idx_transplant_patient ON Transplant_Record(patient_id);
