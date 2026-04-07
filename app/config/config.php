<?php
// Ensure this is the VERY first line. No spaces above this!
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$database = "library";

// We use $GLOBALS to make sure other files can see it regardless of scope
$GLOBALS['conn'] = new mysqli($servername, $username, $password, $database);

if ($GLOBALS['conn']->connect_error) {
    die("Connection failed: " . $GLOBALS['conn']->connect_error);
}

// Create a local reference so existing code doesn't break
$conn = $GLOBALS['conn'];
?>