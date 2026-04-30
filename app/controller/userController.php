<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Locating the config file
$appPath = dirname(__DIR__);
$configPath = $appPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Config file not found at: " . $configPath);
}

if (isset($_POST['logoutButton'])) {
    unset($_SESSION['authUser']);
    unset($_SESSION['user_id']);
    unset($_SESSION['userRole']);

    session_destroy();

    header("Location: /kmkdt-Library/public/login");
    exit();
}
/**
 * Fetch books based on search criteria
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
 * Processes the borrowing logic
 */
function processBookBorrow($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    // Start a transaction to ensure both updates happen together
    $conn->begin_transaction();

    try {
        // 1. Update the books table to show it's taken
        $query1 = "UPDATE books SET user_id = ? WHERE id = ? AND user_id IS NULL";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("ii", $userId, $bookId);
        $stmt1->execute();

        if ($stmt1->affected_rows > 0) {
            // 2. Insert into borrowing_history so the Profile Stats can see it
            $query2 = "INSERT INTO borrowing_history (user_id, book_id, status) VALUES (?, ?, 'borrowed')";
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
 * Fetch user borrowing statistics for the profile dashboard
 */
function getUserStats($conn, $userId) {
    $userId = intval($userId);
    $stats = [
        'total_borrowed' => 0,
        'current_holdings' => 0
    ];

    // Count total books ever borrowed by this user
    $totalQuery = "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ?";
    if ($stmt = $conn->prepare($totalQuery)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stats['total_borrowed'] = $res['total'] ?? 0;
    }

    // Count books currently in possession (status: 'borrowed')
    $currentQuery = "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ? AND status = 'borrowed'";
    if ($stmt = $conn->prepare($currentQuery)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stats['current_holdings'] = $res['total'] ?? 0;
    }

    return $stats;
}

/**
 * Fetches the list of books currently borrowed by the user for UI display
 */
function getMyBooks($conn, $userId) {
    $userId = intval($userId);
    // Joins the books table with borrowing history to get titles and genres
    $query = "SELECT b.title, b.genre 
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
?>