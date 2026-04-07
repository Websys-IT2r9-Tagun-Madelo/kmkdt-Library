<?php
session_start();

// Locating the config file
$appPath = dirname(__DIR__);
$configPath = $appPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

if (file_exists($configPath)) {
    include_once($configPath);
} else {
    die("Config file not found at: " . $configPath);
}

if (isset($_POST['logoutButton'])) {
    unset($_SESSION['authUser']);
    unset($_SESSION['user_id']);
    unset($_SESSION['userRole']);

    session_destroy();

    header("Location: /kmkdt-Library/public/login");
    exit();
}

