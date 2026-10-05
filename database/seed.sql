-- ============================================================
-- SYNORA STUDENT PORTAL — DATABASE SEED DATA (PRACTICAL 8)
-- Realistic Demonstration Data for CHARUSAT DEPSTAR IT
-- ============================================================

USE `synora`;

-- Clear existing data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `registrations`;
TRUNCATE TABLE `events`;
TRUNCATE TABLE `students`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. Insert Sample Students
-- ------------------------------------------------------------
INSERT INTO `students` (`id`, `roll_no`, `name`, `email`, `mobile`, `course`, `semester`, `year`, `gender`, `division`, `batch`, `created_at`) VALUES
(1, '25DIT006', 'Tanis Bedia', '25dit006@charusat.edu.in', '9876543210', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch A', '2026-07-06 09:00:00'),
(2, '25DIT001', 'Aarav Patel', 'aarav.patel@charusat.edu.in', '9825012345', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch A', '2026-07-06 09:15:00'),
(3, '25DIT012', 'Diya Shah', 'diya.shah@charusat.edu.in', '9898023456', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch B', '2026-07-06 09:30:00'),
(4, '25DIT024', 'Rohan Sharma', 'rohan.sharma@charusat.edu.in', '9724034567', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch B', '2026-07-06 09:45:00'),
(5, '25DIT038', 'Ananya Joshi', 'ananya.joshi@charusat.edu.in', '9909045678', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch C', '2026-07-06 10:00:00');

-- ------------------------------------------------------------
-- 2. Insert Department Academic Events
-- ------------------------------------------------------------
INSERT INTO `events` (`id`, `title`, `description`, `category`, `event_date`, `location`, `organizer`, `capacity`, `created_at`) VALUES
(1, 'Web Development Frameworks Workshop', 'Hands-on practical session covering CSS grid systems, responsive layouts, and modern DOM state management for ITUE203.', 'Workshop', '2026-10-05', 'DEPSTAR Lab 303', 'DEPSTAR IT Department', 60, '2026-09-01 10:00:00'),
(2, 'DEPSTAR Hackathon 2026', 'Annual 24-hour student hackathon solving real-world university workflow and campus logistics challenges with web frameworks.', 'Hackathon', '2026-10-12', 'University Central Auditorium', 'DEPSTAR IT Club', 150, '2026-09-01 10:30:00'),
(3, 'Computer Networks & Cloud Architecture', 'Invited expert seminar on enterprise network routing, subnet masking, and practical infrastructure architectures.', 'Seminar', '2026-10-18', 'Room 222 (Audi-Visual)', 'Prof. Ritesh Patel (RHP)', 80, '2026-09-05 11:00:00'),
(4, 'Data Structures & Algorithms Sprint', 'Competitive algorithmic programming round covering trees, graphs, and dynamic programming in CSUC201.', 'Academic', '2026-10-25', 'DEPSTAR Lab 208', 'Prof. Amit Patel (ARP)', 50, '2026-09-10 12:00:00'),
(5, 'Object-Oriented Java Coding Sprint', 'Rapid coding challenge focusing on polymorphism, exception handling, and collection frameworks in CEUE203.', 'Workshop', '2026-11-02', 'DEPSTAR Lab 204', 'Prof. M. P. Dave (MPD)', 50, '2026-09-15 14:00:00'),
(6, 'CHARUSAT Annual Cultural Showcase', 'University-wide celebration of regional performing arts, music, dance, and creative media competitions.', 'Cultural', '2026-11-15', 'CHARUSAT Sports Ground', 'University Cultural Cell', 500, '2026-09-20 09:00:00');

-- ------------------------------------------------------------
-- 3. Insert Student-Event Registrations
-- ------------------------------------------------------------
INSERT INTO `registrations` (`student_id`, `event_id`, `status`, `registered_at`) VALUES
(1, 1, 'Registered', '2026-09-15 10:30:00'),
(1, 2, 'Registered', '2026-09-16 11:00:00'),
(1, 4, 'Registered', '2026-09-20 14:15:00'),
(2, 1, 'Registered', '2026-09-15 10:45:00'),
(2, 3, 'Registered', '2026-09-18 09:20:00'),
(3, 2, 'Registered', '2026-09-17 15:10:00'),
(3, 5, 'Registered', '2026-09-22 16:00:00'),
(4, 1, 'Waitlisted', '2026-09-23 11:30:00'),
(4, 3, 'Registered', '2026-09-24 10:05:00'),
(5, 2, 'Registered', '2026-09-19 12:40:00'),
(5, 6, 'Registered', '2026-09-25 17:00:00');
