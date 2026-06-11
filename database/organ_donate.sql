-- Organ Donate Database Schema
-- MySQL Database for Organ Donation and Transplant Management System

CREATE DATABASE IF NOT EXISTS organ_donate;
USE organ_donate;

-- Admin Table
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donors Table
CREATE TABLE donors (
    donor_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10),
    blood_group VARCHAR(5) NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    medical_history TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Patients Table
CREATE TABLE patients (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(10),
    blood_group VARCHAR(5) NOT NULL,
    required_organ VARCHAR(50) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    medical_condition TEXT,
    urgency_level VARCHAR(20) DEFAULT 'normal',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hospitals Table
CREATE TABLE hospitals (
    hospital_id INT PRIMARY KEY AUTO_INCREMENT,
    hospital_name VARCHAR(100) NOT NULL,
    reg_no VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    status VARCHAR(20) DEFAULT 'pending',
    verification_doc VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Organ Requests Table
CREATE TABLE organ_requests (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    request_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    priority_level VARCHAR(20) DEFAULT 'normal',
    hospital_id INT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id)
);

-- Matching Table
CREATE TABLE matching (
    match_id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT NOT NULL,
    patient_id INT NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    request_id INT NOT NULL,
    match_percentage INT,
    status VARCHAR(20) DEFAULT 'pending',
    matched_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approval_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors(donor_id),
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (request_id) REFERENCES organ_requests(request_id)
);

-- Transplant History Table
CREATE TABLE transplant_history (
    transplant_id INT PRIMARY KEY AUTO_INCREMENT,
    match_id INT NOT NULL,
    donor_id INT NOT NULL,
    patient_id INT NOT NULL,
    hospital_id INT NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    transplant_date TIMESTAMP,
    status VARCHAR(20) DEFAULT 'completed',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (match_id) REFERENCES matching(match_id),
    FOREIGN KEY (donor_id) REFERENCES donors(donor_id),
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id)
);

-- Notifications Table
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    user_type VARCHAR(20),
    title VARCHAR(100),
    message TEXT,
    is_read INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Audit Log Table
CREATE TABLE audit_log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    user_type VARCHAR(20),
    action VARCHAR(100),
    details TEXT,
    ip_address VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create Indexes for better performance
CREATE INDEX idx_donor_blood_group ON donors(blood_group);
CREATE INDEX idx_donor_organ_type ON donors(organ_type);
CREATE INDEX idx_donor_status ON donors(status);
CREATE INDEX idx_patient_blood_group ON patients(blood_group);
CREATE INDEX idx_patient_organ ON patients(required_organ);
CREATE INDEX idx_request_patient ON organ_requests(patient_id);
CREATE INDEX idx_request_status ON organ_requests(status);
CREATE INDEX idx_matching_status ON matching(status);
CREATE INDEX idx_hospital_status ON hospitals(status);
