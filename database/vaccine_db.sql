-- ============================================================================
-- Database Schema: Vaccine Management System (Hospital & Appointments Module)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `vaccine_management_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `vaccine_management_db`;

-- 1. Hospitals Table
CREATE TABLE IF NOT EXISTS `hospitals` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `hospital_name` VARCHAR(150) NOT NULL,
  `license_number` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(120) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `emergency_phone` VARCHAR(30) NULL,
  `address` TEXT NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `timings` VARCHAR(100) DEFAULT 'Mon - Sat: 08:30 AM - 04:30 PM',
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Vaccines Catalog
CREATE TABLE IF NOT EXISTS `vaccines` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `vaccine_name` VARCHAR(100) NOT NULL,
  `disease` VARCHAR(150) NOT NULL,
  `eligible_age` VARCHAR(100) NOT NULL,
  `dosage_route` VARCHAR(100) NOT NULL,
  `stock_doses` INT UNSIGNED DEFAULT 0,
  `status` ENUM('available', 'unavailable') DEFAULT 'available',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Children (Infants registered by parents)
CREATE TABLE IF NOT EXISTS `children` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `parent_name` VARCHAR(100) NOT NULL,
  `parent_phone` VARCHAR(30) NOT NULL,
  `child_name` VARCHAR(100) NOT NULL,
  `dob` DATE NOT NULL,
  `gender` ENUM('male', 'female', 'other') NOT NULL,
  `blood_group` VARCHAR(10) NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Appointments & Bookings
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_code` VARCHAR(20) NOT NULL UNIQUE,
  `child_id` INT UNSIGNED NOT NULL,
  `hospital_id` INT UNSIGNED NOT NULL,
  `vaccine_id` INT UNSIGNED NOT NULL,
  `appointment_date` DATE NOT NULL,
  `time_slot` VARCHAR(30) NOT NULL,
  `admin_status` ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
  `vaccine_status` ENUM('pending', 'vaccinated', 'not_vaccinated') DEFAULT 'pending',
  `batch_number` VARCHAR(50) NULL,
  `admin_staff` VARCHAR(100) NULL,
  `admin_notes` TEXT NULL,
  `reason_not_vaccinated` TEXT NULL,
  `administered_at` DATETIME NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`child_id`) REFERENCES `children`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`hospital_id`) REFERENCES `hospitals`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`vaccine_id`) REFERENCES `vaccines`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Sample Data
INSERT INTO `hospitals` (`id`, `hospital_name`, `license_number`, `email`, `password_hash`, `phone`, `address`, `city`) VALUES
(1, 'City Pediatric Hospital', 'HOSP-PK-9421', 'pediatrics@cityhospital.gov.pk', '$2y$10$w8t3c1Jm0c2Wc2w0L9k4..X3xS8pD2q.mG2mK4rT9uF8s1q7a0b3C', '+92 21 34567890', 'Sector 11-A, North District', 'Karachi');

INSERT INTO `vaccines` (`id`, `vaccine_name`, `disease`, `eligible_age`, `dosage_route`, `stock_doses`, `status`) VALUES
(1, 'Oral Polio Vaccine (OPV)', 'Poliomyelitis', 'At Birth, 6, 10, 14 Weeks', 'Oral Drops', 480, 'available'),
(2, 'BCG Vaccine', 'Tuberculosis', 'At Birth', 'Intradermal', 320, 'available'),
(3, 'Hepatitis B (HepB)', 'Hepatitis B', 'Within 24 Hours of Birth', 'Intramuscular', 250, 'available'),
(4, 'Pentavalent (DTP-HepB-Hib)', 'Diphtheria, Tetanus, Pertussis', '6, 10, 14 Weeks', 'Intramuscular', 310, 'available'),
(5, 'Measles-Rubella (MR)', 'Measles, Rubella', '9 Months & 15 Months', 'Subcutaneous', 60, 'available');

INSERT INTO `children` (`id`, `parent_name`, `parent_phone`, `child_name`, `dob`, `gender`) VALUES
(1, 'Ahmed Ali', '0300-1234567', 'Hamza Ali', '2026-07-24', 'male'),
(2, 'Farhan Khan', '0321-9876543', 'Zoya Khan', '2026-09-02', 'female'),
(3, 'Usman Raza', '0333-5551234', 'Bilal Raza', '2025-12-05', 'male');

INSERT INTO `appointments` (`id`, `booking_code`, `child_id`, `hospital_id`, `vaccine_id`, `appointment_date`, `time_slot`, `vaccine_status`) VALUES
(1, 'BK-201', 1, 1, 1, CURDATE(), '10:00 AM', 'pending'),
(2, 'BK-202', 2, 1, 2, CURDATE(), '10:45 AM', 'pending'),
(3, 'BK-198', 3, 1, 5, CURDATE(), '09:15 AM', 'vaccinated');
