-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 29, 2025 at 03:27 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wium_lie_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

-- Drop tables to avoid conflicts
DROP TABLE IF EXISTS `prescriptiondetails`;
DROP TABLE IF EXISTS `prescription`;
DROP TABLE IF EXISTS `medicalrecord`;
DROP TABLE IF EXISTS `drug`;
DROP TABLE IF EXISTS `pharmacy`;
DROP TABLE IF EXISTS `patient`;
DROP TABLE IF EXISTS `doctor`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `role` enum('DOCTOR','PATIENT','PHARMACY','ADMIN') NOT NULL,
  `email` varchar(100) NOT NULL,
  `contactno` varchar(20) DEFAULT NULL,
  `pass_hash` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `contactno` (`contactno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` VALUES
(1, 'Gud', 'Fyll', 'DOCTOR', 'feelgood@gmail.com', '0918514237', '111', 'Goodville', '2025-10-25 03:06:32'),
(2, 'Doe', 'Jane', 'PATIENT', 'jane.doe@example.com', '0918123456', '222', 'Health Street', '2025-10-25 03:06:32'),
(3, 'Smith', 'John', 'PHARMACY', 'john.smith@pharma.com', '0918111222', '333', 'Pharma Town', '2025-10-25 03:06:32'),
(4, 'Adams', 'Grace', 'ADMIN', 'grace.adams@sys.com', '0918999888', '444', 'Admin City', '2025-10-25 03:06:32'),
(5, 'White', 'Ella', 'PATIENT', 'ella.white@example.com', '0918777666', '555', 'Wellness Village', '2025-10-25 03:06:32'),
(6, 'Strong', 'Jack', 'PATIENT', 'jack.strong@example.com', '0918123123', 'hashedpassword006', 'Resilient Avenue', '2025-10-25 03:13:46');

-- --------------------------------------------------------
-- Table: admin
-- --------------------------------------------------------
CREATE TABLE `admin` (
  `user_id` int NOT NULL,
  `isAdmin` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` VALUES (4,1);

-- --------------------------------------------------------
-- Table: patient
-- --------------------------------------------------------
CREATE TABLE `patient` (
  `user_id` int NOT NULL,
  `birth_date` date NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `patient` VALUES
(2, '1995-06-21'),
(5, '1999-03-10'),
(6, '1990-09-20');

-- --------------------------------------------------------
-- Table: doctor
-- --------------------------------------------------------
CREATE TABLE `doctor` (
  `user_id` int NOT NULL,
  `birth_date` date NOT NULL,
  `specialization` varchar(100),
  `prc_license` varchar(100),
  `clinic_name` varchar(100),
  `isVerified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `prc_license` (`prc_license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `doctor` VALUES
(1, '1988-10-11', 'General Medicine', 'PRC-0001', 'FeelGood Clinic', 1);

-- --------------------------------------------------------
-- Table: pharmacy
-- --------------------------------------------------------
CREATE TABLE `pharmacy` (
  `user_id` int NOT NULL,
  `pharmacy_name` varchar(100),
  `phar_license` varchar(100),
  `open_time` time,
  `close_time` time,
  `dates_open` varchar(50),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pharmacy` VALUES
(3, 'HealthPlus Pharmacy', 'PHR-00321', '08:00:00', '20:00:00', 'Mon–Sun');

-- --------------------------------------------------------
-- Table: drug
-- --------------------------------------------------------
CREATE TABLE `drug` (
  `drug_id` int NOT NULL AUTO_INCREMENT,
  `generic_name` varchar(100) NOT NULL,
  `brand` varchar(100),
  `chemical_name` varchar(100),
  `category` varchar(100),
  `expiry_date` date,
  `isControlled` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`drug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `drug` (`generic_name`,`brand`,`chemical_name`,`category`,`expiry_date`,`isControlled`) VALUES
('Paracetamol','Biogesic','Acetaminophen','Analgesic/Antipyretic','2026-05-01',0),
('Ibuprofen','Advil','Ibuprofen','NSAID','2026-11-10',0),
('Amoxicillin','Amoxil','Amoxicillin Trihydrate','Antibiotic','2027-02-15',0),
('Cetirizine','Allerkid','Cetirizine Hydrochloride','Antihistamine','2026-08-20',0),
('Metformin','Glucophage','Metformin Hydrochloride','Antidiabetic','2027-01-01',0),
('Losartan','Cozaar','Losartan Potassium','Antihypertensive','2027-06-30',0),
('Omeprazole','Losec','Omeprazole','PPI','2026-09-14',0),
('Azithromycin','Zithromax','Azithromycin','Antibiotic','2026-10-05',0),
('Tramadol','Tramal','Tramadol Hydrochloride','Opioid Analgesic','2027-04-22',1),
('Hydroxyzine','Atarax','Hydroxyzine Hydrochloride','Anxiolytic','2026-12-11',0);

-- --------------------------------------------------------
-- Table: medicalrecord
-- --------------------------------------------------------
CREATE TABLE `medicalrecord` (
  `record_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `height` varchar(20),
  `weight` varchar(20),
  `allergies` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`record_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `medicalrecord` VALUES
(1, 6, '180 cm', '75 kg', 'None', '2025-10-28 12:37:23', '2025-10-28 12:37:23');

-- --------------------------------------------------------
-- Table: prescription
-- --------------------------------------------------------
CREATE TABLE `prescription` (
  `prescription_id` int NOT NULL AUTO_INCREMENT,
  `prescribing_doctor` int NOT NULL,
  `record_id` int NOT NULL,
  `prescription_date` date NOT NULL,
  `status` varchar(50),
  PRIMARY KEY (`prescription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DELIMITER $$
CREATE TRIGGER `prescription_before_insert`
BEFORE INSERT ON `prescription`
FOR EACH ROW
BEGIN
  IF NEW.prescription_date IS NULL OR NEW.prescription_date = '0000-00-00' THEN
    SET NEW.prescription_date = CURDATE();
  END IF;
END$$
DELIMITER ;

INSERT INTO `prescription` (`prescribing_doctor`,`record_id`,`status`) VALUES
(1,1,'ACTIVE'),
(1,1,'COMPLETED');

-- --------------------------------------------------------
-- Table: prescriptiondetails
-- --------------------------------------------------------
CREATE TABLE `prescriptiondetails` (
  `prescription_id` int NOT NULL,
  `drug_id` int NOT NULL,
  `duration` varchar(50),
  `dosage` varchar(50),
  `frequency` varchar(50),
  `special_instructions` text,
  `refills` int DEFAULT 0,
  PRIMARY KEY (`prescription_id`,`drug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `prescriptiondetails` VALUES
(1,1,'5 days','500 mg','Every 6 hours','Take after meals', 0, NULL),
(1,3,'3 days','250 mg','Every 8 hours','Complete full dosage', 0, NULL),
(2,2,'7 days','200 mg','Twice a day','Avoid alcohol', 0, NULL),
(2,4,'As needed','10 mg','Once a day','Do not exceed 1 tablet per 24 hours', 0, NULL);

-- --------------------------------------------------------
-- Foreign Keys
-- --------------------------------------------------------

ALTER TABLE `admin` ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `patient` ADD CONSTRAINT `fk_patient_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `doctor` ADD CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `pharmacy` ADD CONSTRAINT `fk_pharmacy_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `medicalrecord` ADD CONSTRAINT `fk_medicalrecord_patient` FOREIGN KEY (`user_id`) REFERENCES `patient` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `prescription` ADD CONSTRAINT `fk_prescription_doctor` FOREIGN KEY (`prescribing_doctor`) REFERENCES `doctor` (`user_id`) ON DELETE CASCADE;
ALTER TABLE `prescription` ADD CONSTRAINT `fk_prescription_record` FOREIGN KEY (`record_id`) REFERENCES `medicalrecord` (`record_id`) ON DELETE CASCADE;
ALTER TABLE `prescriptiondetails` ADD CONSTRAINT `fk_details_drug` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`drug_id`) ON DELETE CASCADE;
ALTER TABLE `prescriptiondetails` ADD CONSTRAINT `fk_details_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescription` (`prescription_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
