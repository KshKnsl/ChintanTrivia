<?php
include 'Connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$quiz_id = $_GET['quiz_id'] ?? '';
if (!$quiz_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing quiz ID']);
    exit;
}

$query = "SELECT u.Name AS name, r.score 
          FROM Results r 
          JOIN user u ON r.UserID = u.UserID
          WHERE r.QuizCode = '" . mysqli_real_escape_string($conn, $quiz_id) . "'
          GROUP BY u.Name 
          ORDER BY r.score DESC 
          LIMIT 5";

$result = mysqli_query($conn, $query);

$leaderboard = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $leaderboard[] = $row;
    }
}

// Return JSON response
header('Content-Type: application/json'); // Correct content type
echo json_encode($leaderboard);
exit;
?>