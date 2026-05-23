<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_USER_SESSION');
    session_set_cookie_params(0, '/');
    session_start();
}

// Get the current page URL path to prevent loops
$currentUrl = $_SERVER['REQUEST_URI'];

// 1. Only enforce authentication if they are NOT on the login or signup page
if (!isset($_SESSION['authUser'])) {
    if (strpos($currentUrl, '/public/login') === false && strpos($currentUrl, '/public/signUp') === false) {
        header("Location: /kmkdt-Library/public/login");
        exit();
    }
}

// 2. Load the controller safely after the route verification
require_once dirname(__DIR__, 1) . '/controller/userController.php';

// 3. Only attempt to assign the ID if the session array index is valid
$currentUserId = isset($_SESSION['authUser']['user_id']) ? $_SESSION['authUser']['user_id'] : null;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}