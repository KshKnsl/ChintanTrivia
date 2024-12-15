<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'Connect.php';
session_start();
$hashedgroupcode = $_GET['code'] ?? '';
$groupcode = '';
$Query = "Select GroupID,GroupCode From QuizGroups";
$result = mysqli_query($conn, $Query);
while ($data = mysqli_fetch_assoc($result)) {
    if (password_verify($data['GroupCode'], $hashedgroupcode)) {
        $groupcode = $data["GroupCode"];
        break;
    }
}
$Query = "Select * From GroupMembers Where GroupID=" . $data['GroupID'] . " And UserID=" . $_SESSION['userid'] . " And IsAdmin=1";
$result = mysqli_query($conn, $Query);
$num = mysqli_num_rows($result);
if ($num != 0) {
    $Query = "Delete From QuizGroups Where GroupID=" . $data['GroupID'];
    $result = mysqli_query($conn, $Query);
} else {
    $Query = "Delete From GroupMembers Where GroupID=" . $data['GroupID'] . " And UserID=" . $_SESSION['userid'];
    $result = mysqli_query($conn, $Query);
}
header("location:DashBoard.php");
?>