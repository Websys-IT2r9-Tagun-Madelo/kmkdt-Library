<?php
// 1. Session Identity Handling (Cookie-safe cross-login initialization)
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
    // 2. Resolve Paths cleanly using explicit absolute directory matching
    $controllerDir = dirname(__DIR__); 
    $appDir        = dirname($controllerDir); // Points to /app/
    
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

    // Auto-detect dynamic parameter bindings from either profile forms or ledger queues
    $loanId = $_POST['loan_id'] ?? $_POST['id'] ?? $_POST['request_id'] ?? null;
    $action = $_POST['action'] ?? 'approve'; // Default to user initialization workflow if unspecified

    if (!$loanId) {
        throw new Exception("invalid_action");
    }

    $loanId = intval($loanId);
    $action = strtolower(trim($action));

    // 3. Look up real-time record variables before shifting table structures
    $checkSql = "SELECT user_id, status, penalty FROM borrowing_history WHERE id = ? LIMIT 1";
    $loan     = null;

    if ($stmt = $conn->prepare($checkSql)) {
        $stmt->bind_param("i", $loanId);
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
    $currentStatus = strtolower(trim($loan['status']));
    $penaltyAmount = floatval($loan['penalty']);

    // --- REJECT ACTION ---
    if ($action === 'reject') {
        if ($currentStatus !== 'payment_pending') {
            throw new Exception("invalid_state");
        }

        // Revert status safely back to standard active timeline bounds
        $revertSql = "UPDATE borrowing_history SET status = 'borrowed' WHERE id = ?";
        $success   = false;

        if ($stmt = $conn->prepare($revertSql)) {
            $stmt->bind_param("i", $loanId);
            if ($stmt->execute()) {
                $success = true;
            }
            $stmt->close();
        }
        
        if (!$success) {
            throw new Exception("update_failed");
        }

        $_SESSION['message'] = "Payment verification request rejected. Fine balance remains active.";
        $_SESSION['code']    = "danger";
        
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "/kmkdt-Library/public/admin/payHistory"));
        exit();
    }

    // --- APPROVE ACTION (Dual Identity Workflow Connection) ---
    elseif ($action === 'approve') {
        
        // Context A: If request is pending, finalize clearance logs and extend timelines (Admin Action)
        if ($currentStatus === 'payment_pending') {
            if (!function_exists('approvePenaltyPayment')) {
                throw new Exception("processor_missing");
            }

            $result = approvePenaltyPayment($conn, $loanId);

            if (!isset($result['code']) || $result['code'] !== 'success') {
                throw new Exception("processing_failed");
            }

            $_SESSION['message'] = $result['message'];
            $_SESSION['code']    = "success";
        } 
        
        // Context B: Initialize request tracking parameters (User Profile Action)
        elseif (in_array($currentStatus, ['borrowed', 'overdue'], true)) {
            if ($penaltyAmount <= 0) {
                throw new Exception("no_penalty_due");
            }

            $requestSql = "UPDATE borrowing_history SET status = 'payment_pending' WHERE id = ?";
            $success    = false;

            if ($stmt = $conn->prepare($requestSql)) {
                $stmt->bind_param("i", $loanId);
                if ($stmt->execute()) {
                    $success = true;
                }
                $stmt->close();
            }

            if (!$success) {
                throw new Exception("update_failed");
            }

            $_SESSION['success'] = "Payment request has been submitted successfully for admin approval.";
            
            header("Location: /kmkdt-Library/public/user/profile?status=payment_submitted");
            exit();
        } 
        
        // Context C: Unrecognizable structural status block boundaries
        else {
            throw new Exception("not_pending_payment");
        }
        
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? "/kmkdt-Library/public/admin/payHistory"));
        exit();
    } else {
        throw new Exception("unknown_action");
    }

} catch (Exception $e) {
    $errorKey = $e->getMessage();
    
    $friendlyMessages = [
        'system_files_missing' => 'Core application configuration engines could not be verified.',
        'database_offline'     => 'The central library service framework link is offline.',
        'invalid_action'       => 'The processing form request parameters could not be validated.',
        'query_failed'         => 'An error occurred while communicating with backend transaction tables.',
        'request_not_found'    => 'This transaction entry was not found or was already updated.',
        'invalid_state'        => 'This item fine tracking status is not marked as pending verification.',
        'update_failed'        => 'System database storage engines failed to save structural status shifts.',
        'processor_missing'    => 'The core transaction management function components are missing.',
        'no_penalty_due'       => 'This loan does not have an active penalty balance to settle.',
        'not_pending_payment'  => 'This item status tracking index is not currently awaiting verification.',
        'processing_failed'    => 'The transaction logic execution engine encountered a failure.',
        'unknown_action'       => 'The submitted system operational task is unrecognized.'
    ];

    $displayError = $friendlyMessages[$errorKey] ?? 'An unexpected system execution barrier occurred.';
    
    die("
        <div style='font-family: sans-serif; padding: 20px; max-width: 500px; margin: 50px auto; border: 1px solid #f5c2c7; background-color: #f8d7da; color: #842029; border-radius: 4px;'>
            <h4 style='margin-top: 0;'>Payment Process Interrupted</h4>
            <p>" . htmlspecialchars($displayError) . "</p>
            <a href='javascript:history.back()' style='color: #842029; font-weight: bold; text-decoration: underline;'>Return to Previous Page</a>
        </div>
    ");
}