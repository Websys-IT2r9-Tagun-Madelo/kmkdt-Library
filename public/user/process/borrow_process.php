<?php
session_start();

// 1. Load the controller and database connection
require_once dirname(__DIR__, 3) . '/app/controller/userController.php';

// 2. Identify the user and the book
$currentUserId = $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if (!$currentUserId || !$bookId) {
    // FIX: Use relative path and clean URL (no .php)
    header("Location: ../BrowBoks?status=error");
    exit();
}

// 3. Process the Borrowing Transaction
$success = processBookBorrow($conn, $currentUserId, $bookId);

if ($success) {
    // FIX: Go up one level to 'user' folder and use clean 'index' or 'Profile'
    header("Location: ../index?status=borrowed");
} else {
    // FIX: Use clean URL name
    header("Location: ../BrowBoks?status=unavailable");
}
exit();