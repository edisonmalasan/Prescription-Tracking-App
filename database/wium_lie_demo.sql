-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 15, 2025 at 03:01 AM
-- Server version: 8.4.3
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

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `user_id` int NOT NULL,
  `isAdmin` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`user_id`, `isAdmin`) VALUES
(4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `user_id` int NOT NULL,
  `birth_date` date NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `prc_license` varchar(100) DEFAULT NULL,
  `clinic_name` varchar(100) DEFAULT NULL,
  `isVerified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `prc_license` (`prc_license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`user_id`, `birth_date`, `specialization`, `prc_license`, `clinic_name`, `isVerified`) VALUES
(1, '1988-10-11', 'General Medicine', 'PRC-0001', 'FeelGood Clinic', 1);

-- --------------------------------------------------------

--
-- Table structure for table `drug`
--

DROP TABLE IF EXISTS `drug`;
CREATE TABLE IF NOT EXISTS `drug` (
  `drug_id` int NOT NULL AUTO_INCREMENT,
  `generic_name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `chemical_name` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `isControlled` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`drug_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `drug`
--

INSERT INTO `drug` (`drug_id`, `generic_name`, `brand`, `chemical_name`, `category`, `isControlled`, `created_at`) VALUES
(1, 'Paracetamol', 'Biogesic', 'Acetaminophen', 'Analgesic/Antipyretic', 0, '2025-11-15 02:51:10'),
(2, 'Ibuprofen', 'Advil', 'Ibuprofen', 'NSAID', 0, '2025-11-15 02:51:10'),
(3, 'Amoxicillin', 'Amoxil', 'Amoxicillin Trihydrate', 'Antibiotic', 0, '2025-11-15 02:51:10'),
(4, 'Cetirizine', 'Allerkid', 'Cetirizine Hydrochloride', 'Antihistamine', 0, '2025-11-15 02:51:10'),
(5, 'Metformin', 'Glucophage', 'Metformin Hydrochloride', 'Antidiabetic', 0, '2025-11-15 02:51:10'),
(6, 'Losartan', 'Cozaar', 'Losartan Potassium', 'Antihypertensive', 0, '2025-11-15 02:51:10'),
(7, 'Omeprazole', 'Losec', 'Omeprazole', 'PPI', 0, '2025-11-15 02:51:10'),
(8, 'Azithromycin', 'Zithromax', 'Azithromycin', 'Antibiotic', 0, '2025-11-15 02:51:10'),
(9, 'Tramadol', 'Tramal', 'Tramadol Hydrochloride', 'Opioid Analgesic', 1, '2025-11-15 02:51:10'),
(10, 'Hydroxyzine', 'Atarax', 'Hydroxyzine Hydrochloride', 'Anxiolytic', 0, '2025-11-15 02:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `medicalrecord`
--

DROP TABLE IF EXISTS `medicalrecord`;
CREATE TABLE IF NOT EXISTS `medicalrecord` (
  `record_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `height` varchar(20) DEFAULT NULL,
  `weight` varchar(20) DEFAULT NULL,
  `allergies` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`record_id`),
  KEY `fk_medicalrecord_patient` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `medicalrecord`
--

INSERT INTO `medicalrecord` (`record_id`, `user_id`, `height`, `weight`, `allergies`, `created_at`, `updated_at`) VALUES
(1, 6, '180 cm', '75 kg', 'None', '2025-10-28 12:37:23', '2025-10-28 12:37:23');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `user_id` int NOT NULL,
  `birth_date` date NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`user_id`, `birth_date`) VALUES
(2, '1995-06-21'),
(5, '1999-03-10'),
(6, '1990-09-20');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy`
--

DROP TABLE IF EXISTS `pharmacy`;
CREATE TABLE IF NOT EXISTS `pharmacy` (
  `user_id` int NOT NULL,
  `pharmacy_name` varchar(100) DEFAULT NULL,
  `phar_license` varchar(100) DEFAULT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `dates_open` varchar(50) DEFAULT NULL,
  `isVerified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pharmacy`
--

INSERT INTO `pharmacy` (`user_id`, `pharmacy_name`, `phar_license`, `open_time`, `close_time`, `dates_open`, `isVerified`) VALUES
(3, 'HealthPlus Pharmacy', 'PHR-00321', '08:00:00', '20:00:00', 'Mon–Sun', 1);

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

DROP TABLE IF EXISTS `prescription`;
CREATE TABLE IF NOT EXISTS `prescription` (
  `prescription_id` int NOT NULL AUTO_INCREMENT,
  `prescribing_doctor` int NOT NULL,
  `record_id` int NOT NULL,
  `prescription_date` date NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`prescription_id`),
  KEY `fk_prescription_doctor` (`prescribing_doctor`),
  KEY `fk_prescription_record` (`record_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`prescription_id`, `prescribing_doctor`, `record_id`, `prescription_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-15', 'ACTIVE', '2025-11-15 02:56:40', '2025-11-15 02:57:50'),
(2, 1, 1, '2025-11-15', 'COMPLETED', '2025-11-15 02:56:40', '2025-11-15 02:57:50');

--
-- Triggers `prescription`
--
DROP TRIGGER IF EXISTS `prescription_before_insert`;
DELIMITER $$
CREATE TRIGGER `prescription_before_insert` BEFORE INSERT ON `prescription` FOR EACH ROW BEGIN
  IF NEW.prescription_date IS NULL OR NEW.prescription_date = '0000-00-00' THEN
    SET NEW.prescription_date = CURDATE();
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptiondetails`
--

DROP TABLE IF EXISTS `prescriptiondetails`;
CREATE TABLE IF NOT EXISTS `prescriptiondetails` (
  `prescription_id` int NOT NULL,
  `drug_id` int NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL,
  `special_instructions` text,
  `refills` int DEFAULT '0',
  PRIMARY KEY (`prescription_id`,`drug_id`),
  KEY `fk_details_drug` (`drug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `prescriptiondetails`
--

INSERT INTO `prescriptiondetails` (`prescription_id`, `drug_id`, `duration`, `dosage`, `frequency`, `special_instructions`, `refills`) VALUES
(1, 1, '5 days', '500 mg', 'Every 6 hours', 'Take after meals', 0),
(1, 3, '3 days', '250 mg', 'Every 8 hours', 'Complete full dosage', 0),
(2, 2, '7 days', '200 mg', 'Twice a day', 'Avoid alcohol', 0),
(2, 4, 'As needed', '10 mg', 'Once a day', 'Do not exceed 1 tablet per 24 hours', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `last_name`, `first_name`, `role`, `email`, `contactno`, `pass_hash`, `address`, `created_at`) VALUES
(1, 'Gud', 'Fyll', 'DOCTOR', 'feelgood@gmail.com', '0918514237', '111', 'Goodville', '2025-10-25 03:06:32'),
(2, 'Doe', 'Jane', 'PATIENT', 'jane.doe@example.com', '0918123456', '222', 'Health Street', '2025-10-25 03:06:32'),
(3, 'Smith', 'John', 'PHARMACY', 'john.smith@pharma.com', '0918111222', '333', 'Pharma Town', '2025-10-25 03:06:32'),
(4, 'Adams', 'Grace', 'ADMIN', 'grace.adams@sys.com', '0918999888', '444', 'Admin City', '2025-10-25 03:06:32'),
(5, 'White', 'Ella', 'PATIENT', 'ella.white@example.com', '0918777666', '555', 'Wellness Village', '2025-10-25 03:06:32'),
(6, 'Strong', 'Jack', 'PATIENT', 'jack.strong@example.com', '0918123123', 'hashedpassword006', 'Resilient Avenue', '2025-10-25 03:13:46');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `medicalrecord`
--
ALTER TABLE `medicalrecord`
  ADD CONSTRAINT `fk_medicalrecord_patient` FOREIGN KEY (`user_id`) REFERENCES `patient` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `fk_patient_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy`
--
ALTER TABLE `pharmacy`
  ADD CONSTRAINT `fk_pharmacy_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `fk_prescription_doctor` FOREIGN KEY (`prescribing_doctor`) REFERENCES `doctor` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prescription_record` FOREIGN KEY (`record_id`) REFERENCES `medicalrecord` (`record_id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptiondetails`
--
ALTER TABLE `prescriptiondetails`
  ADD CONSTRAINT `fk_details_drug` FOREIGN KEY (`drug_id`) REFERENCES `drug` (`drug_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_details_prescription` FOREIGN KEY (`prescription_id`) REFERENCES `prescription` (`prescription_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
