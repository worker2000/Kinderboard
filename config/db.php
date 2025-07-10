<?php
$host = "localhost";
$user = "kinderboard";
$pass = "R!a*G_hVNqkZz63L";
$db = "kinderboard";
$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    echo "Failed to connect: " . $mysqli->connect_error;
    exit();
}
?>
