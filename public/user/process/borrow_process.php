<?php
session_start();


require_once dirname(__DIR__, 3) . '/app/controller/userController.php';


$currentUserId = $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if (!$currentUserId || !$bookId) {
    
    header("Location: ../BrowBoks?status=error");
    exit();
}


$success = processBookBorrow($conn, $currentUserId, $bookId);

if ($success) {
    
    header("Location: ../MBB?status=borrowed");
} else {
    header("Location: ../BrowBoks?status=unavailable");
}
exit();