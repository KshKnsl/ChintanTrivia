<?php
$hostname = "localhost";
$password = "";
$username = "root";
$database = "chintantrivia";
$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Connection Error: " . mysqli_connect_error());
}
?>