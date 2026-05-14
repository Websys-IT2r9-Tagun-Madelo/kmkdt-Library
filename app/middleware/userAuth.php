<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__, 1) . '/controller/userController.php';

if (!isset($_SESSION['authUser'])) {
    header("Location: /kmkdt-Library/public/login");
    exit();
}

$currentUserId = $_SESSION['authUser']['user_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}