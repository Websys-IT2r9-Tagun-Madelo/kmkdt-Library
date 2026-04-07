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

// --- LOGIN LOGIC (Updated for Username Only) ---
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
// --- dont touch code above this line ---


// --- SIGN UP LOGIC ---
if (isset($_POST['registerbutton'])) {
    global $conn;
    // Ensure the connection is active
    if (!isset($conn) && isset($GLOBALS['conn'])) {
        $conn = $GLOBALS['conn'];
    }

    // Helper function for UUID
    function generate_uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    // 1. Sanitize and Capture Inputs
    $uuid = generate_uuid();
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['emailAddress']);
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'];
    $street = mysqli_real_escape_string($conn, $_POST['street']);
    $barangay = mysqli_real_escape_string($conn, $_POST['barangay']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $role = 'user';

    // 2. Validate Email Format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format!";
        $_SESSION['code'] = "error";
        header("Location: ../../public/sign-up");
        exit();
    }

    // 3. Check for Duplicates (Username or Email)
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

    // 5. Insert New User (9 Columns, 9 Placeholders)
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $insertQuery = "INSERT INTO user (uuid, fullName, username, emailAddress, password, street, barangay, city, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insStmt = $conn->prepare($insertQuery);

    // Bind 9 parameters: 9 "s" for strings
    $insStmt->bind_param("sssssssss", $uuid, $fullName, $username, $email, $hashed, $street, $barangay, $city, $role);

    if ($insStmt->execute()) {
        $_SESSION['message'] = "Registration successful! Please login.";
        $_SESSION['code'] = "success";
        header("Location: ../../public/login"); // Redirects to extensionless login
    } else {
        $_SESSION['message'] = "Database Error: " . $conn->error;
        $_SESSION['code'] = "error";
        header("Location: ../../public/sign-up");
    }
    exit();
}