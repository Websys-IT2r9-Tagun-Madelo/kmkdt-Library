<?php
session_start();

// 1. Path Logic: Locating the config file
$appPath = dirname(__DIR__);
$configPath = $appPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Config file not found at: " . $configPath);
}

// --- LOGIN/SIGN IN ---
if (isset($_POST['loginbutton'])) {

    global $conn;
    if (!isset($conn) && isset($GLOBALS['conn'])) {
        $conn = $GLOBALS['conn'];
    }

    if (!isset($conn) || $conn === null) {
        die("Database connection variable (\$conn) is missing. Check your config.php file.");
    }

    // Sanitize input: Using 'username' to match your updated login form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Query: Search strictly for matching Username
    $loginQuery = "SELECT * FROM user WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($loginQuery);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            // Verify hashed password
            if (password_verify($password, $data['password'])) {
                // Regenerate session ID for security during login
                session_regenerate_id(true);

                $_SESSION['authUser'] = [
                    'user_id' => $data['id'],
                    'fullName' => $data['fullName'],
                    'username' => $data['username'],
                    'userRole' => $data['role']
                ];

                $_SESSION['message'] = "Welcome, " . $data['fullName'];
                $_SESSION['code'] = "success";

                // Role-based redirection
                if ($data['role'] === 'admin') {
                    header("Location: /kmkdt-Library/public/admin/index");
                } else {
                    header("Location: /kmkdt-Library/public/user/index");
                }
                exit();
            }
        }
    }

    $_SESSION['message'] = "Invalid Username or Password";
    $_SESSION['code'] = "error";
    header("Location: /kmkdt-Library/public/login");
    exit();
}


// --- SIGN UP/Registration---
if (isset($_POST['registerbutton'])) {
    global $conn;
    if (!isset($conn) && isset($GLOBALS['conn'])) {
        $conn = $GLOBALS['conn'];
    }

    // 1. Capture and Trim Inputs
    $fullName = trim($_POST['fullName']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['emailAddress']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirmPassword'];
    $street   = trim($_POST['street']);
    $barangay = trim($_POST['barangay']);
    $city     = trim($_POST['city']);

    // Store inputs in session so the form can "remember" them if there's an error
    $_SESSION['old_input'] = $_POST;

    // --- CHECK FOR EMPTY FIELDS ---
    if (empty($fullName) || empty($username) || empty($email) || empty($password) || empty($street) || empty($barangay) || empty($city)) {
        $_SESSION['message'] = "Please fill in all details before signing up.";
        $_SESSION['code'] = "error";
        header("Location: ../../public/sign-up");
        exit();
    }

    // 2. Validate Email Format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid Email format!";
        $_SESSION['code'] = "error";
        header("Location: ../../public/sign-up");
        exit();
    }

    // 3. Check for Duplicates
    $checkUser = "SELECT id FROM user WHERE emailAddress = ? OR username = ? LIMIT 1";
    $stmt = $conn->prepare($checkUser);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['message'] = "Username or Email already taken.";
        $_SESSION['code'] = "error";
        header("Location: ../../public/sign-up");
        exit();
    }

    // 4. Password Match Check
    if ($password !== $confirm) {
        $_SESSION['message'] = "Passwords do not match!";
        $_SESSION['code'] = "error";
        header("Location: ../../public/sign-up");
        exit();
    }

    // 5. If everything is OK, proceed to Insert
    // (Sanitize right before DB insert)
    function generate_uuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    $uuid = generate_uuid();
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';

    $insertQuery = "INSERT INTO user (uuid, fullName, username, emailAddress, password, street, barangay, city, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insStmt = $conn->prepare($insertQuery);
    $insStmt->bind_param("sssssssss", $uuid, $fullName, $username, $email, $hashed, $street, $barangay, $city, $role);

    if ($insStmt->execute()) {
        unset($_SESSION['old_input']); // Clear the "remembered" data on success
        $_SESSION['message'] = "Registration successful! Please login.";
        $_SESSION['code'] = "success";
        header("Location: ../../public/login");
    } else {
        $_SESSION['message'] = "Database Error: " . $conn->error;
        $_SESSION['code'] = "error";
        header("Location: ../../public/sign-up");
    }
    exit();
}
// --- 1. REQUEST RESET LINK (From Login Modal) ---
if (isset($_POST['forgotPasswordButton'])) {
    global $conn;
    
    // Sanitize the input to match your Midterm security requirements
    $email = mysqli_real_escape_string($conn, trim($_POST['emailAddress']));

    // Check if the user exists in your MySQL 'user' table
    $query = "SELECT id FROM user WHERE emailAddress = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a secure 32-byte token and set 1-hour expiration
        $token = bin2hex(random_bytes(32));
        $expire = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Update the user record with the token
        $updateQuery = "UPDATE user SET reset_token = ?, token_expire = ? WHERE emailAddress = ?";
        $upStmt = $conn->prepare($updateQuery);
        $upStmt->bind_param("sss", $token, $expire, $email);
        
        if ($upStmt->execute()) {
            $_SESSION['message'] = "Reset link generated.";
            $_SESSION['code'] = "success";
            
            // Absolute path redirect to prevent the blank page issue
            header("Location: /kmkdt-Library/public/reset-password?token=" . $token);
            exit(); 
        }
    } else {
        $_SESSION['message'] = "Email address not found.";
        $_SESSION['code'] = "error";
        header("Location: /kmkdt-Library/public/login");
        exit();
    }
}

// --- 2. FINALIZE PASSWORD RESET (From Reset Page) ---
if (isset($_POST['updatePasswordButton'])) {
    global $conn;
    $token = $_POST['token'];
    $newPass = $_POST['newPassword'];
    $confirmPass = $_POST['confirmPassword'];

    // Validate that passwords match
    if ($newPass !== $confirmPass) {
        $_SESSION['message'] = "Passwords do not match!";
        $_SESSION['code'] = "error";
        header("Location: /kmkdt-Library/public/reset-password?token=" . $token);
        exit();
    }

    // Hash the password (using PASSWORD_DEFAULT for your technical assessment)
    $hashed = password_hash($newPass, PASSWORD_DEFAULT);

    // Update password and CLEAR the token so it cannot be used again
    $sql = "UPDATE user SET password = ?, reset_token = NULL, token_expire = NULL WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed, $token);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Password updated! You can now login.";
        $_SESSION['code'] = "success";
        header("Location: /kmkdt-Library/public/login");
    } else {
        $_SESSION['message'] = "Database error. Reset failed.";
        $_SESSION['code'] = "error";
        header("Location: /kmkdt-Library/public/login");
    }
    exit();
}