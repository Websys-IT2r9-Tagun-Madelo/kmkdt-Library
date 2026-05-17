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
    header("Location: /kmkdt-Library/public/user/myBooks?error=unauthorized");
    exit();
}

// 2. Target the explicit history record, pull category, AND grab the current due_date
$checkSql = "SELECT bh.status, bh.renewal_count, bh.due_date, b.category 
             FROM borrowing_history bh
             JOIN books b ON bh.book_id = b.id
             WHERE bh.id = ? AND bh.user_id = ? AND bh.status IN ('borrowed', 'overdue') 
             LIMIT 1";

$loan = null;
if ($stmt = $conn->prepare($checkSql)) {
    $stmt->bind_param("ii", $loanId, $currentUserId);
    $stmt->execute();
    $loan = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Security boundary check: If no loan record matches, drop out safely
if (!$loan) {
    header("Location: /kmkdt-Library/public/user/myBooks?error=not_found");
    exit();
}

// Block renewal if the user attempts to bypass an overdue penalty lock
if ($loan['status'] === 'overdue') {
    header("Location: /kmkdt-Library/public/user/myBooks?error=payment_required");
    exit();
}

// Enforce your maximum 3-renewal limit restriction policy
if ((int)$loan['renewal_count'] >= 2) {
    header("Location: /kmkdt-Library/public/user/myBooks?error=limit_reached");
    exit();
}

$bookCategory = $loan['category'] ?? 'General';

// E-Books check fallback safety (Online options don't use renewals)
if (stripos($bookCategory, 'Online') !== false) {
    header("Location: /kmkdt-Library/public/user/myBooks?error=online_unlimited");
    exit();
}

// 3. DYNAMIC DAY CALCULATION: Evaluate category classification
if (stripos($bookCategory, 'Reserve') !== false) {
    $daysToAdd = 3;
} elseif (stripos($bookCategory, 'Non-Fiction') !== false) {
    $daysToAdd = 14;
} elseif (stripos($bookCategory, 'Research') !== false) {
    $daysToAdd = 7;
} else {
    $daysToAdd = 18;
}

// FIXED: Calculate new timeline based relative to its PRIOR due date, not 'today'
$currentDueDateTimestamp = strtotime($loan['due_date']);
$newDueDate = date('Y-m-d H:i:s', strtotime("+$daysToAdd days", $currentDueDateTimestamp));

// 4. Process the Renewal Execution
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
    $stmt->close();
}

// Absolute paths to guarantee that folder doubling URL bug stays dead
if ($success) {
    header("Location: /kmkdt-Library/public/user/myBooks?success=renewed&days=$daysToAdd");
} else {
    header("Location: /kmkdt-Library/public/user/myBooks?error=failed");
}
exit();