<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_USER_SESSION'); 
    session_set_cookie_params(0, '/'); 
    session_start();                        
}

require_once dirname(__DIR__, 1) . '/userController.php'; 

// Ensure connection exists
if (!isset($conn) || !$conn) {
    die("System Error: Database connection could not be established.");
}

$uid = $_SESSION['user_id'] ?? $_SESSION['authUser']['id'] ?? $_SESSION['authUser']['user_id'] ?? null;

if (!$uid) {
    header("Location: ../../../public/login?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['message'] = "Security token mismatch.";
        $_SESSION['code'] = "error";
        header("Location: ../../../public/user/profile");
        exit();
    }

    $fullName     = trim($_POST['fullName'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $emailAddress = trim($_POST['emailAddress'] ?? '');
    $street       = trim($_POST['street'] ?? '');
    $barangay     = trim($_POST['barangay'] ?? '');
    $city         = trim($_POST['city'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirmPass  = $_POST['confirmPassword'] ?? '';

    // Mandatory Fields
    if (empty($fullName) || empty($username) || empty($emailAddress)) {
        $_SESSION['message'] = "Full Name, Username, and Email are required.";
        $_SESSION['code'] = "error";
        header("Location: ../../../public/user/profile");
        exit();
    }

    // Email Validation
    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format. Must be a complete address (e.g., name@domain.com).";
        $_SESSION['code'] = "error";
        header("Location: ../../../public/user/profile");
        exit();
    }

    //  Duplicate Prevention
    $checkQuery = "SELECT username, emailAddress FROM user WHERE (username = ? OR emailAddress = ?) AND id != ? LIMIT 1";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ssi", $username, $emailAddress, $uid);
    $checkStmt->execute();
    
    if ($checkStmt->get_result()->num_rows > 0) {
        $_SESSION['message'] = "The username or email is already taken by another account.";
        $_SESSION['code'] = "error";
        header("Location: ../../../public/user/profile");
        exit();
    }

    //  Update Execution
    if (!empty($password)) {
        if ($password !== $confirmPass) {
            $_SESSION['message'] = "Passwords do not match!";
            $_SESSION['code'] = "error";
            header("Location: ../../../public/user/profile");
            exit();
        }
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET fullName=?, username=?, emailAddress=?, street=?, barangay=?, city=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $hashed, $uid);
    } else {
        $sql = "UPDATE user SET fullName=?, username=?, emailAddress=?, street=?, barangay=?, city=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $uid);
    }

    //  Final Execution Check
    if ($stmt && $stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Update session state
            $_SESSION['authUser']['fullName']     = $fullName;
            $_SESSION['authUser']['username']     = $username;
            $_SESSION['authUser']['emailAddress'] = $emailAddress;
            $_SESSION['authUser']['street']       = $street;
            $_SESSION['authUser']['barangay']     = $barangay;
            $_SESSION['authUser']['city']         = $city;

            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['code'] = "success";
        } else {
            // Triggered if the user submitted the form without changing any data
            $_SESSION['message'] = "No changes were made to your profile.";
            $_SESSION['code'] = "info"; 
        }
    } else {
        $_SESSION['message'] = "Update failed: " . ($stmt->error ?? $conn->error);
        $_SESSION['code'] = "error";
    }

    header("Location: ../../../public/user/profile");
    exit();
}
?>