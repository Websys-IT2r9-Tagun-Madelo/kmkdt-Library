<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_USER_SESSION');
    session_start();
}

require_once dirname(__DIR__, 1) . '/userController.php'; 


$userId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;


if ($userId && $bookId) {
    try {
        
        if (isset($conn) && $conn instanceof mysqli) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        }


        if (processBookBorrow($conn, $userId, $bookId)) {
            
            $_SESSION['message'] = "Book borrowed successfully!";
            $_SESSION['code'] = "success";
            
            header("Location: ../../../public/user/myBooks?status=borrowed");
            exit();
        } else {
            $_SESSION['message'] = "This book is currently unavailable or already taken.";
            $_SESSION['code'] = "error";
            
            header("Location: ../../../public/user/browseBooks?status=unavailable");
            exit();
        }

    } catch (Exception $e) {
        
        error_log("Library Borrow Failure (User: $userId, Book: $bookId): " . $e->getMessage());

        $_SESSION['message'] = "System processing failure. Please refresh and try again.";
        $_SESSION['code'] = "error";
        
        header("Location: ../../../public/user/browseBooks?status=failed_transaction");
        exit();
    }
}

header("Location: ../../../public/user/browseBooks?status=error");
exit();

