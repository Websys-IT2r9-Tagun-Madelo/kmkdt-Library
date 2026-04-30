<?php
session_start();

// 1. Correctly locate the controller (3 levels up from public/user/process/)
require_once dirname(__DIR__, 3) . '/app/controller/userController.php';

// 2. Identify the user and the book
$currentUserId = $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if (!$currentUserId || !$bookId) {
    // FIX: Redirect up one level and use clean URL
    header("Location: ../MBB?status=error");
    exit();
}

// 3. Call the return function defined in your controller
$success = processBookReturn($conn, $currentUserId, $bookId);

if ($success) {
    // FIX: Redirect up one level to MBB
    header("Location: ../MBB?status=returned");
} else {
    // FIX: Redirect up one level to MBB
    header("Location: ../MBB?status=fail");
}
exit();