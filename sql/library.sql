-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2026 at 07:30 AM
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
  `status` enum('available','borrowed','online') DEFAULT 'available',
  `user_id` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `category`, `genre`, `cover_image`, `status`, `user_id`) VALUES
(1, 'Introduction to Programming with Python', 'Harris Wang\r\n', 'Introduction to Computer Programming with Python is a beginner-friendly book that teaches the basics of programming using Python. It covers core concepts like variables, loops, functions, and object-oriented programming with simple examples and exercises.\r\n', 'Online, Non-Fiction', 'Technology', 'Introduction-to-Programming-with-Python.jpg', '', NULL),
(2, 'The History of the Ancient World', 'Susan Wise Bauer', 'A narrative history of the ancient world, examining the rise and fall of civilizations across the globe—from the Sumerians and Egyptians to the Romans and the Han dynasty. It highlights the political and social connections between cultures that are often studied in isolation.', 'Non-Fiction', 'History', 'the-history-of-the-ancient-world-from-the-earliest-accounts-to-the-fall-of-rome.jpg\n', 'available', NULL),
(3, 'One Piece Vol. 1', 'Eiichiro Oda', 'Monkey D. Luffy refuses to let anyone or anything stand in the way of his quest to become the king of all pirates. With a course charted for the treacherous waters of the Grand Line and beyond, this is one captain who\'ll never give up until he\'s claimed the greatest treasure on Earth the Legendary One Piece!', 'Fiction', 'Manga', 'one-piece-vol-1.jpg', '', 3),
(4, 'Fit for School Health Study', 'Bella Monse, Habib Benzian, Ella Naliponguit, Vincente Belizario, Alexander Schratz, Wim van Palenstein Helderman\r\n', 'Fit for School Health Study* examines the long-term health effects of an integrated school health program in the Philippines. The study evaluates how school-based interventions improve children’s health, hygiene, and overall well-being.\r\n', 'Research', 'Education and Public Health ', 'fit_for_school.jpg', 'available', NULL),
(5, 'Project Hail Mary', 'Andy Weir', 'Ryland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and the earth itself will perish. Except that right now, he doesn’t know that. He can’t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he’s been asleep for a very, very long time. And he’s just been awakened to find himself millions of miles from home, with nothing but two corpses for company.', 'Online, Fiction', 'Science ', 'p_h_m.jpg', '', NULL),
(6, 'The Duke and I', 'Julia Quinn', 'The Duke and I follows Daphne Bridgerton and Simon Basset, the Duke of Hastings, as they pretend to court each other to solve their social problems. Their fake relationship slowly turns into real romance filled with secrets, wit, and emotional challenges.', 'Fiction', 'Romance', 'duke_and_i.jpg', '', NULL),
(7, 'The Impact of College on Students', 'Kenneth A. Feldman and Theodore M. Newcomb', 'The Impact of College on Students explores how college experiences shape students’ intellectual, social, and personal development. The book examines the long-term effects of higher education on attitudes, behavior, and achievement.', 'Reserve, Research', 'Education and Student Development', 'college_impact.jpg', 'available', NULL);

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
  `status` enum('borrowed','returned','overdue','due soon') DEFAULT 'borrowed',
  `returned_at` timestamp NULL DEFAULT NULL,
  `renewal_count` int(11) DEFAULT 0,
  `penalty` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowing_history`
--

INSERT INTO `borrowing_history` (`id`, `user_id`, `book_id`, `borrowed_at`, `due_date`, `status`, `returned_at`, `renewal_count`, `penalty`) VALUES
(1, 3, 3, '2026-05-16 05:05:57', '2026-06-03', 'borrowed', NULL, 0, 0.00),
(2, 3, 4, '2026-05-16 05:06:09', '2026-05-30', 'returned', '2026-05-16 05:06:16', 1, 0.00);

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
(3, '4a5d09b5-6bce-486b-a23e-882fde0226ea', 'Trafalgar D. Water Law', 'Law1233@gmail.com', 'Trafalgar24', '$2y$10$fT8ZlVlGt4BJemj3gRY5iObyPJWeqprtHpOMHzvxgvoaW8Mp8qhdC', 'Dressrosa Street', 'dressrosa', 'rosa', 'user', '2026-04-06 22:31:50', 'b124db346a9d1e832939efdc2e3dafec987ecb7832fbdc7788653860a6d739b0', '2026-05-13 00:27:56'),
(4, '5aba1f52-3990-4db7-8ede-8b89c8707e55', 'Admin D. Admin', 'Admin@gmail.com', 'Admin123', '$2y$10$opdZwGM/RTUNjPiOlmxveORlTCqg2Hv7vLB1dfT0CGom80sSTJZqG', 'admin street', '', 'Dressrosa City', 'admin', '2026-04-06 22:41:26', NULL, NULL),
(12, 'b84cb570-9088-4882-af3f-bcb1ed98e7f1', 'Nico Robin', 'NicoRobin6@gmail.com', 'nicorobin6', '$2y$10$EHuZbcjp/FzrFvGqLTQB7.8MC92S4k8PjrPrizabwxFfQRcqojnCe', 'Ohara', 'Ohara', 'Ohara', 'user', '2026-04-30 10:19:15', NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `borrowing_history`
--
ALTER TABLE `borrowing_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `penalty_payments`
--
ALTER TABLE `penalty_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
