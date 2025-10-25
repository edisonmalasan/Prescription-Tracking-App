-- Prescription Tracking App Database Schema
-- Created for Webdev Midterms Activity

-- Create database
CREATE DATABASE IF NOT EXISTS prescription_tracking;
USE prescription_tracking;

-- USERS table (base table for all user types)
CREATE TABLE USERS (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    last_name VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    role ENUM('doctor', 'patient', 'pharmacy', 'admin') NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    contactno VARCHAR(20),
    pass_hash VARCHAR(255) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- DOCTOR table
CREATE TABLE DOCTOR (
    user_id INT PRIMARY KEY,
    birth_date DATE,
    specialization VARCHAR(100),
    prc_license VARCHAR(50) UNIQUE NOT NULL,
    dea_number VARCHAR(50) UNIQUE NOT NULL,
    clinic_name VARCHAR(200),
    isVerified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE
);

-- PATIENT table
CREATE TABLE PATIENT (
    user_id INT PRIMARY KEY,
    birth_date DATE,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE
);

-- PHARMACY table
CREATE TABLE PHARMACY (
    user_id INT PRIMARY KEY,
    pharmacy_name VARCHAR(200) UNIQUE NOT NULL,
    phar_license VARCHAR(50) UNIQUE NOT NULL,
    operating_hours VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE
);

-- ADMIN table
CREATE TABLE ADMIN (
    user_id INT PRIMARY KEY,
    isAdmin BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE
);

-- MEDICALRECORD table
CREATE TABLE MEDICALRECORD (
    record_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    height DECIMAL(5,2),
    weight DECIMAL(5,2),
    allergies TEXT,
    blood_type VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES PATIENT(user_id) ON DELETE CASCADE
);

-- DRUG table
CREATE TABLE DRUG (
    drug_id INT PRIMARY KEY AUTO_INCREMENT,
    generic_name VARCHAR(200) UNIQUE NOT NULL,
    brand VARCHAR(200),
    chemical_name VARCHAR(300),
    category VARCHAR(100),
    expiry_date DATE,
    isControlled BOOLEAN DEFAULT FALSE,
    controlled_schedule VARCHAR(10),
    dosage_form VARCHAR(50),
    strength VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- PRESCRIPTION table
CREATE TABLE PRESCRIPTION (
    prescription_id INT PRIMARY KEY AUTO_INCREMENT,
    prescribing_doctor INT NOT NULL,
    record_id INT NOT NULL,
    prescription_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'cancelled', 'pending') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (prescribing_doctor) REFERENCES DOCTOR(user_id) ON DELETE CASCADE,
    FOREIGN KEY (record_id) REFERENCES MEDICALRECORD(record_id) ON DELETE CASCADE
);

-- PRESCRIPTIONDETAILS table
CREATE TABLE PRESCRIPTIONDETAILS (
    detail_id INT PRIMARY KEY AUTO_INCREMENT,
    prescription_id INT NOT NULL,
    drug_id INT NOT NULL,
    duration INT NOT NULL, -- in days
    dosage VARCHAR(100) NOT NULL,
    frequency VARCHAR(100) NOT NULL,
    route VARCHAR(50),
    refills INT DEFAULT 0,
    instructions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prescription_id) REFERENCES PRESCRIPTION(prescription_id) ON DELETE CASCADE,
    FOREIGN KEY (drug_id) REFERENCES DRUG(drug_id) ON DELETE CASCADE
);

-- NOTIFICATIONS table
CREATE TABLE NOTIFICATIONS (
    notification_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('prescription_created', 'prescription_updated', 'prescription_cancelled', 'missed_dose', 'general') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USERS(user_id) ON DELETE CASCADE
);

-- SALES table
CREATE TABLE SALES (
    sale_id INT PRIMARY KEY AUTO_INCREMENT,
    pharmacy_id INT NOT NULL,
    prescription_id INT NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    amount DECIMAL(10,2),
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (pharmacy_id) REFERENCES PHARMACY(user_id) ON DELETE CASCADE,
    FOREIGN KEY (prescription_id) REFERENCES PRESCRIPTION(prescription_id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON USERS(email);
CREATE INDEX idx_users_role ON USERS(role);
CREATE INDEX idx_doctor_license ON DOCTOR(prc_license);
CREATE INDEX idx_doctor_dea ON DOCTOR(dea_number);
CREATE INDEX idx_pharmacy_name ON PHARMACY(pharmacy_name);
CREATE INDEX idx_drug_generic ON DRUG(generic_name);
CREATE INDEX idx_prescription_doctor ON PRESCRIPTION(prescribing_doctor);
CREATE INDEX idx_prescription_record ON PRESCRIPTION(record_id);
CREATE INDEX idx_prescription_status ON PRESCRIPTION(status);
CREATE INDEX idx_notifications_user ON NOTIFICATIONS(user_id);
CREATE INDEX idx_notifications_read ON NOTIFICATIONS(is_read);
CREATE INDEX idx_sales_pharmacy ON SALES(pharmacy_id);
CREATE INDEX idx_sales_date ON SALES(sale_date);

-- Insert sample data for testing
INSERT INTO USERS (last_name, first_name, role, email, contactno, pass_hash, address) VALUES
('Admin', 'System', 'admin', 'admin@prescription.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Address'),
('Smith', 'John', 'doctor', 'john.smith@doctor.com', '1234567891', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Medical St'),
('Doe', 'Jane', 'patient', 'jane.doe@patient.com', '1234567892', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Patient Ave'),
('Johnson', 'MedCenter', 'pharmacy', 'medcenter@pharmacy.com', '1234567893', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '789 Pharmacy Blvd');

INSERT INTO DOCTOR (user_id, birth_date, specialization, prc_license, dea_number, clinic_name, isVerified) VALUES
(2, '1980-05-15', 'Internal Medicine', 'PRC123456', 'DEA123456', 'City Medical Center', TRUE);

INSERT INTO PATIENT (user_id, birth_date) VALUES
(3, '1990-08-20');

INSERT INTO PHARMACY (user_id, pharmacy_name, phar_license, operating_hours) VALUES
(4, 'City Medical Pharmacy', 'PHAR123456', '8:00 AM - 10:00 PM');

INSERT INTO ADMIN (user_id, isAdmin) VALUES
(1, TRUE);

INSERT INTO MEDICALRECORD (user_id, height, weight, allergies, blood_type) VALUES
(3, 165.5, 60.0, 'Penicillin, Shellfish', 'O+');

INSERT INTO DRUG (generic_name, brand, chemical_name, category, expiry_date, isControlled, controlled_schedule, dosage_form, strength) VALUES
('Paracetamol', 'Tylenol', 'Acetaminophen', 'Analgesic', '2025-12-31', FALSE, NULL, 'Tablet', '500mg'),
('Amoxicillin', 'Amoxil', 'Amoxicillin Trihydrate', 'Antibiotic', '2024-12-31', FALSE, NULL, 'Capsule', '500mg'),
('Morphine', 'MS Contin', 'Morphine Sulfate', 'Opioid', '2024-06-30', TRUE, 'II', 'Tablet', '15mg');
