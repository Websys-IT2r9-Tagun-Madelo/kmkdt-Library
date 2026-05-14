<?php
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
    // Subquery checks for any active (non-returned) status in borrowing_history
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
    $total = mysqli_fetch_assoc($totalQuery)['total'] ?: 1; 

    $statsQuery = mysqli_query($conn, "SELECT 
        SUM(status = 'borrowed') as borrowed,
        SUM(status = 'returned') as returned,
        SUM(status = 'overdue') as overdue,
        SUM(status = 'due soon') as due_soon
        FROM borrowing_history");
    
    $counts = mysqli_fetch_assoc($statsQuery);

    return [
        'borrowed_pct' => ($counts['borrowed'] / $total) * 100,
        'returned_pct' => ($counts['returned'] / $total) * 100,
        'overdue_pct' => ($counts['overdue'] / $total) * 100,
        'due_soon_pct' => ($counts['due_soon'] / $total) * 100
    ];
}
?>