-- ============================================================
-- SYNORA STUDENT PORTAL — SEED DATA (PRACTICALS 8 - 14)
-- Academic Context: CHARUSAT / DEPSTAR Information Technology
-- B.Tech IT — Semester 3 (Academic Year 2026–27)
-- ============================================================

USE `synora`;

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- 1. Seed Users (Bcrypt Password Hashes)
-- Default Admin: admin@charusat.edu.in / Admin@2026
-- Default Student: 25dit006@charusat.edu.in / Student@2026
-- ------------------------------------------------------------
TRUNCATE TABLE `audit_logs`;
TRUNCATE TABLE `registrations`;
TRUNCATE TABLE `students`;
TRUNCATE TABLE `events`;
TRUNCATE TABLE `feedback`;
TRUNCATE TABLE `users`;

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `full_name`, `status`, `created_at`) VALUES
(1, 'admin', 'admin@charusat.edu.in', '$2y$10$cfArHyqMhMcR..pJmoCSkeXEwqKVGKW5afErKsuzwC6KMMVrq37YW', 'admin', 'Dr. Parth Goel (HOD IT)', 'active', '2026-08-01 09:00:00'),
(2, 'coordinator', 'coordinator@charusat.edu.in', '$2y$10$cfArHyqMhMcR..pJmoCSkeXEwqKVGKW5afErKsuzwC6KMMVrq37YW', 'admin', 'Prof. M. P. Dave (WDF Coordinator)', 'active', '2026-08-01 09:30:00'),
(3, '25DIT006', '25dit006@charusat.edu.in', '$2y$10$vNIOpnvF/5YuQpeKe8IK6.SZDDXNroEd0yZtL1AT6cuZ4pjD3tKNO', 'student', 'Tanis Bedia', 'active', '2026-08-15 10:00:00'),
(4, '25DIT001', '25dit001@charusat.edu.in', '$2y$10$vNIOpnvF/5YuQpeKe8IK6.SZDDXNroEd0yZtL1AT6cuZ4pjD3tKNO', 'student', 'Aarav Patel', 'active', '2026-08-15 10:15:00'),
(5, '25DIT012', '25dit012@charusat.edu.in', '$2y$10$vNIOpnvF/5YuQpeKe8IK6.SZDDXNroEd0yZtL1AT6cuZ4pjD3tKNO', 'student', 'Diya Shah', 'active', '2026-08-15 10:30:00'),
(6, '25DIT024', '25dit024@charusat.edu.in', '$2y$10$vNIOpnvF/5YuQpeKe8IK6.SZDDXNroEd0yZtL1AT6cuZ4pjD3tKNO', 'student', 'Rohan Sharma', 'active', '2026-08-15 10:45:00'),
(7, '25DIT038', '25dit038@charusat.edu.in', '$2y$10$vNIOpnvF/5YuQpeKe8IK6.SZDDXNroEd0yZtL1AT6cuZ4pjD3tKNO', 'student', 'Ananya Joshi', 'active', '2026-08-15 11:00:00');

