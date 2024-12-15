<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'Connect.php';
session_start();
$Query = "Delete From User Where UserID=" . $_SESSION['userid'];
$result = mysqli_query($conn, $Query);
header("location:LogOut.php");
?>