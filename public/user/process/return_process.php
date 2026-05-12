<?php
session_start();

require_once dirname(__DIR__, 3) . '/app/controller/userController.php';

$currentUserId = $_SESSION['authUser']['user_id'] ?? null;
$bookId = $_GET['id'] ?? null;

if (!$currentUserId || !$bookId) {
    header("Location: ../MBB?status=error");
    exit();
}

$success = processBookReturn($conn, $currentUserId, $bookId);

if ($success) {
    header("Location: ../MBB?status=returned");
} else {
    header("Location: ../MBB?status=fail");
}
exit();