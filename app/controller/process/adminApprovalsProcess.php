<?php
if (session_status() === PHP_SESSION_NONE) {
    if (isset($_COOKIE['KMKDT_ADMIN_SESSION'])) {
        session_name('KMKDT_ADMIN_SESSION');
    } elseif (isset($_COOKIE['KMKDT_USER_SESSION'])) {
        session_name('KMKDT_USER_SESSION');
    }
    
    session_set_cookie_params(0, '/');
    session_start();
}

$redirectUrl = "/kmkdt-Library/public/admin/catalog";

try {
    // 1. INPUT VALIDATION: Ensure request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("invalid_request_method");
    }

    // 2. PATH RESOLUTION & COMPONENT SECURITY
    $controllerDir = dirname(__DIR__); 
    $appDir        = dirname($controllerDir); 
    
    $configPath    = $appDir . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
    $adminCtrlPath = $controllerDir . DIRECTORY_SEPARATOR . 'adminController.php'; 
    $userCtrlPath  = $controllerDir . DIRECTORY_SEPARATOR . 'userController.php'; 

    if (!file_exists($configPath) || !file_exists($adminCtrlPath) || !file_exists($userCtrlPath)) {
        throw new Exception("system_files_missing");
    }

    require_once $configPath; 
    require_once $adminCtrlPath; 
    require_once $userCtrlPath; 

    // 3. DATABASE CONNECTION INTEGRITY CHECK
    if (!isset($conn) || !$conn instanceof mysqli) {
        throw new Exception("database_offline");
    }
    if ($conn->connect_error) {
        throw new Exception("database_connection_error");
    }

    // 4. PARAMETER VALIDATION & SANITIZATION
    $requestId = $_POST['request_id'] ?? null;
    $action    = $_POST['action'] ?? null;

    if ($requestId === null || empty($action)) {
        throw new Exception("missing_parameters");
    }

    $requestId = intval($requestId);
    $action    = strtolower(trim($action));

    if ($requestId <= 0) {
        throw new Exception("invalid_id_format");
    }

    if (!in_array($action, ['approve', 'reject'], true)) {
        throw new Exception("unknown_action");
    }

    // 5. DATABASE QUERY VALIDATION: Fetching history securely
    $checkSql = "SELECT user_id, status, book_id FROM borrowing_history WHERE id = ? LIMIT 1";
    $loan     = null;

    if ($stmt = $conn->prepare($checkSql)) {
        $stmt->bind_param("i", $requestId);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception("execution_failed_history");
        }
        $loan = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        throw new Exception("query_prepare_failed");
    }

    // Verify record exists
    if (!$loan) {
        throw new Exception("request_not_found");
    }

    $targetUserId  = (int)$loan['user_id'];
    $actualBookId  = (int)$loan['book_id'];
    $currentStatus = strtolower(trim($loan['status']));

    // Verify state match before making updates
    if ($currentStatus !== 'pending_return') {
        throw new Exception("not_pending_return");
    }


    // --- ACTION: REJECT ---
    if ($action === 'reject') {
        $revertSql = "UPDATE borrowing_history SET status = 'borrowed' WHERE id = ?";
        $success   = false;

        if ($stmt = $conn->prepare($revertSql)) {
            $stmt->bind_param("i", $requestId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $success = true;
                } else {
                    throw new Exception("no_rows_changed");
                }
            } else {
                throw new Exception("execution_failed_reject");
            }
            $stmt->close();
        } else {
            throw new Exception("query_prepare_failed");
        }
        
        if (!$success) {
            throw new Exception("update_failed");
        }

        $_SESSION['success'] = "Return request has been rejected.";
        header("Location: {$redirectUrl}");
        exit();
    }

    // --- ACTION: APPROVE ---
    elseif ($action === 'approve') {
        if (!function_exists('processBookReturn')) {
            throw new Exception("processor_missing");
        }

        // processBookReturn contains its own internal database transactions
        $success = processBookReturn($conn, $targetUserId, $actualBookId, true);

        if (!$success) {
            throw new Exception("processing_failed");
        }

        $_SESSION['success'] = "Book return approved and successfully added back to inventory.";
        header("Location: {$redirectUrl}");
        exit();
    }

} catch (Exception $e) {
    // 6. ERROR MAPPING: Translate code exception tags into consumer-friendly language
    $errorKey = $e->getMessage();
    
    $friendlyMessages = [
        'invalid_request_method'   => 'Invalid form submission mechanism detected.',
        'system_files_missing'     => 'Library application files could not be safely loaded.',
        'database_offline'         => 'The resource controller database link is offline.',
        'database_connection_error'=> 'Failed to connect to the central data node.',
        'missing_parameters'       => 'Required processing action details were missing.',
        'invalid_id_format'        => 'The structural record identifier structure is invalid.',
        'unknown_action'           => 'The requested catalog action variation is unrecognized.',
        'query_prepare_failed'     => 'System database compilation interface error encountered.',
        'execution_failed_history' => 'Failed to pull verification telemetry data.',
        'execution_failed_reject'  => 'Database execution encountered a write failure during rejection.',
        'request_not_found'        => 'This specific tracking record was not found or already modified.',
        'not_pending_return'       => 'This tracking state is no longer in a pending return condition.',
        'no_rows_changed'          => 'The database record is already updated to this position.',
        'update_failed'            => 'Internal transaction write updates failed to finalize.',
        'processor_missing'        => 'Core business workflow engines failed dependencies resolution.',
        'processing_failed'        => 'The system transaction processing update failed to finish execution safely.'
    ];

    // Catch-all safety guard to protect structural filepaths or database queries from leaking
    $displayError = $friendlyMessages[$errorKey] ?? 'An unexpected system data friction occurred during updates.';

    $_SESSION['error'] = "Action Failed: " . $displayError;
    header("Location: {$redirectUrl}");
    exit();
}