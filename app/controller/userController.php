<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_USER_SESSION'); 
    session_set_cookie_params(0, '/'); 
    session_start();                     
}

if (!defined('USER_CONTROLLER_INITIALIZED')) {
    define('USER_CONTROLLER_INITIALIZED', true);

    
    $ctrlAppPath = dirname(__DIR__);
    $ctrlConfigPath = $ctrlAppPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    if (file_exists($ctrlConfigPath)) {
        include_once($ctrlConfigPath);
    } else {
        die("Config file not found at: " . $ctrlConfigPath);
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'get_live_user_updates') {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=UTF-8');
    
    
    $userId = $_SESSION['user_id'] ?? $_SESSION['authUser']['id'] ?? $_SESSION['authUser']['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['notifications' => []]);
        exit();
    }
    
    $currentDate = date('Y-m-d');
    $notifications = [];
    
    try {
        $sql = "
            (
                SELECT 
                    'warning' AS type,
                    'New Message Received' AS title,
                    CONCAT(SUBSTRING(m.message, 1, 45), '...') AS message,
                    m.created_at AS sort_time
                FROM messages m
                JOIN conversations c ON m.conversation_id = c.id
                WHERE (c.user_id = $userId OR c.admin_id = $userId)
                  AND m.sender_id != $userId 
                  AND m.status = 'sent'
            )
            UNION ALL
            (
                SELECT 
                    CASE 
                        WHEN bh.due_date < '$currentDate' THEN 'danger' 
                        ELSE 'warning' 
                    END AS type,
                    CASE 
                        WHEN bh.due_date < '$currentDate' THEN 'Book Overdue Alert!' 
                        ELSE 'Return Due Soon' 
                    END AS title,
                    CONCAT('\"', b.title, '\" is due on ', DATE_FORMAT(bh.due_date, '%b %d, %Y')) AS message,
                    bh.borrowed_at AS sort_time
                FROM borrowing_history bh
                JOIN books b ON bh.book_id = b.id
                WHERE bh.user_id = $userId 
                  AND bh.status IN ('borrowed', 'overdue')
                  AND (bh.due_date < '$currentDate' OR bh.due_date <= DATE_ADD('$currentDate', INTERVAL 2 DAY))
            )
            ORDER BY sort_time DESC 
            LIMIT 5";
                
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $notifications[] = [
                    'type' => $row['type'],
                    'title' => $row['title'],
                    'message' => $row['message']
                ];
            }
        }
    } catch (Exception $e) {
        
    }
    
    echo json_encode(['notifications' => $notifications]);
    exit();
}

// 3. LOGOUT LOGIC
if (isset($_POST['logoutButton'])) {
    unset($_SESSION['authUser']);
    unset($_SESSION['user_id']);
    unset($_SESSION['userRole']);
    session_destroy();
    header("Location: /kmkdt-Library/public/login");
    exit();
}
function getAllBooks($conn, $search = '') {
    $search = trim($search);
    
    $baseSelect = "SELECT b.*, u.username AS borrower_name, bh.due_date 
                   FROM books b
                   LEFT JOIN borrowing_history bh ON b.id = bh.book_id AND bh.status IN ('borrowed', 'overdue')
                   LEFT JOIN user u ON bh.user_id = u.id";

    if (empty($search) || strtolower($search) === 'all') {
        return $conn->query("$baseSelect");
    }

    $filterCategories = ['Fiction', 'Non-Fiction', 'Research', 'Online'];

    if (in_array($search, $filterCategories)) {
        if ($search === 'Fiction') {
            $sql = "$baseSelect WHERE 
                    (b.category LIKE 'Fiction%' OR b.category LIKE '%, Fiction%') 
                    AND b.category NOT LIKE '%Non-Fiction%'";
            $stmt = $conn->prepare($sql);
        } else {
            $sql = "$baseSelect WHERE b.category LIKE ? OR b.genre LIKE ?";
            $searchTerm = "%$search%";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
        }
    } else {
        $sql = "$baseSelect WHERE 
                b.title LIKE ? OR b.author LIKE ? OR b.genre LIKE ? OR b.category LIKE ?";
        $searchTerm = "%$search%";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }
    
    $stmt->execute();
    return $stmt->get_result();
}
function processBookBorrow($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    $conn->begin_transaction();
    try {
        $catQuery = "SELECT category FROM books WHERE id = ? FOR UPDATE"; 
        $catStmt = $conn->prepare($catQuery);
        $catStmt->bind_param("i", $bookId);
        $catStmt->execute();
        $bookData = $catStmt->get_result()->fetch_assoc();
        
        if (!$bookData) {
            throw new Exception("Book not found.");
        }
        
        $category = $bookData['category'] ?? 'General';

        
        if (stripos($category, 'Online') !== false) {
            $days = 365; 
        } elseif (stripos($category, 'Reserve') !== false) {
            $days = 3;
        } elseif (stripos($category, 'Non-Fiction') !== false) {
            $days = 14; 
        } elseif (stripos($category, 'Research') !== false) {
            $days = 7;
        } else {
            $days = 18; 
        }

        $dueDate = date('Y-m-d', strtotime("+$days days"));

        
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

function getMyBooks($conn, $userId) {
    $userId = intval($userId);

    
    $query = "SELECT bh.id AS loan_id, b.id AS book_id, b.title, b.category, b.genre, b.cover_image, 
                    bh.due_date, bh.status, bh.renewal_count, bh.penalty
            FROM books b 
            JOIN borrowing_history bh ON b.id = bh.book_id 
            WHERE bh.user_id = ? AND bh.status IN ('borrowed', 'overdue')
            ORDER BY bh.borrowed_at DESC";
        
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return null;
}

function processBookRenewal($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    
    $checkQuery = "SELECT renewal_count FROM borrowing_history WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->bind_param("ii", $userId, $bookId);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result()->fetch_assoc();
    
    if ($result && $result['renewal_count'] >= 1) {
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

function processBookReturn($conn, $userId, $bookId) {
    $userId = intval($userId);
    $bookId = intval($bookId);

    $conn->begin_transaction();
    try {
        
        $checkSql = "SELECT status FROM borrowing_history 
                     WHERE user_id = ? AND book_id = ? AND status IN ('borrowed', 'overdue')
                     ORDER BY borrowed_at DESC LIMIT 1";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->bind_param("ii", $userId, $bookId);
        $stmtCheck->execute();
        $loan = $stmtCheck->get_result()->fetch_assoc();

        
        if ($loan && $loan['status'] === 'overdue') {
            
            $conn->rollback();
            return false; 
        }

        
        $query1 = "UPDATE books SET user_id = NULL, status = 'Available' WHERE id = ? AND user_id = ?";
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

function getPaymentHistory($conn, $userId) {
    $userId = intval($userId);
    
    $query = "SELECT pp.amount_paid, pp.paid_at, 
                     IFNULL(b.title, 'Fine Settlement Record') as title 
              FROM penalty_payments pp
              LEFT JOIN borrowing_history bh ON pp.loan_id = bh.id
              LEFT JOIN books b ON bh.book_id = b.id
              WHERE pp.user_id = ?
              ORDER BY pp.paid_at DESC";
              
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result();
    }
    return null;
}