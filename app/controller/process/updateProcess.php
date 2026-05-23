<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_USER_SESSION'); 
    session_set_cookie_params(0, '/'); 
    session_start();                     
}

require_once dirname(__DIR__, 1) . '/userController.php'; 


$uid = $_SESSION['user_id'] ?? $_SESSION['authUser']['id'] ?? $_SESSION['authUser']['user_id'] ?? null;

if (!$uid) {

    echo "Session Data: ";
    print_r($_SESSION); 
    die();

    header("Location: ../../../public/login?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Security validation failed.");
    }

    
    $fullName     = trim($_POST['fullName']);
    $username     = trim($_POST['username']);
    $emailAddress = trim($_POST['emailAddress']);
    $street       = trim($_POST['street']);
    $barangay     = trim($_POST['barangay']);
    $city         = trim($_POST['city']);
    $password     = $_POST['password'];
    $confirmPass  = $_POST['confirmPassword'];

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

    if ($stmt->execute()) {
        $_SESSION['authUser']['fullName'] = $fullName;
        $_SESSION['authUser']['username'] = $username;
        $_SESSION['authUser']['emailAddress'] = $emailAddress;
        $_SESSION['authUser']['street'] = $street;
        $_SESSION['authUser']['barangay'] = $barangay;
        $_SESSION['authUser']['city'] = $city;

        $_SESSION['message'] = "Profile updated successfully!";
        $_SESSION['code'] = "success"; 
    } else {
        $_SESSION['message'] = "Database error: " . $conn->error;
        $_SESSION['code'] = "error";
    }


    header("Location: ../../../public/user/profile");
    exit();
}