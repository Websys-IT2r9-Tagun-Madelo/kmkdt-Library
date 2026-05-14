<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 1) . '/userController.php'; 


$userId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if ($userId && $bookId) {
    
    // Process the borrowing logic via your userController
    if (processBookBorrow($conn, $userId, $bookId)) {
        
        // Success: Set session data for the Lime Green Toast
        $_SESSION['message'] = "Book borrowed successfully!";
        $_SESSION['code'] = "success";
        
        // Using your preferred relative path back to My Borrowed Books
        header("Location: ../../../public/user/MBB?status=borrowed");
        exit();
    } else {
        // Failure: Book might already be borrowed or unavailable
        $_SESSION['message'] = "This book is currently unavailable.";
        $_SESSION['code'] = "error";
        header("Location: ../../../public/user/BrowBoks?status=unavailable");
        exit();
    }
}
header("Location: ../../../public/user/BrowBoks?status=error");
exit();

