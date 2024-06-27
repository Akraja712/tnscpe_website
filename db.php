<?php
$servername = "localhost";
$username = "u743445510_tnscpe";
$password = "Tnscpe@2024";
$database = "u743445510_tnscpe";

define('DOMAIN_URL', 'https://tnscpe.graymatterworks.com/'); /* chnage to your domain here - don't forget to add forward slash at the end of the URL like this "/" */

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

