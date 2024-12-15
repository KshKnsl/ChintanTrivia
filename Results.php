<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json");
$rawInput = file_get_contents('php://input');
$inputData = json_decode($rawInput, true);
if ($inputData === null) {
    echo json_encode(['error' => 'No data received or invalid JSON']);
    exit;
}
$score = isset($inputData['score']) ? (int) $inputData['score'] : null;
$QuizCode = $inputData['QuizCode'] ?? null;
$UserID = $inputData['UserID'] ?? null;
$timespent = isset($inputData['timespent']) ? (int) $inputData['timespent'] : null;
$GroupID = isset($inputData['GroupID']) && (int) $inputData['GroupID'] > 0 ? (int) $inputData['GroupID'] : null;
$userAnswers = isset($inputData['userAnswers']) ? $inputData['userAnswers'] : null;
$correctAnswers = isset($inputData['correctAnswers']) ? $inputData['correctAnswers'] : null;
$questionids = isset($inputData['questionids']) ? $inputData['questionids'] : null;
$response = [
    'receivedData' => [
        'score' => $score,
        'QuizCode' => $QuizCode,
        'UserID' => $UserID,
        'timespent' => $timespent,
        'GroupID' => $GroupID,
        'userAnswers' => $userAnswers,
        'correctAnswers' => $correctAnswers,
        'questionids' => $questionids
    ],
    'message' => "Data received successfully"
];
echo json_encode($response);
include "Connect.php";
if ($conn) {
    if ($GroupID === null)
        $query = "INSERT INTO Results (QuizCode, UserID, Score, TimeSpent, Submission, GroupID) VALUES ('$QuizCode', '$UserID', $score, $timespent, NOW(), NULL)";
    else
        $query = "INSERT INTO Results (QuizCode, UserID, Score, TimeSpent, Submission, GroupID) VALUES ('$QuizCode', '$UserID', $score, $timespent, NOW(), $GroupID)";

    $conn->query($query);

    $currAttemptID = $conn->query("select max(AttemptID) from Results")->fetch_assoc()['max(AttemptID)'];

    for ($i = 0; $i < count($correctAnswers); $i++) {
        $questionId = $questionids[$i];
        $userAnswer = isset($userAnswers[$i]) ? (is_array($userAnswers[$i]) ? implode(', ', $userAnswers[$i]) : $userAnswers[$i]) : null;
        $correctAnswer = isset($correctAnswers[$i]) ? (is_array($correctAnswers[$i]) ? implode(', ', $correctAnswers[$i]) : $correctAnswers[$i]) : null;
        $isCorrect = ($userAnswer === $correctAnswer) ? 1 : 0;

        var_dump($currAttemptID, $questionId, $userAnswer, $correctAnswer, $isCorrect); // Added var_dump

        $query2 = "INSERT INTO Attempts (AttemptID, QuestionID, UserAnswer, CorrectAnswer, IsCorrect) VALUES ($currAttemptID, $questionId, '$userAnswer', '$correctAnswer', $isCorrect)";
        $conn->query($query2);
    }
}
echo json_encode($response);
?>