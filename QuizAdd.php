<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'Connect.php';
function generateQuizCode($conn)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code_length = 6;
    do {
        $code = '';
        for ($i = 0; $i < $code_length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        $result = mysqli_query($conn, "SELECT QuizCode FROM Quiz WHERE QuizCode = '$code'");
    } while (mysqli_num_rows($result) > 0);
    return $code;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        echo json_encode(["success" => false, "message" => "Invalid JSON data received."]);
        exit;
    }

    $quizCode = generateQuizCode($conn);

    $isActive = 1;
    $groupID = $data['groupID'];
    if ($groupID !== "NULL" && $groupID !== '') {
        $groupID = (int) $groupID;
    } else {
        $groupID = "NULL";
    }

    $startDate = !empty($data['startDate']) ? $data['startDate'] : date('Y-m-d');
    $startTime = !empty($data['startTime']) ? $data['startTime'] : '00:00:00';
    $dueDate = !empty($data['dueDate']) ? $data['dueDate'] : date('Y-m-d', strtotime('+1 week'));
    $dueTime = !empty($data['dueTime']) ? $data['dueTime'] : '23:59:59';

    $sqlQuiz = "INSERT INTO Quiz (Name, Description, TimeLimit, QuizCode, AdminID, GroupID, IsActive, ShowAnswer, StartDate, StartTime, DueDate, DueTime) 
                    VALUES (
                        '" . mysqli_real_escape_string($conn, $data['title']) . "', 
                        '" . mysqli_real_escape_string($conn, $data['description']) . "', 
                        " . (int) $data['timeLimit'] . ", 
                        '" . mysqli_real_escape_string($conn, $quizCode) . "', 
                        '" . mysqli_real_escape_string($conn, $data['adminID']) . "', 
                        " . $groupID . ", 
                        $isActive, 
                        " . (int) $data['showAnswersImmediately'] . ",
                        '" . mysqli_real_escape_string($conn, $startDate) . "',
                        '" . mysqli_real_escape_string($conn, $startTime) . "',
                        '" . mysqli_real_escape_string($conn, $dueDate) . "',
                        '" . mysqli_real_escape_string($conn, $dueTime) . "'
                    )";

    mysqli_query($conn, $sqlQuiz);
    $quizID = mysqli_insert_id($conn);
    foreach ($data['questions'] as $question) {
        $sqlQuestion = "INSERT INTO Questions (QuizID, Name, Marks, Negative_Marks, Time, Type) 
                            VALUES (
                                $quizID, 
                                '" . mysqli_real_escape_string($conn, $question['text']) . "', 
                                " . (int) $question['positiveMarks'] . ", 
                                " . (int) $question['negativeMarks'] . ", 
                                " . (int) $question['timeLimit'] . ", 
                                '" . mysqli_real_escape_string($conn, $question['type']) . "'
                            )";

        mysqli_query($conn, $sqlQuestion);

        $questionID = mysqli_insert_id($conn);

        if (in_array($question['type'], ['multiple-choice-single', 'multiple-choice-multiple', 'true-false'])) {
            foreach ($question['options'] as $index => $option) {
                $isCorrect = in_array($index + 1, (array) $question['correctAnswer']) ? 1 : 0;
                $sqlOption = "INSERT INTO Options_MCQ (QuestionID, Name, IsCorrect) 
                                  VALUES ($questionID, '" . mysqli_real_escape_string($conn, $option) . "', $isCorrect)";

                mysqli_query($conn, $sqlOption);
            }
        } elseif ($question['type'] === 'fill-in-the-blank') {
            $sqlOptionFill = "INSERT INTO Options_Fill (QuestionID, Answer) 
                                  VALUES ($questionID, '" . mysqli_real_escape_string($conn, $question['correctAnswer']) . "')";

            mysqli_query($conn, $sqlOptionFill);
        }
    }
    echo json_encode(["success" => true, "message" => "Quiz created successfully!", "quizCode" => $quizCode]);
}
?>