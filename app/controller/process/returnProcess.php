<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 1) . '/userController.php'; 

$currentUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;

$loanId = $_GET['id'] ?? null; 


if (!$currentUserId || !$loanId) {
    header("Location: /kmkdt-Library/public/user/myBooks?error=unauthorized");
    exit();
}

$checkSql = "SELECT status, book_id FROM borrowing_history 
             WHERE id = ? AND user_id = ? AND status IN ('borrowed', 'overdue') 
             LIMIT 1";

if ($stmt = $conn->prepare($checkSql)) {
    $stmt->bind_param("ii", $loanId, $currentUserId);
    $stmt->execute();
    $loan = $stmt->get_result()->fetch_assoc();
}


if ($loan && $loan['status'] === 'overdue') {
    header("Location: /kmkdt-Library/public/user/myBooks?error=payment_required");
    exit();
}

$actualBookId = $loan['book_id'] ?? null;

if ($actualBookId) {
    $success = processBookReturn($conn, $currentUserId, $actualBookId);
} else {
    $success = false;
}

if ($success) {
    header("Location: /kmkdt-Library/public/user/myBooks?success=returned");
} else {
    header("Location: /kmkdt-Library/public/user/myBooks?error=failed");
}
exit();