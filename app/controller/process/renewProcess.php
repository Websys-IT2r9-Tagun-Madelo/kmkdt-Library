<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Adjust path mapping depth as per project requirements
require_once dirname(__DIR__, 1) . '/userController.php'; 

$currentUserId = $_SESSION['user_id'] ?? $_SESSION['authUser']['user_id'] ?? null;
// Captures the dynamic loan transaction ID passed via URL (?id=...)
$loanId = $_GET['id'] ?? null; 

// 1. Authorization Check
if (!$currentUserId || !$loanId) {
    header("Location: /kmkdt-Library/public/user/MBB?error=unauthorized");
    exit();
}

// 2. Target the explicit history record using its unique ID
$checkSql = "SELECT status, renewal_count FROM borrowing_history 
             WHERE id = ? AND user_id = ? AND status IN ('borrowed', 'overdue') 
             LIMIT 1";

if ($stmt = $conn->prepare($checkSql)) {
    $stmt->bind_param("ii", $loanId, $currentUserId);
    $stmt->execute();
    $loan = $stmt->get_result()->fetch_assoc();
}

// Block renewal if the user attempts to bypass an overdue penalty lock
if ($loan && $loan['status'] === 'overdue') {
    header("Location: /kmkdt-Library/public/user/MBB?error=payment_required");
    exit();
}

// Enforce your maximum 3-renewal limit restriction policy
if ($loan && (int)$loan['renewal_count'] >= 3) {
    header("Location: /kmkdt-Library/public/user/MBB?error=limit_reached");
    exit();
}

// 3. Process the Renewal Execution
// Extends the timeline relative to the current timestamp by 18 days
$newDueDate = date('Y-m-d H:i:s', strtotime('+18 days'));

$updateSql = "UPDATE borrowing_history 
              SET due_date = ?, 
                  renewal_count = renewal_count + 1 
              WHERE id = ? AND user_id = ?";

$success = false;
if ($stmt = $conn->prepare($updateSql)) {
    $stmt->bind_param("sii", $newDueDate, $loanId, $currentUserId);
    if ($stmt->execute()) {
        $success = true;
    }
}

// Absolute paths to guarantee that folder doubling URL bug stays dead
if ($success) {
    header("Location: /kmkdt-Library/public/user/MBB?success=renewed");
} else {
    header("Location: /kmkdt-Library/public/user/MBB?error=failed");
}
exit();