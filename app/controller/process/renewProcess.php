<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once dirname(__DIR__, 1) . '/userController.php'; 


$currentUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;
$query = "SELECT genre, renewal_count FROM borrowings WHERE id = ?";


$category = $row['genre'];
$extensionDays = (stripos($category, 'Fiction') !== false) ? 30 : 14;


$updateQuery = "UPDATE borrowings SET 
                borrowed_at = CURRENT_TIMESTAMP, 
                renewal_count = renewal_count + 1 
                WHERE id = ? AND renewal_count < 2";

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