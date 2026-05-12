<?php
session_start();

$appPath = dirname(__DIR__);
$configPath = $appPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Config file not found at: " . $configPath);
}

if (!isset($_SESSION['authUser'])) {
    $_SESSION['message'] = "Please login to access the admin area.";
    $_SESSION['code'] = "warning";
    header("Location: /kmkdt-Library/public/login");
    exit();
}

if ($_SESSION['authUser']['userRole'] !== 'admin') {
    $_SESSION['message'] = "Access denied: Admins only.";
    $_SESSION['code'] = "error";
    header("Location: /kmkdt-Library/public/login");
    exit();
}
?>