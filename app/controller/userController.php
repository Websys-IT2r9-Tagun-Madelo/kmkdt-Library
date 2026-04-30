<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. DATABASE & CONFIGURATION
$appPath = dirname(__DIR__);
$configPath = $appPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Config file not found at: " . $configPath);
}

// 2. LOGOUT LOGIC
if (isset($_POST['logoutButton'])) {
    unset($_SESSION['authUser']);
    unset($_SESSION['user_id']);
    unset($_SESSION['userRole']);
    session_destroy();
    header("Location: /kmkdt-Library/public/login");
    exit();
}

/**
 * Fetch books for the Browse catalog
 */
function getAllBooks($conn, $searchTerm = "") {
    if (!empty($searchTerm)) {
        $query = "SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR genre LIKE ?";
        $stmt = $conn->prepare($query);
        $search = "%$searchTerm%";
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        return $stmt->get_result();
    } else {
        $query = "SELECT * FROM books";
        return $conn->query($query);
    }
}

/**
 * Processes the borrowing transaction
 */
function processBookBorrow($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    $conn->begin_transaction();
    try {
        // Update books table to assign the book to the user
        $query1 = "UPDATE books SET user_id = ? WHERE id = ? AND user_id IS NULL";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("ii", $userId, $bookId);
        $stmt1->execute();

        if ($stmt1->affected_rows > 0) {
            // Log the action in history
            $query2 = "INSERT INTO borrowing_history (user_id, book_id, status, borrowed_at) VALUES (?, ?, 'borrowed', NOW())";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bind_param("ii", $userId, $bookId);
            $stmt2->execute();

            $conn->commit();
            return true;
        }
        $conn->rollback();
        return false;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/**
 * Fetches books currently held by the user
 */
function getMyBooks($conn, $userId) {
    $userId = intval($userId);
    $query = "SELECT b.id, b.title, b.genre, b.cover_image, bh.borrowed_at 
              FROM books b 
              JOIN borrowing_history bh ON b.id = bh.book_id 
              WHERE bh.user_id = ? AND bh.status = 'borrowed'";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return null;
}

/**
 * Extends the borrowing period (Renewal)
 */
function processBookRenewal($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    $query = "UPDATE borrowing_history 
              SET borrowed_at = NOW() 
              WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $bookId);
    return $stmt->execute();
}

/**
 * Processes the return of a book
 */
function processBookReturn($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    $conn->begin_transaction();
    try {
        // Clear user_id from books table
        $query1 = "UPDATE books SET user_id = NULL WHERE id = ? AND user_id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("ii", $bookId, $userId);
        $stmt1->execute();

        // Update history status
        $query2 = "UPDATE borrowing_history SET status = 'returned', returned_at = NOW() 
                   WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
        $stmt2 = $conn->prepare($query2);
        $stmt2->bind_param("ii", $userId, $bookId);
        $stmt2->execute();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

/**
 * Fetch stats for the Profile dashboard
 */
/**
 * Fetch user borrowing statistics including total returned
 */
function getUserStats($conn, $userId) {
    $userId = intval($userId);
    $stats = [
        'total_borrowed' => 0, 
        'current_holdings' => 0,
        'total_returned' => 0 
    ];

    // 1. Total Borrowed (All time)
    $totalQuery = "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ?";
    if ($stmt = $conn->prepare($totalQuery)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stats['total_borrowed'] = $res['total'] ?? 0;
    }

    // 2. Currently Holding (Active borrows)
    $currentQuery = "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ? AND status = 'borrowed'";
    if ($stmt = $conn->prepare($currentQuery)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stats['current_holdings'] = $res['total'] ?? 0;
    }

    // 3. NEW: Total Returned
    $returnedQuery = "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ? AND status = 'returned'";
    if ($stmt = $conn->prepare($returnedQuery)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stats['total_returned'] = $res['total'] ?? 0;
    }

    return $stats;
}
?>