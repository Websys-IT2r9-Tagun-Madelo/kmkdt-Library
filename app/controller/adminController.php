<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. GLOBAL DATABASE CONNECTION
$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (file_exists($configPath)) {
    require_once($configPath);
} else {
    die("Configuration file not found at: " . $configPath);
}

// Ensure $conn is available
if (!isset($conn) || !$conn) {
    die("Database connection failed.");
}

// ==========================================
// 2. HELPER FUNCTIONS
// ==========================================

function redirect($message, $code) {
    $_SESSION['message'] = $message;
    $_SESSION['code'] = $code;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

function getLiveOverdueNotifications($conn, $isUnlimited = false) {
    $currentDate = date('Y-m-d');
    
    $limitClause = $isUnlimited ? "" : " LIMIT 5";
    
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
            ORDER BY h.id DESC" . $limitClause;
            
    try {
        $result = mysqli_query($conn, $sql);
        if (!$result) return [];
        
        $rawRecords = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        $dismissedIds = $_SESSION['dismissed_notifications'] ?? [];
        if (empty($dismissedIds)) {
            return $rawRecords;
        }
        
        return array_values(array_filter($rawRecords, function($item) use ($dismissedIds) {
            return !in_array((int)$item['id'], $dismissedIds);
        }));
        
    } catch (mysqli_sql_exception $e) {
        return [];
    }
}

// ==========================================
// 3. AJAX REQUESTS (GET)
// ==========================================
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];

    if ($action === 'getNotifications') {
        $isUnlimited = (isset($_GET['limit']) && $_GET['limit'] === 'none');
        echo json_encode(['success' => true, 'notifications' => getLiveOverdueNotifications($conn, $isUnlimited)]);
        exit();
    }

    if ($action === 'markNotificationRead') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $_SESSION['dismissed_notifications'][] = $id;
        }
        echo json_encode(['success' => true]);
        exit();
    }

    if ($action === 'clearAllNotifications') {
        $allActive = getLiveOverdueNotifications($conn, true);
        foreach ($allActive as $noti) {
            $_SESSION['dismissed_notifications'][] = $noti['id'];
        }
        echo json_encode(['success' => true]);
        exit();
    }
}

// ==========================================
// FORM ACTIONS (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Logout Handler
    if (isset($_POST['logoutButton'])) {
        $_SESSION = array();
        session_destroy();
        header("Location: /kmkdt-Library/public/login");
        exit();
    }

    // CRUD Handlers
    if (isset($_POST['action'])) {
        
        // CREATE
        if ($_POST['action'] === 'create') {
            $username = trim($_POST['username']);
            $email = filter_var($_POST['emailAddress'], FILTER_VALIDATE_EMAIL);

            $stmt = $conn->prepare("INSERT INTO user (fullName, username, emailAddress, role, street, barangay, city, password, dateCreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $hash = password_hash(!empty($_POST['password']) ? $_POST['password'] : 'Library123!', PASSWORD_DEFAULT);
            $stmt->bind_param("ssssssss", $_POST['fullName'], $username, $email, $_POST['role'], $_POST['street'], $_POST['barangay'], $_POST['city'], $hash);
            
            if ($stmt->execute()) redirect("Member created successfully!", "success");
            else redirect("Error: " . $stmt->error, "error");
        }

        // UPDATE
        elseif ($_POST['action'] === 'update') {
            $stmt = $conn->prepare("UPDATE user SET fullName = ?, emailAddress = ?, role = ?, street = ?, barangay = ?, city = ? WHERE username = ?");
            $stmt->bind_param("sssssss", $_POST['fullName'], $_POST['emailAddress'], $_POST['role'], $_POST['street'], $_POST['barangay'], $_POST['city'], $_POST['username']);
            
            if ($stmt->execute()) redirect("Account updated successfully.", "success");
            else redirect("Update failed: " . $stmt->error, "error");
        }

        // DELETE
        elseif ($_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM user WHERE username = ?");
            $stmt->bind_param("s", $_POST['username']);
            
            if ($stmt->execute()) redirect("User deleted successfully.", "warning");
            else redirect("Delete failed: " . $stmt->error, "error");
        }
    }
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
            ORDER BY b.id ASC";

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

/**
 * Fetches aggregate circulation metrics for the Admin Dashboard Overview
 */
function getCirculationStats($conn) {
    $query = "SELECT 
                SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) AS total_borrowed,
                SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) AS total_returned,
                SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) AS total_overdue,
                SUM(CASE WHEN status = 'due soon' THEN 1 ELSE 0 END) AS total_due_soon,
                COUNT(*) AS total_transactions
              FROM borrowing_history";
              
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Circulation Stats Query Failed: " . mysqli_error($conn));
    }
    
    $stats = mysqli_fetch_assoc($result);
    
    $total    = isset($stats['total_transactions']) ? (int)$stats['total_transactions'] : 0;
    $borrowed = isset($stats['total_borrowed'])     ? (int)$stats['total_borrowed'] : 0;
    $returned = isset($stats['total_returned'])     ? (int)$stats['total_returned'] : 0;
    $overdue  = isset($stats['total_overdue'])      ? (int)$stats['total_overdue'] : 0;
    $dueSoon  = isset($stats['total_due_soon'])     ? (int)$stats['total_due_soon'] : 0;
    
    if ($total > 0) {
        $stats['borrowed_pct'] = round(($borrowed / $total) * 100, 1);
        $stats['returned_pct'] = round(($returned / $total) * 100, 1);
        $stats['overdue_pct']  = round(($overdue / $total) * 100, 1);
        $stats['due_soon_pct'] = round(($dueSoon / $total) * 100, 1);
    } else {
        $stats['borrowed_pct'] = 0;
        $stats['returned_pct'] = 0;
        $stats['overdue_pct']  = 0;
        $stats['due_soon_pct'] = 0;
    }
    
    return $stats;
}
?>
