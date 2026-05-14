<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Moves up one level to reach 'controller' folder
require_once dirname(__DIR__, 1) . '/userController.php'; 

// Check for the simple key OR the authUser array key
$userId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

// 1. Check if both User and Book ID exist
if ($userId && $bookId) {
    
    // 2. Execute borrowing via your controller
    if (processBookBorrow($conn, $userId, $bookId)) {
        // Success: Redirect to My Borrowed Books
        header("Location: ../../../public/user/MBB?status=borrowed");
        exit();
    } else {
        // Fail: Book unavailable
        header("Location: ../../../public/user/BrowBoks?status=unavailable");
        exit();
    }
}

// 3. Fallback: If any check fails, redirect back to the gallery
header("Location: ../../../public/user/BrowBoks?status=error");
exit();