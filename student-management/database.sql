-- Student Management Database Setup
-- Run this SQL file in your MySQL/phpMyAdmin

CREATE DATABASE IF NOT EXISTS student_management 
    CHARACTER SET utf8 
    COLLATE utf8_general_ci;

USE student_management;

CREATE TABLE IF NOT EXISTS students (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    phone       VARCHAR(15)   NOT NULL,
    dob         DATE          NOT NULL,
    gender      ENUM('Male','Female','Other') NOT NULL,
    course      VARCHAR(100)  NOT NULL,
    department  VARCHAR(100)  NOT NULL,
    year        TINYINT       NOT NULL,
    address     TEXT,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample Data
INSERT INTO students (name, email, phone, dob, gender, course, department, year, address) VALUES
('Arun Kumar',    'arun@example.com',    '9876543210', '2003-05-15', 'Male',   'B.Tech', 'Computer Science', 2, '12, Anna Nagar, Chennai'),
('Priya Devi',    'priya@example.com',   '9876543211', '2004-08-22', 'Female', 'B.Sc',   'Mathematics',      1, '45, RS Puram, Coimbatore'),
('Rahul Raj',     'rahul@example.com',   '9876543212', '2002-11-30', 'Male',   'B.E',    'Electronics',      3, '78, Gandhipuram, Coimbatore'),
('Deepa Sundari', 'deepa@example.com',   '9876543213', '2003-02-18', 'Female', 'B.Tech', 'Information Tech', 2, '23, Saibaba Colony, Coimbatore'),
('Karthik Vel',   'karthik@example.com', '9876543214', '2001-07-09', 'Male',   'M.Tech', 'Computer Science', 1, '56, Peelamedu, Coimbatore');
