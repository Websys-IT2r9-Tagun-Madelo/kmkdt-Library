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
// 2. Check if logged in
if (!isset($_SESSION['authUser'])) {
    $_SESSION['message'] = "Please login to access the admin area.";
    $_SESSION['code'] = "warning";
    header("Location: /kmkdt-Library/public/login");
    exit();
}

// 3. Check if user is an admin
// We check the 'userRole' key inside the 'authUser' session array
if ($_SESSION['authUser']['userRole'] !== 'admin') {
    $_SESSION['message'] = "Access denied: Admins only.";
    $_SESSION['code'] = "error";
    header("Location: /kmkdt-Library/public/login");
    exit();
}
?>