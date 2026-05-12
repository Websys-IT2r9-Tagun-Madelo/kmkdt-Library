<?php
session_start();
require_once dirname(__DIR__, 3) . '/app/controller/userController.php';

$currentUserId = $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if ($currentUserId && $bookId) {
    if (processBookRenewal($conn, $currentUserId, $bookId)) {
        header("Location: ../MBB?status=renewed");
        exit();
    } else {
        // NEW: Specific error for maximum renewal limit
        header("Location: ../MBB?status=limit_reached");
        exit();
    }
}

header("Location: ../MBB?status=error");
exit();