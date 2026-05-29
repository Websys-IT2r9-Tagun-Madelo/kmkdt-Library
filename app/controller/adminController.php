<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_ADMIN_SESSION'); 
    session_start();
}


$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
if (file_exists($configPath)) {
    require_once($configPath);
} else {
    die("Configuration file not found at: " . $configPath);
}


if (!isset($conn) || !$conn) {
    die("Database connection failed.");
}


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


// AJAX REQUESTS (GET)
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


// FORM ACTIONS (POST)

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
        
        // --- CREATE ---
        if ($_POST['action'] === 'create') {
            $username = trim($_POST['username']);
            $email = trim($_POST['emailAddress']);

            // Validate Email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                redirect("Please enter a valid email address (e.g., name@domain.com).", "error");
            }

            // Check for existing user
            $check = $conn->prepare("SELECT id FROM user WHERE username = ? OR emailAddress = ?");
            $check->bind_param("ss", $username, $email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                redirect("Username or Email is already in use.", "error");
            }

            $stmt = $conn->prepare("INSERT INTO user (fullName, username, emailAddress, role, street, barangay, city, password, dateCreated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $hash = password_hash(!empty($_POST['password']) ? $_POST['password'] : 'Library123!', PASSWORD_DEFAULT);
            $stmt->bind_param("ssssssss", $_POST['fullName'], $username, $email, $_POST['role'], $_POST['street'], $_POST['barangay'], $_POST['city'], $hash);
            
            if ($stmt->execute()) redirect("Member created successfully!", "success");
            else redirect("Database error: " . $stmt->error, "error");
        }

        // --- UPDATE ---
        elseif ($_POST['action'] === 'update') {
            $username = trim($_POST['username']);
            $email = trim($_POST['emailAddress']);
            $fullName = trim($_POST['fullName']);
            $role = $_POST['role'];
            $id = $_POST['id'] ?? null; 

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                redirect("Please enter a valid email address.", "error");
            }

            // 1. Duplicate Check
            $check = $conn->prepare("SELECT id FROM user WHERE (LOWER(username) = LOWER(?) OR LOWER(emailAddress) = LOWER(?)) AND id != ? LIMIT 1");
            $check->bind_param("ssi", $username, $email, $id);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                redirect("That username or email is already taken by another user.", "error");
            }

            $stmt = $conn->prepare("UPDATE user SET fullName = ?, emailAddress = ?, role = ?, username = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $fullName, $email, $role, $username, $id);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    redirect("Profile updated successfully (Location profile preserved).", "success");
                } else {
                    redirect("No changes were made to the profile.", "info");
                }
            } else {
                redirect("Database error: " . $stmt->error, "error");
            }
        }
        // --- DELETE ---
        elseif ($_POST['action'] === 'delete') {
            $email = $_POST['email'] ?? null;

            if (!$email) {
                redirect("Error: No email provided for deletion.", "error");
            }

            $stmt = $conn->prepare("DELETE FROM user WHERE emailAddress = ?");
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    redirect("User deleted successfully.", "warning");
                } else {
                    redirect("User not found.", "error");
                }
            } else {
                redirect("Delete failed: " . $stmt->error, "error");
            }
        }
    }
}
    
// Data Queries
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

function getRecentActivity($conn) {
    // Computes status logic directly inside SQL via conditional CASE loops
    $sql = "SELECT h.*, u.fullName, b.title,
                   CASE 
                       WHEN h.status != 'returned' AND h.due_date < NOW() THEN 1 
                       ELSE 0 
                   END AS is_overdue
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
    $currentDate = date('Y-m-d');
    $sql = "SELECT 
                h.id, u.fullName, b.title, h.borrowed_at, h.due_date, h.renewal_count,
                CASE 
                    WHEN h.status = 'returned' THEN 'Returned'
                    WHEN h.status = 'overdue' OR h.due_date < '$currentDate' THEN 'Overdue'
                    WHEN h.status = 'borrowed' AND h.due_date <= DATE_ADD('$currentDate', INTERVAL 2 DAY) THEN 'Due Soon'
                    ELSE 'Borrowed'
                END AS status
            FROM borrowing_history h
            LEFT JOIN user u ON h.user_id = u.id 
            LEFT JOIN books b ON h.book_id = b.id
            ORDER BY h.id DESC";

    $result = mysqli_query($conn, $sql);
    return ($result) ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
}


