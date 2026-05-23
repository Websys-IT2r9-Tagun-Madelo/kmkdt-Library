<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_USER_SESSION');
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Strict']);
    session_start();
}

// 1. Identify if the current page is public
$currentUrl = $_SERVER['REQUEST_URI'];
$isPublicPage = (strpos($currentUrl, '/public/login') !== false || 
                 strpos($currentUrl, '/public/signUp') !== false);

// 2. Strict Authentication Guard
// If the user is NOT logged in AND they are trying to access a restricted page
if (!isset($_SESSION['authUser']) || empty($_SESSION['authUser'])) {
    if (!$isPublicPage) {
        // Redirect guests to login
        header("Location: /kmkdt-Library/public/login");
        exit();
    }
} else {
    // Optional: If a LOGGED-IN user tries to visit login/signup, kick them to dashboard
    if ($isPublicPage) {
        header("Location: /kmkdt-Library/public/user/profile"); // Or your dashboard
        exit();
    }
}

// 3. Load the controller only after verifying the user is allowed to be here
require_once dirname(__DIR__, 1) . '/controller/userController.php';


$currentUserId = $_SESSION['authUser']['user_id'] ?? $_SESSION['authUser']['id'] ?? null;


if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}