-- Organ Donate Database
CREATE DATABASE IF NOT EXISTS organ_donate;
USE organ_donate;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);

INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$1UIzhivJ9dFfJuw.LliNveeZpIzo43u9rVBKH0BvxKmCx/eKSXSXu');
-- Admin default password is admin123. Run setup.php if you want to recreate the database with the same credentials.

CREATE TABLE donors (
  donor_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  age INT NOT NULL,
  gender VARCHAR(10) NOT NULL,
  blood_group VARCHAR(5) NOT NULL,
  organ_type VARCHAR(50) NOT NULL,
  phone VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  status VARCHAR(20) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE patients (
  patient_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  age INT NOT NULL,
  blood_group VARCHAR(5) NOT NULL,
  required_organ VARCHAR(50) NOT NULL,
  phone VARCHAR(15) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE hospitals (
  hospital_id INT AUTO_INCREMENT PRIMARY KEY,
  hospital_name VARCHAR(150) NOT NULL,
  reg_no VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(15) NOT NULL,
  password VARCHAR(255) NOT NULL,
  status VARCHAR(20) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE organ_requests (
  request_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  organ_type VARCHAR(50) NOT NULL,
  request_date DATE NOT NULL,
  priority_level VARCHAR(20) DEFAULT 'Normal',
  status VARCHAR(20) DEFAULT 'Pending',
  FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

CREATE TABLE matching (
  match_id INT AUTO_INCREMENT PRIMARY KEY,
  donor_id INT NOT NULL,
  patient_id INT NOT NULL,
  organ_type VARCHAR(50) NOT NULL,
  status VARCHAR(20) DEFAULT 'Matched',
  match_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (donor_id) REFERENCES donors(donor_id) ON DELETE CASCADE,
  FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);

CREATE TABLE notifications (
  notif_id INT AUTO_INCREMENT PRIMARY KEY,
  patient_id INT NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE
);
