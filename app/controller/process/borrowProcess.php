<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 1) . '/userController.php'; 


$userId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if ($userId && $bookId) {
    
    
    if (processBookBorrow($conn, $userId, $bookId)) {
        
        
        $_SESSION['message'] = "Book borrowed successfully!";
        $_SESSION['code'] = "success";
        
        
        header("Location: ../../../public/user/MBB?status=borrowed");
        exit();
    } else {
       
        $_SESSION['message'] = "This book is currently unavailable.";
        $_SESSION['code'] = "error";
        header("Location: ../../../public/user/browseBooks?status=unavailable");
        exit();
    }
}
header("Location: ../../../public/user/browseBooks?status=error");
exit();

