<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'Connect.php';
$quiz_code = isset($_GET['quiz_code']) ? $conn->real_escape_string($_GET['quiz_code']) : '';
$attempt_id = isset($_GET['attempt_id']) ? $conn->real_escape_string($_GET['attempt_id']) : '';
if (empty($quiz_code) || empty($attempt_id)) {
    header('HTTP/1.0 400 Bad Request');
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$quiz_query = "SELECT QuizID, Name FROM Quiz WHERE QuizCode = '$quiz_code'";
$quiz_result = $conn->query($quiz_query);

if ($quiz_result->num_rows == 0) {
    header('HTTP/1.0 404 Not Found');
    echo json_encode(['error' => 'Quiz not found']);
    exit;
}

$quiz_data = $quiz_result->fetch_assoc();
$quiz_name = $quiz_data['Name'];
$quiz_id = $quiz_data['QuizID'];

$question_query = "
    SELECT 
        q.QuestionID, 
        q.Name AS question, 
        q.Type,
        A.UserAnswer AS user_answer,
        A.CorrectAnswer AS correct_answer,
        A.IsCorrect AS is_correct
    FROM 
        Questions q
    LEFT JOIN 
        Attempts A ON A.QuestionID = q.QuestionID
    Join 
        Results R ON R.AttemptID=A.AttemptID
    WHERE 
        q.QuizID =" . $quiz_id ." And R.AttemptID=".$attempt_id. " ORDER BY q.QuestionID";

$question_result = $conn->query($question_query);

if (!$question_result) {
    // Handle the error
    echo json_encode(['error' => 'Database query failed: ' . $conn->error]);
    exit;
}
$questions = $question_result->fetch_all(MYSQLI_ASSOC);
$total_questions = count($questions);
$correct_answers = array_sum(array_column($questions, 'is_correct'));
$incorrect_answers = $total_questions - $correct_answers;

$score_query = "SELECT Score, TimeSpent FROM Results WHERE QuizCode = '$quiz_code' AND AttemptID = '$attempt_id'";
$score_result = $conn->query($score_query);
$score_data = $score_result->fetch_assoc();
$response = [
    'quiz_name' => $quiz_name,
    'total_questions' => $total_questions,
    'correct_answers' => $correct_answers,
    'incorrect_answers' => $incorrect_answers,
    'score' => $score_data['Score'],
    'time_spent' => $score_data['TimeSpent'],
    'questions' => $questions
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>