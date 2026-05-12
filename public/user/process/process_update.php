<?php
session_start();

$configPath = $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Still not found. Check if the folder is 'config' or 'Config': " . $configPath);
}

if (isset($_POST['csrf_token'])) { 
    $uid = $_SESSION['authUser']['user_id'];
    $fullName = trim($_POST['fullName']);
    $username = trim($_POST['username']);
    $emailAddress = trim($_POST['emailAddress']);
    $street = trim($_POST['street']);
    $barangay = trim($_POST['barangay']);
    $city = trim($_POST['city']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

   
    if (!empty($password)) {
        if ($password !== $confirmPassword) {
            $_SESSION['message'] = "Passwords do not match!";
            $_SESSION['code'] = "error";
            header("Location: Profile.php");
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

    if ($stmt->execute()) {
        $_SESSION['authUser']['fullName'] = $fullName;
        $_SESSION['authUser']['username'] = $username;
        
        $_SESSION['message'] = "Account updated successfully!";
        $_SESSION['code'] = "success";
    } else {
        $_SESSION['message'] = "Error updating account: " . $conn->error;
        $_SESSION['code'] = "error";
    }
    
    header("Location: /kmkdt-Library/public/user/Profile");
    exit();
}