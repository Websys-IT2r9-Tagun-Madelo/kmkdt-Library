<?php
if (ob_get_level() == 0) ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = dirname(__DIR__); 
$configPath = $basePath . '/config/config.php'; 
$controllerPath = $basePath . '/controller/adminController.php';

// 1. Strict Authorization Guard Check
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