function getCirculationStats($conn) {
    $currentDate = date('Y-m-d');
    $query = "SELECT 
                SUM(CASE WHEN status = 'borrowed' AND due_date > '$currentDate' AND due_date <= DATE_ADD('$currentDate', INTERVAL 3 DAY) THEN 1 ELSE 0 END) AS total_due_soon,
                SUM(CASE WHEN status = 'borrowed' AND (due_date > DATE_ADD('$currentDate', INTERVAL 3 DAY) OR due_date = '$currentDate') THEN 1 ELSE 0 END) AS total_borrowed,
                SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) AS total_returned,
                SUM(CASE WHEN status = 'overdue' OR (status = 'borrowed' AND due_date < '$currentDate') THEN 1 ELSE 0 END) AS total_overdue,
                COUNT(*) AS total_transactions
            FROM borrowing_history";
              
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Circulation Stats Query Failed: " . mysqli_error($conn));
    }
    
    $stats = mysqli_fetch_assoc($result);
    
    // Ensure we default to 0 if the query returns NULL for any count
    $total    = (int)($stats['total_transactions'] ?? 0);
    $borrowed = (int)($stats['total_borrowed'] ?? 0);
    $returned = (int)($stats['total_returned'] ?? 0);
    $overdue  = (int)($stats['total_overdue'] ?? 0);
    $dueSoon  = (int)($stats['total_due_soon'] ?? 0);
    
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
function getPenaltyPaymentsHistory($conn) {
    $query = "SELECT pp.id, pp.amount_paid, pp.paid_at, u.fullName, u.username, 
                     IFNULL(b.title, 'System Fine Adjustment') as book_title
              FROM penalty_payments pp
              INNER JOIN user u ON pp.user_id = u.id
              LEFT JOIN borrowing_history bh ON pp.loan_id = bh.id
              LEFT JOIN books b ON bh.book_id = b.id
              ORDER BY pp.paid_at DESC";

    $payments = [];
    $result = mysqli_query($conn, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $payments[] = $row;
        }
    }

    return $payments;
}

