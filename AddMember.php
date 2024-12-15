<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'Connect.php';
if (!isset($_SESSION['username']) || $_SESSION['loggedin'] != true) {
    header("location:index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!$conn)
        die(mysqli_connect_error());
    $groupcode = $_POST['Group_Code'];
    $members = (int) $_POST['memberCount'];
    $Query = "Select * From QuizGroups Where GroupCode='$groupcode' ";
    $result = mysqli_query($conn, $Query);
    $num = mysqli_num_rows($result);
    if ($num == 1) {
        $groupdata = mysqli_fetch_assoc($result);
        $error = "";
        for ($i = 1; $i <= $members; $i++) {
            $mail = $_POST['grp_member' . $i . '_email'];
            if (isset($_POST['grp_member' . $i . '_isAdmin']))
                $isAdmin = 1;
            else
                $isAdmin = 0;

            $Query = "Select * From User Where Email='$mail' ";
            $result = mysqli_query($conn, $Query);
            $num = mysqli_num_rows($result);
            if ($num == 0) {
                $error = $error . $mail . "<BR>";
            } else {
                $userdata = mysqli_fetch_assoc($result);
                $UserID = (int) $userdata['UserID'];
                $GroupID = (int) $groupdata['GroupID'];
                $Query = "SELECT * FROM GroupMembers WHERE UserID = $UserID AND GroupID = $GroupID";
                $result2 = mysqli_query($conn, $Query);
                $num2 = mysqli_num_rows($result2);
                if ($num2 == 0) {
                    $Query = "Insert Into GroupMembers Values('{$userdata['UserID']}','{$groupdata['GroupID']}','{$isAdmin}')";
                    $result = mysqli_query($conn, $Query);
                }
            }
        }
        if ($error == "")
            header("location:DashBoard.php");
    }
}
?>