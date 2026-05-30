<?php
//  Session Identity Handling
if (session_status() === PHP_SESSION_NONE) {
    if (isset($_COOKIE['KMKDT_USER_SESSION'])) {
        session_name('KMKDT_USER_SESSION');
    } elseif (isset($_COOKIE['KMKDT_ADMIN_SESSION'])) {
        session_name('KMKDT_ADMIN_SESSION');
    }
    
    session_set_cookie_params(0, '/');
    session_start();
}

try {
    // Resolve Paths cleanly using explicit absolute directory matching
    $controllerDir = dirname(__DIR__); 
    $appDir        = dirname($controllerDir); 
    
    $configPath    = $appDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
    $adminCtrlPath = $controllerDir . DIRECTORY_SEPARATOR . 'adminController.php'; 
    $userCtrlPath  = $controllerDir . DIRECTORY_SEPARATOR . 'userController.php'; 

    // Prevent raw server path exposures inside the file system checks
    if (!file_exists($configPath) || !file_exists($adminCtrlPath) || !file_exists($userCtrlPath)) {
        throw new Exception("system_files_missing");
    }

    require_once $configPath; 
    require_once $adminCtrlPath; 
    require_once $userCtrlPath; 

    if (!isset($conn) || !$conn instanceof mysqli) {
        throw new Exception("database_offline");
    }

    $requestId = $_POST['request_id'] ?? null;
    $action    = $_POST['action'] ?? null;

    if (!$requestId || !$action) {
        throw new Exception("invalid_action");
    }

    $requestId = intval($requestId);
    $action    = strtolower(trim($action));

    // Look up request history state
    $checkSql = "SELECT user_id, status, book_id FROM borrowing_history WHERE id = ? LIMIT 1";
    $loan     = null;

    if ($stmt = $conn->prepare($checkSql)) {
        $stmt->bind_param("i", $requestId);
        if ($stmt->execute()) {
            $loan = $stmt->get_result()->fetch_assoc();
        }
        $stmt->close();
    } else {
        throw new Exception("query_failed");
    }

    if (!$loan) {
        throw new Exception("request_not_found");
    }

    $targetUserId  = (int)$loan['user_id'];
    $actualBookId  = (int)$loan['book_id'];
    $currentStatus = strtolower(trim($loan['status']));

    // Reject action
    if ($action === 'reject') {
        if ($currentStatus !== 'pending_return') {
            throw new Exception("invalid_state");
        }

        $revertSql = "UPDATE borrowing_history SET status = 'borrowed' WHERE id = ?";
        $success   = false;

        if ($stmt = $conn->prepare($revertSql)) {
            $stmt->bind_param("i", $requestId);
            if ($stmt->execute()) {
                $success = true;
            }
            $stmt->close();
        }
        
        if (!$success) {
            throw new Exception("update_failed");
        }

        $_SESSION['success'] = "Return request has been rejected.";
        
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "/"));
        exit();
    }

    // Approve action 
    elseif ($action === 'approve') {
        
        // Context A: If the entry is ALREADY pending_return, process inventory check-in (Admin action)
        if ($currentStatus === 'pending_return') {
            if (!function_exists('processBookReturn')) {
                throw new Exception("processor_missing");
            }

            $success = processBookReturn($conn, $targetUserId, $actualBookId, true);

            if (!$success) {
                throw new Exception("processing_failed");
            }

            $_SESSION['success'] = "Book return approved and successfully added back to inventory.";
        } 
        
        // Context B: If status is borrowed or overdue, initialize the return request (User action)
        elseif (in_array($currentStatus, ['borrowed', 'overdue'], true)) {
            $requestSql = "UPDATE borrowing_history SET status = 'pending_return' WHERE id = ?";
            $success    = false;

            if ($stmt = $conn->prepare($requestSql)) {
                $stmt->bind_param("i", $requestId);
                if ($stmt->execute()) {
                    $success = true;
                }
                $stmt->close();
            }

            if (!$success) {
                throw new Exception("update_failed");
            }

            $_SESSION['success'] = "Return request has been submitted successfully for admin approval.";
        } 
        
        // Context C: State is unrecognizable or already processed
        else {
            throw new Exception("not_pending_return");
        }
        
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "/"));
        exit();
    } else {
        throw new Exception("unknown_action");
    }

} catch (Exception $e) {
    $errorKey = $e->getMessage();
    
    // Completely consumer-friendly array dictionary definitions
    $friendlyMessages = [
        'system_files_missing' => 'Core application configuration engines could not be verified.',
        'database_offline'     => 'The central library service framework link is offline.',
        'invalid_action'       => 'The processing form request parameters could not be validated.',
        'query_failed'         => 'An error occurred while communicating with backend transaction tables.',
        'request_not_found'    => 'This transaction entry was not found or was already updated.',
        'invalid_state'        => 'This item tracking status is not marked as pending a return.',
        'update_failed'        => 'System database storage engines failed to save structural status shifts.',
        'processor_missing'    => 'The transaction management engine failed to load required components.',
        'not_pending_return'   => 'This item status tracking index is not currently awaiting manual validation.',
        'processing_failed'    => 'The system transaction update failed to finish execution safely.',
        'unknown_action'       => 'The submitted system operational task is unrecognized.'
    ];

    $displayError = $friendlyMessages[$errorKey] ?? 'An unexpected system execution barrier occurred.';
    
    
    die("
        <div style='font-family: sans-serif; padding: 20px; max-width: 500px; margin: 50px auto; border: 1px solid #f5c2c7; background-color: #f8d7da; color: #842029; border-radius: 4px;'>
            <h4 style='margin-top: 0;'>Return Update Interrupted</h4>
            <p>" . htmlspecialchars($displayError) . "</p>
            <a href='javascript:history.back()' style='color: #842029; font-weight: bold; text-decoration: underline;'>Return to Previous Page</a>
        </div>
    ");
}