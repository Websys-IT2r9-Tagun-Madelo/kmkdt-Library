<?php

date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$database = "library";


$GLOBALS['conn'] = new mysqli($servername, $username, $password, $database);

if ($GLOBALS['conn']->connect_error) {
    die("Connection failed: " . $GLOBALS['conn']->connect_error);
}


$conn = $GLOBALS['conn'];
?>