-- ------------------------------------------------------------
-- 2. Seed Students (20+ Seeded Records for Practical 11 CRUD)
-- ------------------------------------------------------------
INSERT INTO `students` (`id`, `user_id`, `roll_no`, `name`, `email`, `mobile`, `course`, `semester`, `year`, `gender`, `division`, `batch`, `created_at`) VALUES
(1, 3, '25DIT006', 'Tanis Bedia', '25dit006@charusat.edu.in', '9876543210', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch A', '2026-08-15 10:00:00'),
(2, 4, '25DIT001', 'Aarav Patel', '25dit001@charusat.edu.in', '9876500001', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch A', '2026-08-15 10:15:00'),
(3, 5, '25DIT012', 'Diya Shah', '25dit012@charusat.edu.in', '9876500012', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch B', '2026-08-15 10:30:00'),
(4, 6, '25DIT024', 'Rohan Sharma', '25dit024@charusat.edu.in', '9876500024', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch B', '2026-08-15 10:45:00'),
(5, 7, '25DIT038', 'Ananya Joshi', '25dit038@charusat.edu.in', '9876500038', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch C', '2026-08-15 11:00:00'),
(6, NULL, '25DIT002', 'Ishaan Trivedi', '25dit002@charusat.edu.in', '9876500002', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch A', '2026-08-16 09:10:00'),
(7, NULL, '25DIT003', 'Kavya Desai', '25dit003@charusat.edu.in', '9876500003', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch A', '2026-08-16 09:20:00'),
(8, NULL, '25DIT004', 'Manav Dave', '25dit004@charusat.edu.in', '9876500004', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch A', '2026-08-16 09:30:00'),
(9, NULL, '25DIT005', 'Nitya Parikh', '25dit005@charusat.edu.in', '9876500005', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch A', '2026-08-16 09:40:00'),
(10, NULL, '25DIT007', 'Pranav Solanki', '25dit007@charusat.edu.in', '9876500007', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch A', '2026-08-16 09:50:00'),
(11, NULL, '25DIT008', 'Riddhi Bhatt', '25dit008@charusat.edu.in', '9876500008', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch A', '2026-08-16 10:00:00'),
(12, NULL, '25DIT014', 'Siddharth Vora', '25dit014@charusat.edu.in', '9876500014', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch B', '2026-08-17 10:10:00'),
(13, NULL, '25DIT015', 'Tanvi Mehta', '25dit015@charusat.edu.in', '9876500015', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch B', '2026-08-17 10:20:00'),
(14, NULL, '25DIT018', 'Utkarsh Pandya', '25dit018@charusat.edu.in', '9876500018', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch B', '2026-08-17 10:30:00'),
(15, NULL, '25DIT020', 'Vidhi Raval', '25dit020@charusat.edu.in', '9876500020', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch B', '2026-08-17 10:40:00'),
(16, NULL, '25DIT022', 'Yashraj Zala', '25dit022@charusat.edu.in', '9876500022', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch B', '2026-08-17 10:50:00'),
(17, NULL, '25DIT029', 'Aayushi Soni', '25dit029@charusat.edu.in', '9876500029', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch C', '2026-08-18 11:00:00'),
(18, NULL, '25DIT031', 'Bhavya Makwana', '25dit031@charusat.edu.in', '9876500031', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch C', '2026-08-18 11:10:00'),
(19, NULL, '25DIT033', 'Chirag Panchal', '25dit033@charusat.edu.in', '9876500033', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch C', '2026-08-18 11:20:00'),
(20, NULL, '25DIT035', 'Drashti Chauhan', '25dit035@charusat.edu.in', '9876500035', 'B.Tech IT', 3, 2, 'Female', '3IT-A', 'Batch C', '2026-08-18 11:30:00'),
(21, NULL, '25DIT040', 'Harshil Rana', '25dit040@charusat.edu.in', '9876500040', 'B.Tech IT', 3, 2, 'Male', '3IT-A', 'Batch C', '2026-08-18 11:40:00');

-- ------------------------------------------------------------
-- 3. Seed Events (Campus Hackathons, Workshops & Seminars)
-- ------------------------------------------------------------
INSERT INTO `events` (`id`, `title`, `description`, `category`, `event_date`, `time`, `location`, `organizer`, `capacity`, `poster_url`, `status`, `created_at`) VALUES
(1, 'Synora AI Innovation Hackathon 2026', 'A 24-hour student hackathon focused on building intelligent web and AI solutions for community and campus challenges.', 'Hackathon', '2026-10-12', '09:00 AM – 06:00 PM', 'DEPSTAR Innovation Lab (Room 303)', 'Department of Information Technology', 60, '../assets/images/hackathon.svg', 'Open', '2026-09-01 10:00:00'),
(2, 'Full-Stack Web Architectures Workshop', 'Hands-on deep dive into modern CSS architectures, component design systems, and asynchronous JavaScript APIs for ITUE203.', 'Workshop', '2026-10-18', '02:00 PM – 05:00 PM', 'Lab 208, Computer Block', 'WDF Student Chapter', 40, '../assets/images/workshop.svg', 'Open', '2026-09-02 11:00:00'),
(3, 'Computer Networks & Cloud Architecture', 'Invited expert seminar on enterprise network routing, subnet masking, and practical infrastructure architectures.', 'Seminar', '2026-10-25', '11:00 AM – 01:00 PM', 'Room 222 (Audi-Visual)', 'Prof. Ritesh Patel (RHP)', 80, '../assets/images/seminar.svg', 'Open', '2026-09-05 12:00:00'),
(4, 'Data Structures & Algorithms Sprint', 'Competitive algorithmic programming round covering trees, graphs, and dynamic programming in CSUC201.', 'Academic', '2026-11-02', '01:00 PM – 04:00 PM', 'DEPSTAR Lab 208', 'Prof. Amit Patel (ARP)', 50, '../assets/images/academic.svg', 'Open', '2026-09-10 14:00:00'),
(5, 'Object-Oriented Java Coding Sprint', 'Rapid coding challenge focusing on polymorphism, exception handling, and collection frameworks in CEUE203.', 'Workshop', '2026-11-09', '10:00 AM – 01:00 PM', 'DEPSTAR Lab 204', 'Prof. M. P. Dave (MPD)', 50, '../assets/images/workshop.svg', 'Open', '2026-09-15 10:00:00'),
(6, 'CHARUSAT Annual Cultural Showcase', 'University-wide celebration of regional performing arts, music, dance, and creative media competitions.', 'Cultural', '2026-11-15', '06:00 PM – 10:00 PM', 'CHARUSAT Sports Ground', 'University Cultural Cell', 500, '../assets/images/cultural.svg', 'Open', '2026-09-20 09:00:00'),
(7, 'Cyber Security & Ethical Hacking Clinic', 'Practical session demonstrating web application vulnerability assessment, SQL injection mitigation, and CSRF prevention.', 'Workshop', '2026-11-22', '02:00 PM – 05:00 PM', 'DEPSTAR Lab 303', 'DEPSTAR Cyber Cell', 45, '../assets/images/workshop.svg', 'Open', '2026-09-22 15:00:00'),
(8, 'Inter-College Chess & Esports Championship', 'Annual sports cell tournament featuring classical rapid chess and team-based strategic gaming.', 'Sports', '2026-11-28', '09:00 AM – 05:00 PM', 'Student Activity Center', 'CHARUSAT Sports Council', 120, '../assets/images/sports.svg', 'Open', '2026-09-25 11:30:00');

-- ------------------------------------------------------------
-- 4. Seed Registrations (M:N Links)
-- ------------------------------------------------------------
INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `status`, `registered_at`) VALUES
(1, 1, 1, 'Registered', '2026-09-15 10:30:00'),
(2, 1, 2, 'Registered', '2026-09-16 11:00:00'),
(3, 1, 4, 'Registered', '2026-09-20 14:15:00'),
(4, 2, 1, 'Registered', '2026-09-15 10:45:00'),
(5, 2, 3, 'Registered', '2026-09-18 09:20:00'),
(6, 3, 1, 'Registered', '2026-09-17 15:10:00'),
(7, 3, 5, 'Registered', '2026-09-22 16:00:00'),
(8, 4, 2, 'Waitlisted', '2026-09-23 11:30:00'),
(9, 4, 3, 'Registered', '2026-09-24 10:05:00'),
(10, 5, 1, 'Registered', '2026-09-19 12:40:00'),
(11, 5, 6, 'Registered', '2026-09-25 17:00:00'),
(12, 6, 1, 'Registered', '2026-09-26 10:15:00'),
(13, 7, 2, 'Registered', '2026-09-26 14:00:00'),
(14, 8, 4, 'Registered', '2026-09-27 09:45:00'),
(15, 9, 7, 'Registered', '2026-09-28 16:30:00');

-- ------------------------------------------------------------
-- 5. Seed Feedback Entries
-- ------------------------------------------------------------
INSERT INTO `feedback` (`id`, `name`, `email`, `category`, `rating`, `comments`, `created_at`) VALUES
(1, 'Tanis Bedia', '25dit006@charusat.edu.in', 'UI & Themes', 5, 'The dark theme palette and responsive timetable view are exceptionally well designed.', '2026-09-29 12:24:14'),
(2, 'Diya Shah', '25dit012@charusat.edu.in', 'Timetable & Schedule', 5, 'Class schedule cards make lab batch tracking effortless on mobile.', '2026-09-29 13:10:00'),
(3, 'Aarav Patel', '25dit001@charusat.edu.in', 'Events & Registration', 4, 'Looking forward to the upcoming DEPSTAR AI Hackathon registration confirmation.', '2026-09-30 11:00:00'),
(4, 'Rohan Sharma', '25dit024@charusat.edu.in', 'Courses & Materials', 5, 'Course syllabus downloads and lab manuals are cleanly accessible.', '2026-10-01 15:45:00'),
(5, 'Ananya Joshi', '25dit038@charusat.edu.in', 'General', 5, 'Great unified student portal for the Information Technology department.', '2026-10-02 16:20:00');

-- ------------------------------------------------------------
-- 6. Seed Audit Logs (Practical 14)
-- ------------------------------------------------------------
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'SYSTEM_INIT', 'Database schema and seed records initialized for Academic Year 2026–27', '127.0.0.1', '2026-08-01 09:00:00'),
(2, 1, 'USER_CREATED', 'Admin account coordinator@charusat.edu.in created with admin role', '127.0.0.1', '2026-08-01 09:30:00'),
(3, 2, 'EVENT_CREATED', 'Created new event: Synora AI Innovation Hackathon 2026', '127.0.0.1', '2026-09-01 10:00:00'),
(4, 2, 'EVENT_CREATED', 'Created new event: Full-Stack Web Architectures Workshop', '127.0.0.1', '2026-09-02 11:00:00'),
(5, 1, 'STUDENT_CREATED', 'Seeded initial 21 B.Tech IT department student directory records', '127.0.0.1', '2026-09-10 10:00:00'),
(6, 3, 'USER_LOGIN', 'Student Tanis Bedia (25DIT006) authenticated successfully', '127.0.0.1', '2026-09-29 09:10:00'),
(7, 3, 'EVENT_REGISTER', 'Student 25DIT006 registered for event: Synora AI Innovation Hackathon 2026', '127.0.0.1', '2026-09-29 09:15:00'),
(8, 1, 'ADMIN_LOGIN', 'Administrator Dr. Parth Goel logged in to access Admin Control Center', '127.0.0.1', '2026-10-01 08:30:00');

SET FOREIGN_KEY_CHECKS = 1;
