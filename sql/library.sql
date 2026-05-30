-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2026 at 10:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` enum('available','borrowed','online') NOT NULL DEFAULT 'available',
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `ebook_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `category`, `genre`, `cover_image`, `status`, `user_id`, `ebook_file`) VALUES
(1, 'Introduction to Programming with Python', 'Harris Wang', 'Introduction to Computer Programming with Python is a beginner-friendly book that teaches the basics of programming using Python. It covers core concepts like variables, loops, functions, and object-oriented programming with simple examples and exercises.', 'Non-Fiction, Online', 'Technology', 'introduction-to-programming-with-python.jpg', 'available', NULL, 'ebook_1780089723_3af26eeb.pdf'),
(2, 'The History of the Ancient World', 'Susan Wise Bauer', 'A narrative history of the ancient world, examining the rise and fall of civilizations across the globe—from the Sumerians and Egyptians to the Romans and the Han dynasty. It highlights the political and social connections between cultures that are often studied in isolation.', 'Non-Fiction', 'History', 'the-history-of-the-ancient-world-from-the-earliest-accounts-to-the-fall-of-rome.jpg', 'available', NULL, NULL),
(3, 'One Piece Vol. 1', 'Eiichiro Oda', 'Monkey D. Luffy refuses to let anyone or anything stand in the way of his quest to become the king of all pirates. With a course charted for the treacherous waters of the Grand Line and beyond, this is one captain who\'ll never give up until he\'s claimed the greatest treasure on Earth the Legendary One Piece!', 'Fiction', 'Manga', 'one-piece-vol-1.jpg', 'available', NULL, NULL),
(4, 'Fit for School Health Study', 'Bella Monse, Habib Benzian, Ella Naliponguit, Vincente Belizario, Alexander Schratz, Wim van Palenstein Helderman', 'Fit for School Health Study examines the long-term health effects of an integrated school health program in the Philippines. The study evaluates how school-based interventions improve children’s health, hygiene, and overall well-being.', 'Non-Fiction, Research, Reserve', 'Health', 'fit_for_school.jpg', 'borrowed', 12, NULL),
(5, 'Project Hail Mary', 'Andy Weir', 'Ryland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and the earth itself will perish. Except that right now, he doesn’t know that. He can’t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he’s been asleep for a very, very long time. And he’s just been awakened to find himself millions of miles from home, with nothing but two corpses for company.', 'Fiction, Online', 'Science', 'p_h_m.jpg', 'available', NULL, 'ebook_1780089440_f2e23af1.pdf'),
(6, 'The Duke and I', 'Julia Quinn', 'The Duke and I follows Daphne Bridgerton and Simon Basset, the Duke of Hastings, as they pretend to court each other to solve their social problems. Their fake relationship slowly turns into real romance filled with secrets, wit, and emotional challenges.', 'Fiction', 'Romance', 'duke_and_i.jpg', 'borrowed', 12, NULL),
(7, 'The Impact of College on Students', 'Kenneth A. Feldman and Theodore M. Newcomb', 'The Impact of College on Students explores how college experiences shape students’ intellectual, social, and personal development. The book examines the long-term effects of higher education on attitudes, behavior, and achievement.', 'Non-Fiction, Research, Reserve', 'Development', 'college_impact.jpg', 'borrowed', 3, NULL),
(23, 'The Summer I Turned Pretty', 'Jenny han', 'The Summer I Turned Pretty follows Belly through one unforgettable summer at the beach house, where friendships, first love, and growing up change everything between her, Jeremiah, and Conrad.', 'Fiction', 'Romance', 'the_summer_i_turned_pretty.jpg', 'available', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `borrowing_history`
--

CREATE TABLE `borrowing_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrowed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date NOT NULL,
  `status` enum('borrowed','returned','overdue','pending_return') NOT NULL DEFAULT 'borrowed',
  `returned_at` timestamp NULL DEFAULT NULL,
  `renewal_count` int(11) DEFAULT 0,
  `penalty` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowing_history`
--

INSERT INTO `borrowing_history` (`id`, `user_id`, `book_id`, `borrowed_at`, `due_date`, `status`, `returned_at`, `renewal_count`, `penalty`) VALUES
(1, 3, 3, '2026-05-30 00:06:58', '2026-06-06', 'returned', '2026-05-30 07:04:57', 0, 0.00),
(2, 3, 4, '2026-05-30 00:07:04', '2026-06-06', 'returned', '2026-05-30 07:04:55', 0, 0.00),
(3, 3, 7, '2026-05-30 00:07:11', '2026-06-06', 'returned', '2026-05-30 07:04:51', 0, 0.00),
(4, 3, 7, '2026-05-30 07:07:56', '2026-06-06', 'borrowed', NULL, 0, 0.00),
(5, 12, 6, '2026-05-30 07:44:45', '2026-06-06', 'pending_return', NULL, 0, 0.00),
(6, 12, 4, '2026-05-30 07:44:53', '2026-06-06', 'pending_return', NULL, 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `user_id`, `admin_id`, `created_at`, `updated_at`) VALUES
(6, 12, 4, '2026-05-16 07:21:13', '2026-05-21 14:58:40'),
(7, 14, 4, '2026-05-16 16:56:00', '2026-05-23 12:51:33'),
(10, 3, 4, '2026-05-17 09:02:31', '2026-05-30 00:15:51'),
(11, 3, 12, '2026-05-17 09:03:42', '2026-05-30 00:15:03'),
(12, 15, 4, '2026-05-21 17:16:17', '2026-05-21 20:14:59'),
(13, 3, 15, '2026-05-23 11:46:27', '2026-05-29 22:12:50'),
(14, 12, 15, '2026-05-29 16:34:07', '2026-05-29 16:34:15'),
(15, 12, 29, '2026-05-29 16:36:16', '2026-05-29 16:36:20'),
(16, 29, 4, '2026-05-29 16:36:36', '2026-05-29 16:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `status` enum('sent','read') DEFAULT 'sent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `status`, `created_at`) VALUES
(1, 11, 3, 'hi', 'read', '2026-05-29 13:17:25'),
(2, 11, 12, 'hello', 'read', '2026-05-29 13:17:48'),
(3, 11, 3, 'hi', 'read', '2026-05-29 16:13:23'),
(4, 11, 3, 'hi', 'read', '2026-05-29 16:13:24'),
(5, 11, 12, 'hi', 'read', '2026-05-29 16:14:04'),
(6, 10, 3, 'may i ask for help?', 'read', '2026-05-29 16:14:37'),
(7, 10, 4, 'no', 'read', '2026-05-29 16:15:03'),
(8, 10, 4, 'char', 'read', '2026-05-29 16:15:04'),
(9, 10, 4, 'yes', 'read', '2026-05-29 16:15:05'),
(10, 14, 12, 'hi', 'sent', '2026-05-29 16:34:15'),
(11, 15, 12, 'hi', 'read', '2026-05-29 16:36:20'),
(12, 16, 29, 'may i ask for help', 'read', '2026-05-29 16:37:06'),
(13, 16, 29, '?>', 'read', '2026-05-29 16:37:08'),
(14, 16, 4, 'no', 'sent', '2026-05-29 16:37:29'),
(15, 16, 4, 'char', 'sent', '2026-05-29 16:37:30'),
(16, 16, 4, 'yes', 'sent', '2026-05-29 16:37:31'),
(17, 10, 3, 'may i ask for help?', 'read', '2026-05-29 22:12:42'),
(18, 11, 3, 'wazzap', 'read', '2026-05-29 22:12:47'),
(19, 13, 3, 'zaap', 'sent', '2026-05-29 22:12:50'),
(20, 11, 12, 'wazzap', 'read', '2026-05-29 22:13:22'),
(21, 10, 4, 'yez', 'read', '2026-05-29 22:13:46'),
(22, 10, 3, 'may i ask for help?', 'read', '2026-05-30 00:14:56'),
(23, 11, 3, 'wzzap', 'read', '2026-05-30 00:15:00'),
(24, 11, 3, 'hil', 'read', '2026-05-30 00:15:03'),
(25, 10, 4, 'yes', 'sent', '2026-05-30 00:15:49'),
(26, 10, 4, 'es', 'sent', '2026-05-30 00:15:50'),
(27, 10, 4, 'e', 'sent', '2026-05-30 00:15:51');

-- --------------------------------------------------------

--
-- Table structure for table `penalty_payments`
--

CREATE TABLE `penalty_payments` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penalty_payments`
--

INSERT INTO `penalty_payments` (`id`, `loan_id`, `user_id`, `amount_paid`, `paid_at`) VALUES
(1, 2, 3, 15.00, '2026-05-30 00:08:48'),
(2, 2, 3, 15.00, '2026-05-30 00:14:17'),
(3, 3, 3, 20.00, '2026-05-30 06:22:53'),
(4, 3, 3, 15.00, '2026-05-30 06:24:46'),
(5, 3, 3, 15.00, '2026-05-30 06:25:48'),
(6, 2, 3, 10.00, '2026-05-30 07:00:06'),
(7, 1, 3, 10.00, '2026-05-30 07:01:19'),
(8, 2, 3, 10.00, '2026-05-30 07:03:12'),
(9, 4, 3, 10.00, '2026-05-30 07:44:23'),
(10, 5, 12, 20.00, '2026-05-30 07:46:07'),
(11, 6, 12, 10.00, '2026-05-30 08:00:40'),
(12, 5, 12, 15.00, '2026-05-30 08:07:51'),
(13, 4, 3, 55.00, '2026-05-30 08:09:32'),
(14, 4, 3, 20.00, '2026-05-30 08:43:58'),
(15, 6, 12, 15.00, '2026-05-30 09:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `emailAddress` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `dateCreated` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `uuid`, `fullName`, `emailAddress`, `username`, `password`, `street`, `barangay`, `city`, `role`, `dateCreated`, `reset_token`, `token_expire`) VALUES
(3, '4a5d09b5-6bce-486b-a23e-882fde0226ea', 'Trafalgar D. Water Law', 'Law1233@gmail.com', 'law24', '$2y$10$8VjtFHD4vQTrcZ0qZ2/ifuUWNirnu20upJ88z6Eu9tuR8E0dhqYkO', 'Dressrosa Street', 'dressrosa', 'Dressrosa City', 'user', '2026-04-06 22:31:50', NULL, NULL),
(4, '5aba1f52-3990-4db7-8ede-8b89c8707e55', 'Admin D. Admin', 'admin1@gmail.com', 'admin1', '$2y$10$7SkC2a7TXRgZ6vaDghMJ7OmG51F.JR1YeWptqEduOFNYuqS0RhHmS', '', 'Dressrosa', 'Dressrosa', 'admin', '2026-04-06 22:41:26', '0efde6e341b750e9ad90f853f9c6cb9a77d039fcc82b359cb659c8fe68a24a04', '2026-05-23 21:41:16'),
(12, 'b84cb570-9088-4882-af3f-bcb1ed98e7f1', 'Nico Robin', 'NicoRobinn6@gmail.com', 'nicorobin6', '$2y$10$nlu2shFm9Xxt4B8j2op3F.rqIiQUz9dZ0h4UnG9R7e4NPH6MUpL8a', 'Ohara', 'Ohara', 'Ohara', 'user', '2026-04-30 10:19:15', NULL, NULL),
(14, '580fe9d1-ae99-46c4-aaff-850be5e02614', 'admin2cutie', 'admin2@kmkdtlibrary.edu.ph', 'admin2', '$2y$10$1q/4R7bod9HUzNYHAydDFeSC6Vt/wL033oVy2m8WyTuH9iE3m6UlC', '11', 'North pole', 'canada', 'admin', '2026-05-16 16:55:30', NULL, NULL),
(15, 'f94ccec0-fa3a-49b0-83d7-3faba21768a5', 'khai', 'kyla@gmail.com', 'kyky14', '$2y$10$uKfpi/i6KyKl07wmLwm51e05b6laUXSOeOUxq.KMaPYXkqg7RAuaO', '', '', '', 'user', '2026-05-21 17:16:02', NULL, NULL),
(29, '22b43bf2-cd97-4737-ad3d-d38364f32358', 'carlos sainz', 'carlos@gmail.com', 'carlos123', '$2y$10$N6.uN9HhfqwFS0yWNs/J7./icB6jEn5JPMY5JfN07M.MSrB/7gE66', '123', 'obrero', 'davao city', 'user', '2026-05-29 08:59:04', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrowing_history`
--
ALTER TABLE `borrowing_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_history` (`user_id`),
  ADD KEY `fk_book_history` (`book_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_admin_id` (`admin_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversation_id` (`conversation_id`),
  ADD KEY `idx_sender_id` (`sender_id`);

--
-- Indexes for table `penalty_payments`
--
ALTER TABLE `penalty_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `borrowing_history`
--
ALTER TABLE `borrowing_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `penalty_payments`
--
ALTER TABLE `penalty_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrowing_history`
--
ALTER TABLE `borrowing_history`
  ADD CONSTRAINT `borrowing_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `borrowing_history_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `fk_book_history` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_history` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversations_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
