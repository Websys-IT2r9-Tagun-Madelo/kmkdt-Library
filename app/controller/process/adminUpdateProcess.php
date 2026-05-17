<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/'); 
    session_start();
}

// 1. ARCHITECTURAL ROOT RESOLUTION
$configPath = dirname(__DIR__, 2) . '/config/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
}

// Capture authenticated context
$uid = $_SESSION['user_id'] ?? $_SESSION['authUser']['id'] ?? $_SESSION['authUser']['user_id'] ?? null;

// =========================================================================
// ADMIN PROFILE & PASSWORD COUPLING CONTROLLER ENGINE (EXTENDED PROCESSING)
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if (!$uid) {
        $_SESSION['message'] = "Unauthorized system attempt detected. Please sign in again.";
        $_SESSION['code'] = "error";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../../views/admin/profile.php'));
        exit();
    }

    if ($_POST['action'] === 'updateProfileEverything') {
        $fullName     = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
        $username     = isset($_POST['username']) ? trim($_POST['username']) : '';
        $emailAddress = isset($_POST['emailAddress']) ? trim($_POST['emailAddress']) : ''; 
        $street       = isset($_POST['street']) ? trim($_POST['street']) : '';
        $barangay     = isset($_POST['barangay']) ? trim($_POST['barangay']) : '';
        $city         = isset($_POST['city']) ? trim($_POST['city']) : '';
        $password     = isset($_POST['password']) ? $_POST['password'] : '';
        $confirmPass  = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

        if (empty($fullName) || empty($username) || empty($emailAddress)) {
            $_SESSION['message'] = "Full Name, Username, and Email fields are mandatory.";
            $_SESSION['code'] = "error";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        if (!isset($conn) || !$conn) {
            $_SESSION['message'] = "Database connectivity failure: Connection instance missing.";
            $_SESSION['code'] = "error";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        if (!empty($password)) {
            if ($password !== $confirmPass) {
                $_SESSION['message'] = "Validation mismatch: Password verification fields do not match.";
                $_SESSION['code']    = "error";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }
            
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $query = "UPDATE user SET fullName = ?, username = ?, emailAddress = ?, street = ?, barangay = ?, city = ?, password = ? WHERE id = ?";
            $stmt  = mysqli_prepare($conn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $hashedPassword, $uid);
            }
        } else {
            $query = "UPDATE user SET fullName = ?, username = ?, emailAddress = ?, street = ?, barangay = ?, city = ? WHERE id = ?";
            $stmt  = mysqli_prepare($conn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $uid);
            }
        }

        if ($stmt) {
            if (mysqli_stmt_execute($stmt)) {
                
                // =========================================================================
                // CRITICAL HOTFIX: SESSION SYNCHRONIZATION ENGINE
                // =========================================================================
                // Update basic root session attributes instantly
                if (isset($_SESSION['fullName'])) $_SESSION['fullName'] = $fullName;
                if (isset($_SESSION['emailAddress'])) $_SESSION['emailAddress'] = $emailAddress;
                if (isset($_SESSION['username'])) $_SESSION['username'] = $username;

                // Update deep multi-dimensional authorization storage arrays (e.g., authUser or auth_user)
                if (isset($_SESSION['authUser']) && is_array($_SESSION['authUser'])) {
                    $_SESSION['authUser']['fullName']     = $fullName;
                    $_SESSION['authUser']['username']     = $username;
                    $_SESSION['authUser']['emailAddress'] = $emailAddress;
                    $_SESSION['authUser']['street']       = $street;
                    $_SESSION['authUser']['barangay']     = $barangay;
                    $_SESSION['authUser']['city']         = $city;
                }
                
                if (isset($_SESSION['auth_user']) && is_array($_SESSION['auth_user'])) {
                    $_SESSION['auth_user']['fullName']     = $fullName;
                    $_SESSION['auth_user']['username']     = $username;
                    $_SESSION['auth_user']['emailAddress'] = $emailAddress;
                    $_SESSION['auth_user']['street']       = $street;
                    $_SESSION['auth_user']['barangay']     = $barangay;
                    $_SESSION['auth_user']['city']         = $city;
                }

                $_SESSION['message'] = "Profile details successfully synchronized!";
                $_SESSION['code']    = "success";
            } else {
                $_SESSION['message'] = "Database error: " . mysqli_stmt_error($stmt);
                $_SESSION['code']    = "error";
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['message'] = "Internal statement architectural error: " . mysqli_error($conn);
            $_SESSION['code']    = "error";
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}