<?php
// 1. Path to Controller
require_once dirname(__DIR__, 2) . '/app/controller/userController.php';

// 2. Validate Session and ID
$userId = $_SESSION['user_id'] ?? null; 
$bookId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($userId && $bookId > 0) {
    // 3. Execute Borrowing
    if (processBookBorrow($conn, $userId, $bookId)) {
        // Redirect to the extensionless URL (no .php)
        header("Location: ./BrowBoks?status=success");
    } else {
        header("Location: ./BrowBoks?status=error");
    }
} else {
    header("Location: ./BrowBoks?status=login_required");
}
exit();