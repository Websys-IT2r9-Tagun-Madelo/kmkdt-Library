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
function getAllBooks($conn, $search = '') {
    $search = trim($search);
    
    if (empty($search) || strtolower($search) === 'all') {
        return $conn->query("SELECT * FROM books");
    }

    $filterCategories = ['Fiction', 'Non-Fiction', 'Research', 'Online'];

    if (in_array($search, $filterCategories)) {
        if ($search === 'Fiction') {
            // This logic specifically finds "Fiction" or "Online, Fiction" 
            // while EXCLUDING "Non-Fiction"
            $sql = "SELECT * FROM books WHERE 
                    (category LIKE 'Fiction%' OR category LIKE '%, Fiction%') 
                    AND category NOT LIKE '%Non-Fiction%'";
            $stmt = $conn->prepare($sql);
        } else {
            // Standard partial match for other categories
            $sql = "SELECT * FROM books WHERE category LIKE ? OR genre LIKE ?";
            $searchTerm = "%$search%";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
        }
    } else {
        // General Keyword Search
        $sql = "SELECT * FROM books WHERE 
                title LIKE ? OR author LIKE ? OR genre LIKE ? OR category LIKE ?";
        $searchTerm = "%$search%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}
/**
 * Processes the borrowing transaction
 * Updated to sync with UI loan period logic
 */
function processBookBorrow($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    $conn->begin_transaction();
    try {
        // Fetch category to apply your specific Feature Rules
        $catQuery = "SELECT category FROM books WHERE id = ? FOR UPDATE"; 
        $catStmt = $conn->prepare($catQuery);
        $catStmt->bind_param("i", $bookId);
        $catStmt->execute();
        $bookData = $catStmt->get_result()->fetch_assoc();
        
        if (!$bookData) {
            throw new Exception("Book not found.");
        }
        
        $category = $bookData['category'] ?? 'General';

        // SYNCED LOGIC HIERARCHY
        if (stripos($category, 'Online') !== false) {
            $days = 365; // Logical "Unlimited" for history records
        } elseif (stripos($category, 'Reserve') !== false) {
            $days = 3;
        } elseif (stripos($category, 'Non-Fiction') !== false) {
            $days = 14; 
        } elseif (stripos($category, 'Research') !== false) {
            $days = 7;
        } else {
            $days = 18; // Standard default
        }

        $dueDate = date('Y-m-d', strtotime("+$days days"));

        // Update book: Set user_id AND status to 'Unavailable'
        $query1 = "UPDATE books SET user_id = ?, status = 'Unavailable' WHERE id = ? AND user_id IS NULL";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("ii", $userId, $bookId);
        $stmt1->execute();

        if ($stmt1->affected_rows > 0) {
            $query2 = "INSERT INTO borrowing_history (user_id, book_id, status, borrowed_at, due_date, renewal_count) 
                       VALUES (?, ?, 'borrowed', NOW(), ?, 0)";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bind_param("iis", $userId, $bookId, $dueDate);
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
 * Fetches user books for MBB.php
 */
function getMyBooks($conn, $userId) {
    $userId = intval($userId);
    
    $query = "SELECT b.id, b.title, b.genre, b.cover_image, 
                     MAX(bh.borrowed_at) as borrowed_at, 
                     MAX(bh.due_date) as due_date, 
                     MAX(bh.renewal_count) as renewal_count 
              FROM books b 
              JOIN borrowing_history bh ON b.id = bh.book_id 
              WHERE bh.user_id = ? AND bh.status IN ('borrowed', 'overdue')
              GROUP BY b.id 
              ORDER BY borrowed_at DESC";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return null;
}

/**
 *
 */
function processBookRenewal($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    // 1. Check if the renewal limit (2) has been reached
    $checkQuery = "SELECT renewal_count FROM borrowing_history WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->bind_param("ii", $userId, $bookId);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result()->fetch_assoc();
    
    if ($result && $result['renewal_count'] >= 2) {
        return false; 
    }
    $query = "UPDATE borrowing_history 
              SET borrowed_at = NOW(), 
                  renewal_count = renewal_count + 1,
                  status = 'borrowed' 
              WHERE user_id = ? AND book_id = ? AND status IN ('borrowed', 'overdue')";
    
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
        $query1 = "UPDATE books SET user_id = NULL WHERE id = ? AND user_id = ?";
        $stmt1 = $conn->prepare($query1);
        $stmt1->bind_param("ii", $bookId, $userId);
        $stmt1->execute();

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
 * Fetch user stats for the Dashboard
 */
function getUserStats($conn, $userId) {
    $userId = intval($userId);
    $stats = ['total_borrowed' => 0, 'current_holdings' => 0, 'total_returned' => 0];

    $queries = [
        'total_borrowed' => "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ?",
        'current_holdings' => "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ? AND status = 'borrowed'",
        'total_returned' => "SELECT COUNT(*) as total FROM borrowing_history WHERE user_id = ? AND status = 'returned'"
    ];

    foreach ($queries as $key => $sql) {
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();
            $stats[$key] = $res['total'] ?? 0;
        }
    }
    return $stats;
}

/**
 * Fetches full user details for profile
 */
function getUserById($conn, $userId) {
    $userId = intval($userId);
    $sql = "SELECT username, emailAddress, street, barangay, city, fullName FROM user WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc(); 
    }
    return null;
}

function getBookForReader($conn, $id) {
    $id = intval($id); 
    $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


function getBooksByCategory($conn, $categoryName, $limit = 3) {
    // We use the 'genre' column to match your established logic
    $query = "SELECT * FROM books WHERE genre LIKE ? LIMIT ?";
    $stmt = $conn->prepare($query);
    $searchTerm = "%" . $categoryName . "%";
    $stmt->bind_param("si", $searchTerm, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    return $books;
}
?>