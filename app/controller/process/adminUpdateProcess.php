<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_ADMIN_SESSION'); 
    session_set_cookie_params(0, '/');  
    session_start();                     
}

$configPath = dirname(__DIR__, 2) . '/config/config.php';
if (file_exists($configPath)) {
    require_once $configPath;
} else {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/kmkdt-Library/app/config/config.php';
}

$uid = $_SESSION['user_id'] ?? $_SESSION['authUser']['id'] ?? $_SESSION['authUser']['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!$uid) {
        $_SESSION['message'] = "Unauthorized system attempt detected.";
        $_SESSION['code'] = "error";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../../views/admin/profile.php'));
        exit();
    }

    if ($_POST['action'] === 'updateProfileEverything') {
        $_SESSION['old_input'] = $_POST;

        // Get the ID of the user being edited (if it's a hidden input, use it; otherwise fallback to $uid)
        $targetId = isset($_POST['id']) ? (int)$_POST['id'] : $uid;

        $fullName     = trim($_POST['fullName']);
        $username     = trim($_POST['username']);
        $emailAddress = trim($_POST['emailAddress']);
        $password     = $_POST['password'];
        $confirmPass  = $_POST['confirmPassword'];

        // --- LOGIC: Is this the owner? ---
        $isOwner = ($uid == $targetId);

        // 1. Mandatory Fields & Email Validation
        if (empty($fullName) || empty($username) || empty($emailAddress)) {
            $_SESSION['message'] = "Full Name, Username, and Email fields are mandatory.";
            $_SESSION['code'] = "error";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Invalid email format.";
            $_SESSION['code'] = "error";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // 2. Duplicate Prevention
        $checkQuery = "SELECT username FROM user WHERE (username = ? OR emailAddress = ?) AND id != ? LIMIT 1";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, "ssi", $username, $emailAddress, $targetId);
        mysqli_stmt_execute($checkStmt);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($checkStmt))) {
            $_SESSION['message'] = "Username or Email already taken.";
            $_SESSION['code'] = "error";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // 3. Dynamic Query Builder
        if ($isOwner) {
            // --- OWNER CAN UPDATE EVERYTHING ---
            $street = trim($_POST['street']);
            $barangay = trim($_POST['barangay']);
            $city = trim($_POST['city']);

            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $query = "UPDATE user SET fullName=?, username=?, emailAddress=?, street=?, barangay=?, city=?, password=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $hashed, $targetId);
            } else {
                $query = "UPDATE user SET fullName=?, username=?, emailAddress=?, street=?, barangay=?, city=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssssssi", $fullName, $username, $emailAddress, $street, $barangay, $city, $targetId);
            }
        } else {
            // --- ADMIN CAN ONLY UPDATE PROFILE ---
            if (!empty($password)) {
                $hashed = password_hash($password, PASSWORD_BCRYPT);
                $query = "UPDATE user SET fullName=?, username=?, emailAddress=?, password=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssssi", $fullName, $username, $emailAddress, $hashed, $targetId);
            } else {
                $query = "UPDATE user SET fullName=?, username=?, emailAddress=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssi", $fullName, $username, $emailAddress, $targetId);
            }
        }

        // 4. Execution
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['code'] = "success";
        } else {
            $_SESSION['message'] = "Update failed: " . mysqli_stmt_error($stmt);
            $_SESSION['code'] = "error";
        }
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>