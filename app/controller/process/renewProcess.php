<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fixed Absolute Path for your XAMPP structure
require_once dirname(__DIR__, 1) . '/userController.php'; 
require_once dirname(__DIR__, 2) . '/config/config.php'; // Ensure $conn is defined here

$currentUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

// Use the same session key consistently
if ($bookId && $currentUserId) {
    
    // Call the function seen in Screenshot 2026-05-14 175741.jpg
    // This function should already handle the UPDATE and renewal_count logic
    $success = processBookRenewal($conn, $currentUserId, $bookId);

    if ($success) {
        // This points to C:/xampp/htdocs/kmkdt-Library/public/user/MMB.php
        header("Location: /kmkdt-Library/public/user/MBB?status=renewed");
        exit();
    } else {
        header("Location: /kmkdt-Library/public/user/MBB?status=limit_reached");
        exit();
    }
}

// Fallback error
header("Location: /kmkdt-Library/public/user/MBB?status=error");
exit();