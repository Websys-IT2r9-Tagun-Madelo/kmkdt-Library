<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$basePath = dirname(__DIR__); 
$configPath = $basePath . '/config/config.php'; // This file MUST define $conn
$controllerPath = $basePath . '/controller/adminController.php';

if (file_exists($configPath)) {
    include_once($configPath); 
}

if (file_exists($controllerPath)) {
    require_once($controllerPath);
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