function getLibraryMetrics($conn) {
    $currentDate = date('Y-m-d');
    $metrics = [
        'borrowedCount' => 0,
        'returnedCount' => 0,
        'overdueCount'  => 0,
        'catalogCount'  => 0
    ];

    // Aggregated counter queries
    $countsQuery = mysqli_query($conn, "
        SELECT 
            SUM(CASE WHEN status = 'borrowed' AND due_date >= '$currentDate' THEN 1 ELSE 0 END) as borrowed,
            SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned,
            SUM(CASE WHEN status = 'overdue' OR (status = 'borrowed' AND due_date < '$currentDate') THEN 1 ELSE 0 END) as overdue
        FROM borrowing_history
    ");

    if ($countsQuery) {
        $row = mysqli_fetch_assoc($countsQuery);
        $metrics['borrowedCount'] = (int)($row['borrowed'] ?? 0);
        $metrics['returnedCount'] = (int)($row['returned'] ?? 0);
        $metrics['overdueCount']  = (int)($row['overdue'] ?? 0);
    }

    $catalogQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM books");
    if ($catalogQuery) {
        $row = mysqli_fetch_assoc($catalogQuery);
        $metrics['catalogCount'] = (int)($row['total'] ?? 0);
    }

    return $metrics;
}

function getActiveBorrows($conn) {
    $currentDate = date('Y-m-d');
    $data = [];
    $query = "SELECT h.id, u.fullName, b.title, h.borrowed_at, h.due_date 
              FROM borrowing_history h
              JOIN user u ON h.user_id = u.id
              JOIN books b ON h.book_id = b.id
              WHERE h.status = 'borrowed' AND h.due_date >= '$currentDate' 
              ORDER BY h.borrowed_at DESC";
              
    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

function getHistoricalReturns($conn) {
    $data = [];
    $query = "SELECT h.id, u.fullName, b.title, h.borrowed_at, h.due_date 
              FROM borrowing_history history_tbl 
              LEFT JOIN borrowing_history h ON h.id = history_tbl.id
              JOIN user u ON h.user_id = u.id
              JOIN books b ON h.book_id = b.id
              WHERE h.status = 'returned' 
              ORDER BY h.id DESC";

    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}

function getOverdueViolations($conn) {
    $currentDate = date('Y-m-d');
    $data = [];
    $query = "SELECT h.id, u.fullName, b.title, h.due_date, 
                     GREATEST(0, DATEDIFF(CURDATE(), h.due_date)) as days_overdue
              FROM borrowing_history h
              JOIN user u ON h.user_id = u.id
              JOIN books b ON h.book_id = b.id
              WHERE h.status = 'overdue' OR (h.status = 'borrowed' AND h.due_date < '$currentDate') 
              ORDER BY days_overdue DESC";

    $result = mysqli_query($conn, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
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

function getProcessedAdminProfile($conn, $adminId) {
    $adminData = [];
    
    
    if (!$conn instanceof mysqli) {
        error_log("[Profile Error] Invalid or missing database connection object.");
        return getAdminFallbackStructure([]);
    }

    if (!$adminId) {
        error_log("[Profile Error] Attempted to fetch profile with an empty or null Admin ID.");
        return getAdminFallbackStructure([]);
    }

    
    try {
        $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE id = ? LIMIT 1");
        
        if ($stmt === false) {
            throw new Exception("Failed to prepare statement: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt, "i", $adminId);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to execute statement: " . mysqli_stmt_error($stmt));
        }

        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {
            throw new Exception("Failed to get statement result: " . mysqli_stmt_error($stmt));
        }

        $adminData = mysqli_fetch_assoc($result) ?: [];
        mysqli_stmt_close($stmt);

    } catch (Exception $e) {
        
        error_log("[Profile Database Exception] " . $e->getMessage());
        
        
        if (isset($stmt) && $stmt instanceof mysqli_stmt) {
            mysqli_stmt_close($stmt);
        }
        
        
        $adminData = []; 
    }

    
    return getAdminFallbackStructure($adminData);
}

function getAdminFallbackStructure($adminData) {
    $adminData = is_array($adminData) ? $adminData : [];

    $processed = [
        'raw'          => $adminData, 
        'fullName'     => !empty($adminData['fullName']) ? htmlspecialchars($adminData['fullName']) : 'Admin User',
        'emailAddress' => !empty($adminData['emailAddress']) ? htmlspecialchars($adminData['emailAddress']) : 'admin@example.com',
        'role'         => !empty($adminData['role']) ? htmlspecialchars($adminData['role']) : 'Administrator'
    ];

    
    $street   = !empty($adminData['street']) ? htmlspecialchars($adminData['street']) : '';
    $barangay = !empty($adminData['barangay']) ? htmlspecialchars($adminData['barangay']) : '';
    $city     = !empty($adminData['city']) ? htmlspecialchars($adminData['city']) : '';

    $addressArray = array_filter([$street, $barangay, $city]);
    $processed['address'] = !empty($addressArray) ? implode(', ', $addressArray) : 'N/A';

    return $processed;
}

function getAllCategorizedUsers($conn) {
    $categorized = [
        'admins'        => [],
        'standardUsers' => []
    ];

    if (!$conn instanceof mysqli) {
        error_log("[Members Error] Invalid or missing database connection object.");
        return $categorized;
    }

    try {
        $result = $conn->query("SELECT * FROM user ORDER BY dateCreated ASC");
        
        if ($result === false) {
            throw new Exception("Database query failed: " . $conn->error);
        }


        while ($row = $result->fetch_assoc()) {
            $role = !empty($row['role']) ? strtolower(trim($row['role'])) : 'user';
            
            if ($role === 'admin' || $role === 'administrator') {
                $categorized['admins'][] = $row;
            } else {
                $categorized['standardUsers'][] = $row;
            }
        }
        
        $result->free(); 

    } catch (Exception $e) {
        error_log("[Members Database Exception] " . $e->getMessage());
    }

    return $categorized;
}

function handleAdminLogoutRequest() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logoutButton'])) {
        $_SESSION = [];
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
}

function getDashboardAnalytics($conn) {
    
    $analytics = [
        'borrowedCount'    => 0,
        'overdueCount'     => 0,
        'catalogCount'     => 0,
        'totalRevenue'     => 0.00,
        'totalMembers'     => 0,
        'recentActivities' => []
    ];

    if (!$conn instanceof mysqli) {
        error_log("[Dashboard Engine Error] Missing or invalid database connection context.");
        return $analytics;
    }

    try {
        $currentDate = date('Y-m-d');
        $currentDateTime = date('Y-m-d H:i:s');

        // 1. Fetch live transaction data statuses
        $countsQuery = $conn->query("
            SELECT 
                SUM(CASE WHEN status = 'borrowed' AND due_date >= '$currentDate' THEN 1 ELSE 0 END) as borrowed,
                SUM(CASE WHEN status = 'overdue' OR (status = 'borrowed' AND due_date < '$currentDate') THEN 1 ELSE 0 END) as overdue
            FROM borrowing_history
        ");
        if ($countsQuery) {
            $row = $countsQuery->fetch_assoc();
            $analytics['borrowedCount'] = (int)($row['borrowed'] ?? 0);
            $analytics['overdueCount']  = (int)($row['overdue'] ?? 0);
            $countsQuery->free();
        }

        // 2. Fetch Catalog Inventory Total
        $catalogQuery = $conn->query("SELECT COUNT(*) as total FROM books");
        if ($catalogQuery) {
            $analytics['catalogCount'] = (int)($catalogQuery->fetch_assoc()['total'] ?? 0);
            $catalogQuery->free();
        }

        // 3. Fetch Total Revenue from Fines
        $revenueQuery = $conn->query("SELECT SUM(amount_paid) as total_fines FROM penalty_payments");
        if ($revenueQuery) {
            $analytics['totalRevenue'] = (float)($revenueQuery->fetch_assoc()['total_fines'] ?? 0.00);
            $revenueQuery->free();
        }

        // 4. Fetch Total Member Profiles Registry Count
        $memberQuery = $conn->query("SELECT COUNT(*) as total FROM user");
        if ($memberQuery) {
            $analytics['totalMembers'] = (int)($memberQuery->fetch_assoc()['total'] ?? 0);
            $memberQuery->free();
        }

        
        $sql = "SELECT h.*, u.fullName, b.title,
                       CASE 
                           WHEN h.status != 'returned' AND h.due_date < '$currentDateTime' THEN 1 
                           ELSE 0 
                       END AS is_overdue
                FROM borrowing_history h
                JOIN user u ON h.user_id = u.id
                JOIN books b ON h.book_id = b.id
                ORDER BY h.id DESC LIMIT 5";
        
        $activityResult = $conn->query($sql);
        if ($activityResult) {
            $analytics['recentActivities'] = $activityResult->fetch_all(MYSQLI_ASSOC) ?: [];
            $activityResult->free();
        }

    } catch (Exception $e) {
        error_log("[Dashboard Engine Exception] Compile failed: " . $e->getMessage());
    }

    return $analytics;
}