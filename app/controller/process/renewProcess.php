<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** 
 * PATH ADJUSTMENT:
 * Since this file is now in app/controller/process/
 * We go up one level (../) to reach userController.php.
 */
require_once dirname(__DIR__, 1) . '/userController.php'; 

/**
 * Consistent Session Key:
 * Using 'user_id' to match your established midterm authentication structure.
 */
$currentUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if ($currentUserId && $bookId) {

    if (processBookRenewal($conn, $currentUserId, $bookId)) {

        header("Location: ../../../public/user/MBB?success=renewed");
        exit();
    } else {
        
        header("Location: ../../../public/user/MBB?error=limit_reached");
        exit();
    }
}

header("Location: ../../../public/user/MBB?error=system");
exit();