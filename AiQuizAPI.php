<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

include 'Connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$topic = $conn->real_escape_string($data['topic']);

function generateQuizCode($conn) 
{
    do 
    {
        $quizCode = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $result = $conn->query("SELECT QuizCode FROM Quiz WHERE QuizCode = '$quizCode'");
    } while ($result->num_rows > 0);
    return $quizCode;
}

$apiKey = "YOUR_GEMINI_API_KEY_HERE";
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$refinedPrompt = "
Generate a quiz based on the following topic: \"$topic\"

Please provide the following information in JSON format:
{
  \"title\": \"An engaging title for the quiz\",
  \"description\": \"A brief description of the quiz content\",
  \"timeRequired\": 3,
  \"questions\": [
    {
      \"text\": \"Question text here\",
      \"type\": \"multiple-choice-single\",
      \"options\": [\"Option 1\", \"Option 2\", \"Option 3\", \"Option 4\"],
      \"correctAnswer\": 1,
      \"positiveMarks\": 1,
      \"negativeMarks\": 0,
      \"timeLimit\": 30
    },
    // ... 9 more questions ...
  ]
}

Ensure that:
1. The title is catchy and relevant to the topic.
2. The description provides a concise overview of what the quiz covers.
3. The timeRequired is a reasonable estimate for completing all questions, in minutes.
4. Provide exactly 10 questions.
5. Each question has a clear and concise text.
6. The \"type\" is always \"multiple-choice-single\".
7. Assign appropriate positive and negative marks, and time limits for each question based on its difficulty.
8. Keep the questions challenging and engaging.
9. Always ask relevant and not straightforward questions.
10. Avoid asking questions that can be easily answered by a simple Google search.
11. Do not use quotes and slashes in the JSON keys or values.
12. no not give any backticks in the response just give the json response as normal text with any backticks or formatting
Please provide only the JSON output, without any additional text or explanation.
";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'contents' => [['parts' => [['text' => $refinedPrompt]]]]
]));

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
curl_close($ch);

$jsonResponse = json_decode($response, true);
$generatedContent = json_decode($jsonResponse['candidates'][0]['content']['parts'][0]['text'], true);
echo json_encode($generatedContent);

// Generate a unique quiz code
$quizCode = generateQuizCode($conn);
$quizName = $generatedContent['title'];
$quizDescription = $generatedContent['description'];
$totalTimeLimit = $generatedContent['timeRequired']; // Convert minutes to seconds
$sql = "INSERT INTO Quiz (QuizCode, Name, Description, TimeLimit) VALUES ('$quizCode', '$quizName', '$quizDescription', $totalTimeLimit)";
$conn->query($sql);
$quizId = $conn->insert_id;

// Insert questions into the database
for ($i = 0; $i < count($generatedContent['questions']); $i++) 
{
    $question = $generatedContent['questions'][$i];
    $questionText = $conn->real_escape_string($question['text']);
    $sql = "INSERT INTO Questions (QuizID, Name, Type, Marks, Negative_Marks, Time) VALUES ($quizId, '$questionText', '{$question['type']}', {$question['positiveMarks']}, {$question['negativeMarks']}, {$question['timeLimit']})";
    $conn->query($sql);
    $questionId = $conn->insert_id;

    for ($j = 0; $j < count($question['options']); $j++) {
        $option = $question['options'][$j];
        $isCorrect = ($j + 1 == $question['correctAnswer']) ? 1 : 0;
        $escapedOption = $conn->real_escape_string($option);
        $sql = "INSERT INTO Options_MCQ (QuestionID, Name, IsCorrect) VALUES ($questionId, '$escapedOption', $isCorrect)";
        $conn->query($sql);
    }
}

echo json_encode([
    'success' => true, 
    'quizCode' => $quizCode,
    'title' => $quizName,
    'description' => $quizDescription,
    'timeRequired' => $totalTimeLimit / 60,
    'quizData' => $generatedContent
]);

$conn->close();