<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fixed: Include full .php extension for technical accuracy
require_once dirname(__DIR__, 1) . '/userController.php'; 

// 1. Authentication Check using absolute path to prevent stacking
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../public/login?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    // CSRF Protection for Midterm security requirements
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security validation failed.");
    }

    $uid = $_SESSION['user_id'];
    $fullName = trim($_POST['fullName']);
    $username = trim($_POST['username']);
    $emailAddress = trim($_POST['emailAddress']);
    $street = trim($_POST['street']);
    $barangay = trim($_POST['barangay']);
    $city = trim($_POST['city']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // 2. Password & SQL Logic
    if (!empty($password)) {
        if ($password !== $confirmPassword) {
            $_SESSION['message'] = "Passwords do not match!";
            $_SESSION['code'] = "error";
            header("Location: ../../../public/user/Profile");
            exit();
        }
        
        // Midterm Requirement: Secure Hashing
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET fullName=?, username=?, emailAddress=?, street=?, barangay=?, city=?, password=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $hashed, $uid);
    } else {
        // Update without changing the password for a better UX
        $sql = "UPDATE user SET fullName=?, username=?, emailAddress=?, street=?, barangay=?, city=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $uid);
    }

    // 3. Execution & Session Sync
    if ($stmt->execute()) {
        // Sync session data so the Sidebar reflects changes immediately
        $_SESSION['authUser']['fullName'] = $fullName;
        $_SESSION['authUser']['username'] = $username;
        
        $_SESSION['message'] = "Account updated successfully!";
        $_SESSION['code'] = "success"; 
    } else {
        $_SESSION['message'] = "Error updating account: " . $conn->error;
        $_SESSION['code'] = "error";
    }
    
    // Correct absolute redirect to avoid "public/kmkdt-Library/public/" duplication
    header("Location: ../../../public/user/Profile");
    exit();
}