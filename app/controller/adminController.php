<?php
// Start session if it hasn't been initialized yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

function getCirculationStats($conn) {
    $totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM borrowing_history");
    $totalRow = mysqli_fetch_assoc($totalQuery);
    $total = (!empty($totalRow['total'])) ? $totalRow['total'] : 1; 

    $statsQuery = mysqli_query($conn, "SELECT 
        SUM(status = 'borrowed') as borrowed,
        SUM(status = 'returned') as returned,
        SUM(status = 'overdue') as overdue,
        SUM(status = 'due soon') as due_soon
        FROM borrowing_history");
    
    $counts = mysqli_fetch_assoc($statsQuery);

    return [
        'borrowed_pct' => (($counts['borrowed'] ?? 0) / $total) * 100,
        'returned_pct' => (($counts['returned'] ?? 0) / $total) * 100,
        'overdue_pct' => (($counts['overdue'] ?? 0) / $total) * 100,
        'due_soon_pct' => (($counts['due_soon'] ?? 0) / $total) * 100
    ];
}
?>