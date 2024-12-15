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
        if(!$conn){
            die(mysqli_connect_error());
        }
        $groupcode = $_POST['Group_Code'];
        $Query = "Select * From QuizGroups Where GroupCode='$groupcode' ";
        $result = mysqli_query($conn, $Query);
        $data=mysqli_fetch_assoc($result);
        $num=mysqli_num_rows($result);
        if($num==1){
            $groupID=(int) $data['GroupID'];
            $Query="Select * From GroupMembers Where UserID=".$_SESSION['userid']." And GroupID=".$groupID;
            $result2=mysqli_query($conn,$Query);
            $num2=mysqli_num_rows($result2);
            if($num2==0){
                $Query = "Insert Into GroupMembers Values('{$_SESSION['userid']}','{$data['GroupID']}','0') ";
                $result = mysqli_query($conn, $Query);
            }
        }
        header("location:DashBoard.php");
    }
?>