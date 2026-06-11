-- Organ Donate Database Schema
-- Created: 2026

CREATE DATABASE IF NOT EXISTS organ_donate_db;
USE organ_donate_db;

-- Admin Table
CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donors Table
CREATE TABLE donors (
    donor_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    gender VARCHAR(20) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    hospital_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Patients Table
CREATE TABLE patients (
    patient_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    required_organ VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hospitals Table
CREATE TABLE hospitals (
    hospital_id INT PRIMARY KEY AUTO_INCREMENT,
    hospital_name VARCHAR(100) NOT NULL,
    reg_no VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' COMMENT 'pending, approved, rejected',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Organ Requests Table
CREATE TABLE organ_requests (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    request_date DATE NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' COMMENT 'pending, matched, completed, cancelled',
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id)
);

-- Matching Table
CREATE TABLE matching (
    match_id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT NOT NULL,
    patient_id INT NOT NULL,
    organ_type VARCHAR(50) NOT NULL,
    hospital_id INT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending' COMMENT 'pending, matched, approved, completed, rejected',
    match_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (donor_id) REFERENCES donors(donor_id),
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id)
);

-- Notifications Table
CREATE TABLE notifications (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$YflB8A9/FxDcNV/4IbFKO.Y.x1W8KTIo9P5U0m5YjY5dQqP5N1Dua');
-- Password: admin@123

-- Create Indexes
CREATE INDEX idx_donor_status ON donors(status);
CREATE INDEX idx_hospital_status ON hospitals(status);
CREATE INDEX idx_organ_requests_patient ON organ_requests(patient_id);
CREATE INDEX idx_matching_donor ON matching(donor_id);
CREATE INDEX idx_matching_patient ON matching(patient_id);
CREATE INDEX idx_notifications_user ON notifications(user_id);