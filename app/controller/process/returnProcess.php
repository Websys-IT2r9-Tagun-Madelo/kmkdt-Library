<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 1) . '/userController.php'; 

$currentUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if (!$currentUserId || !$bookId) {
    header("Location: ../../../public/user/MBB?error=unauthorized");
    exit();
}

$success = processBookReturn($conn, $currentUserId, $bookId);

if ($success) {
    header("Location: ../../../public/user/MBB?success=returned");
} else {
    header("Location: ../../../public/user/MBB?error=failed");
}
exit();