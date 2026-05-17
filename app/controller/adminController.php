<?php
// Start session if it hasn't been initialized yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===== FOR REAL-TIME NAVBAR NOTIFICATIONS =====
if (isset($_GET['action']) && $_GET['action'] === 'getNotifications') {
    // Clear any accidental whitespace or layouts from corrupting the JSON payload
    if (ob_get_length()) {
        ob_clean();
    }
    header('Content-Type: application/json');

    // Dynamically safely resolve database instance context if it hasn't dropped into memory yet
    if (!isset($conn)) {
        $configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
        if (file_exists($configPath)) {
            include_once($configPath);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database connection context link unavailable.']);
            exit();
        }
    }

    // Call your now safely defined notification core query function
    $notifications = getLiveOverdueNotifications($conn);

    // Ship the dynamic block cleanly to the JavaScript polling handler
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
    exit();
}

// 1. Safe Logout Interceptor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logoutButton'])) {
    $_SESSION = array(); // Clear all session values completely

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    header("Location: /kmkdt-Library/public/login");
    exit();
}

// 2. Data Queries
function getAllMembers($conn) {
    $sql = "SELECT id, fullName, role, dateCreated FROM user ORDER BY dateCreated ASC";
    try {
        $result = mysqli_query($conn, $sql);
        if ($result) {
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    } catch (mysqli_sql_exception $e) {
        die("Database Error: " . $e->getMessage());
    }
    return [];
}

function getCatalog($conn) {
    $sql = "SELECT b.*, 
            (SELECT h.status FROM borrowing_history h 
             WHERE h.book_id = b.id 
             AND h.status != 'returned' 
             LIMIT 1) as active_status
            FROM books b
            ORDER BY b.id DESC";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return [];
    }
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getRecentActivity($conn) {
    $sql = "SELECT h.*, u.fullName, b.title 
            FROM borrowing_history h
            JOIN user u ON h.user_id = u.id
            JOIN books b ON h.book_id = b.id
            ORDER BY h.id DESC LIMIT 5";
    try {
        $result = mysqli_query($conn, $sql);
        return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        return [];
    }
}

function getCirculationRecords($conn) {
    $sql = "SELECT 
                h.id, 
                u.fullName, 
                b.title, 
                h.borrowed_at, 
                h.due_date, 
                h.status,
                h.renewal_count
            FROM borrowing_history h
            LEFT JOIN user u ON h.user_id = u.id 
            LEFT JOIN books b ON h.book_id = b.id
            ORDER BY h.id ASC";

    $result = mysqli_query($conn, $sql);
    return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}

function getLiveOverdueNotifications($conn) {
    $currentDate = date('Y-m-d');
    
    // Scans borrowing_history for all status transitions, mapping clean user alerts
    $sql = "SELECT 
                CASE 
                    WHEN h.status = 'returned' THEN 'success'
                    WHEN h.status = 'overdue' OR (h.status = 'borrowed' AND h.due_date < '$currentDate') THEN 'danger'
                    ELSE 'warning'
                END AS type,
                CASE 
                    WHEN h.status = 'returned' THEN 'Book Returned Safely'
                    WHEN h.status = 'overdue' OR (h.status = 'borrowed' AND h.due_date < '$currentDate') THEN 'Overdue Book Warning'
                    ELSE 'New Book Loan Checked Out'
                END AS title,
                CASE 
                    WHEN h.status = 'returned' THEN CONCAT(u.fullName, ' successfully checked back in \"', b.title, '\"')
                    WHEN h.status = 'overdue' OR (h.status = 'borrowed' AND h.due_date < '$currentDate') THEN CONCAT(u.fullName, ' is past the deadline for \"', b.title, '\"')
                    ELSE CONCAT(u.fullName, ' borrowed \"', b.title, '\" (Due: ', DATE_FORMAT(h.due_date, '%b %d, %Y'), ')')
                END AS message,
                h.id
            FROM borrowing_history h
            JOIN user u ON h.user_id = u.id
            JOIN books b ON h.book_id = b.id
            ORDER BY h.id DESC LIMIT 5";
            
    try {
        $result = mysqli_query($conn, $sql);
        return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
    } catch (mysqli_sql_exception $e) {
        return [];
    }
}
?>