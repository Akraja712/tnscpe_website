<?php
$servername = "localhost";
$username = "u743445510_tnscpe";
$password = "Tnscpe@2024";
$database = "u743445510_tnscpe";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

