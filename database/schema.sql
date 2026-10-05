-- ============================================================
-- SYNORA STUDENT PORTAL — DATABASE SCHEMA (PRACTICAL 8)
-- Academic Context: CHARUSAT / DEPSTAR Information Technology
-- B.Tech IT — Semester 3 (Academic Year 2026–27)
-- ============================================================

-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS `synora` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `synora`;

-- Disable foreign key checks during schema creation
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. Table: students
-- Entity representing registered B.Tech IT department students
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `students`;

CREATE TABLE `students` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `roll_no` VARCHAR(20) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(120) NOT NULL,
  `mobile` VARCHAR(15) NOT NULL,
  `course` VARCHAR(60) NOT NULL DEFAULT 'B.Tech IT',
  `semester` TINYINT UNSIGNED NOT NULL DEFAULT 3,
  `year` TINYINT UNSIGNED NOT NULL DEFAULT 2,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `division` VARCHAR(10) NOT NULL DEFAULT '3IT-A',
  `batch` VARCHAR(10) NOT NULL DEFAULT 'Batch A',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_students_roll_no` (`roll_no`),
  UNIQUE KEY `idx_students_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. Table: events
-- Entity representing campus hackathons, workshops & seminars
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `events`;

CREATE TABLE `events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `event_date` DATE NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `organizer` VARCHAR(100) NOT NULL DEFAULT 'DEPSTAR IT Department',
  `capacity` INT UNSIGNED NOT NULL DEFAULT 100,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_events_date` (`event_date`),
  KEY `idx_events_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. Table: registrations
-- Associative entity linking students to events (M:N relationship)
-- ------------------------------------------------------------
CREATE TABLE `registrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `event_id` INT UNSIGNED NOT NULL,
  `status` ENUM('Registered', 'Waitlisted', 'Cancelled', 'Attended') NOT NULL DEFAULT 'Registered',
  `registered_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_event` (`student_id`, `event_id`),
  KEY `idx_registrations_student` (`student_id`),
  KEY `idx_registrations_event` (`event_id`),
  CONSTRAINT `fk_registrations_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_registrations_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
