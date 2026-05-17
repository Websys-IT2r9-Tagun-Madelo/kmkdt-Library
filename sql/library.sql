-- Conversations Table
CREATE TABLE IF NOT EXISTS conversations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES user(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_admin_id (admin_id)
);

-- Messages Table
CREATE TABLE IF NOT EXISTS messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('sent', 'read') DEFAULT 'sent',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES user(id) ON DELETE CASCADE,
    INDEX idx_conversation_id (conversation_id),
    INDEX idx_sender_id (sender_id)
);

--User Table
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


--Books Table with data
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
-- data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `description`, `category`, `genre`, `cover_image`, `status`, `user_id`) VALUES
(1, 'Introduction to Programming with Python', 'Harris Wang\r\n', 'Introduction to Computer Programming with Python is a beginner-friendly book that teaches the basics of programming using Python. It covers core concepts like variables, loops, functions, and object-oriented programming with simple examples and exercises.\r\n', 'Online, Non-Fiction', 'Technology', 'Introduction-to-Programming-with-Python.jpg', '', NULL),
(2, 'The History of the Ancient World', 'Susan Wise Bauer', 'A narrative history of the ancient world, examining the rise and fall of civilizations across the globe—from the Sumerians and Egyptians to the Romans and the Han dynasty. It highlights the political and social connections between cultures that are often studied in isolation.', 'Non-Fiction', 'History', 'the-history-of-the-ancient-world-from-the-earliest-accounts-to-the-fall-of-rome.jpg\n', 'available', NULL),
(3, 'One Piece Vol. 1', 'Eiichiro Oda', 'Monkey D. Luffy refuses to let anyone or anything stand in the way of his quest to become the king of all pirates. With a course charted for the treacherous waters of the Grand Line and beyond, this is one captain who\'ll never give up until he\'s claimed the greatest treasure on Earth the Legendary One Piece!', 'Fiction', 'Manga', 'one-piece-vol-1.jpg', '', 3),
(4, 'Fit for School Health Study', 'Bella Monse, Habib Benzian, Ella Naliponguit, Vincente Belizario, Alexander Schratz, Wim van Palenstein Helderman\r\n', 'Fit for School Health Study* examines the long-term health effects of an integrated school health program in the Philippines. The study evaluates how school-based interventions improve children’s health, hygiene, and overall well-being.\r\n', 'Research', 'Education and Public Health ', 'fit_for_school.jpg', 'available', NULL),
(5, 'Project Hail Mary', 'Andy Weir', 'Ryland Grace is the sole survivor on a desperate, last-chance mission—and if he fails, humanity and the earth itself will perish. Except that right now, he doesn’t know that. He can’t even remember his own name, let alone the nature of his assignment or how to complete it. All he knows is that he’s been asleep for a very, very long time. And he’s just been awakened to find himself millions of miles from home, with nothing but two corpses for company.', 'Online, Fiction', 'Science ', 'p_h_m.jpg', '', NULL),
(6, 'The Duke and I', 'Julia Quinn', 'The Duke and I follows Daphne Bridgerton and Simon Basset, the Duke of Hastings, as they pretend to court each other to solve their social problems. Their fake relationship slowly turns into real romance filled with secrets, wit, and emotional challenges.', 'Fiction', 'Romance', 'duke_and_i.jpg', '', NULL),
(7, 'The Impact of College on Students', 'Kenneth A. Feldman and Theodore M. Newcomb', 'The Impact of College on Students explores how college experiences shape students’ intellectual, social, and personal development. The book examines the long-term effects of higher education on attitudes, behavior, and achievement.', 'Reserve, Research', 'Education and Student Development', 'college_impact.jpg', 'available', NULL);


--Penalty payments Table
CREATE TABLE `penalty_payments` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--borrowing_history Table
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



