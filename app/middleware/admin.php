<?php
if (ob_get_level() == 0) ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_ADMIN_SESSION');
    session_set_cookie_params(0, '/');
    session_start();
}

$basePath = dirname(__DIR__); 
$configPath = $basePath . '/config/config.php'; 
$controllerPath = $basePath . '/controller/adminController.php';


// Check if the user is currently looking at the login or signup page
$currentUrl = $_SERVER['REQUEST_URI'];
if (strpos($currentUrl, '/public/login') !== false || strpos($currentUrl, '/public/signUp') !== false) {
    return; 
}

// Strict Authorization Guard Check
if (!isset($_SESSION['authUser']) || !is_array($_SESSION['authUser'])) {
    $_SESSION['message'] = "Please login to access the admin area.";
    $_SESSION['code'] = "warning";
    header("Location: /kmkdt-Library/public/login");
    exit();
}

if (!isset($_SESSION['authUser']['userRole']) || $_SESSION['authUser']['userRole'] !== 'admin') {
    $_SESSION['message'] = "Access denied: Admins only.";
    $_SESSION['code'] = "error";
    header("Location: /kmkdt-Library/public/login");
    exit();
}
?>