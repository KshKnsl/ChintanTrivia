<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

c
$qqq = [];
$sql = "Select * from Quiz where QuizCode='$code' ";

$QuizDetails = mysqli_query($conn, $sql);
if (mysqli_num_rows($QuizDetails) > 0) {
  $row = mysqli_fetch_assoc($QuizDetails);

} else {
  print "No result found";
}

$qqq["title"] = $row["Name"];
$qqq["description"] = $row["Description"];
$qqq["timeLimit"] = $row["TimeLimit"];
$qqq["showAnswersImmediately"] = false;
$qqq["questions"] = array();


$QuizID = $row["QuizID"];


$sql = "select * from Questions where QuizID=$QuizID";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $q = [];
    $q["text"] = $row["Name"];
    $q["type"] = $row["Type"];
    $q["timeLimit"] = $row["Time"];
    $q["positiveMarks"] = $row["Marks"];
    $q["negativeMarks"] = $row["Negative_Marks"];
    $q["questionid"] = $row["QuestionID"];
    $QuestionID = $row["QuestionID"];
    switch ($row["Type"]) {
      case "multiple-choice-single":
      case "true-false":
        $options = [];

        $sql2 = "select * from Options_MCQ where QuestionID=$QuestionID";
        $result2 = mysqli_query($conn, $sql2);
        if (mysqli_num_rows($result2) > 0) {
          while ($row2 = mysqli_fetch_assoc($result2)) {
            array_push($options, $row2["Name"]);
            if ($row2["IsCorrect"] == 1) {
              $q["correctAnswer"] = $row2["Name"];
            }
          }

        }

        $q["options"] = $options;
        break;


      case "multiple-choice-multiple":
        $options = [];
        $correctAnswer = [];

        $sql2 = "select * from Options_MCQ where QuestionID=$QuestionID";
        $result2 = mysqli_query($conn, $sql2);
        if (mysqli_num_rows($result2) > 0) {
          while ($row2 = mysqli_fetch_assoc($result2)) {
            array_push($options, $row2["Name"]);
            if ($row2["IsCorrect"] == 1) {
              array_push($correctAnswer, $row2["Name"]);
            }
          }

        }

        $q["options"] = $options;
        $q["correctAnswer"] = $correctAnswer;
        break;

      case "fill-in-the-blank":

        $sql2 = "select * from Options_Fill where QuestionID=$QuestionID";
        $result2 = mysqli_query($conn, $sql2);
        if (mysqli_num_rows($result2) > 0) {
          $row2 = mysqli_fetch_assoc($result2);
          $q["correctAnswer"] = $row2["Answer"];

        }

        break;


    }

    array_push($qqq["questions"], $q);
  }

} else {
  print "No result found";
}

print json_encode($qqq);
?>