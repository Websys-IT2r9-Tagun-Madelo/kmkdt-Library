<?php
session_start();

// 1. Correctly locate the controller (3 levels up)
require_once dirname(__DIR__, 3) . '/app/controller/userController.php';

// 2. Identify the user and the book
$currentUserId = $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if ($currentUserId && $bookId) {
    // 3. Call the renewal function
    if (processBookRenewal($conn, $currentUserId, $bookId)) {
        // SUCCESS: Redirect up one level to MBB using clean URL
        header("Location: ../MBB?status=renewed");
        exit();
    }
}

// ERROR: Redirect back to MBB
header("Location: ../MBB?status=error");
exit();