<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['username']) || $_SESSION['loggedin'] != true) {
    header("location:index.php");
    exit;
}

include "Connect.php";
$query = "Select Distinct Q.QuizID As QuizID,Q.Name As Name From Quiz Q Join Results R Where R.UserID=" . $_SESSION['userid'] . " And R.QuizCode=Q.QuizCode" . " OR Q.AdminID=" . $_SESSION['userid'];
$quiz = mysqli_query($conn, $query);
$results = [];
$is_admin = false;
$check = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $check = 1;
    $quiz_id = isset($_POST['quiz_id']) ? ($_POST['quiz_id']) : null;
    $quizdata = mysqli_query($conn, "Select * From Quiz Where QuizID='$quiz_id' And AdminID=" . $_SESSION['userid']);
    if (mysqli_num_rows($quizdata) != 0) {
        $is_admin = true;
    }
    $result_query = $is_admin
        ? "SELECT r.*,r.AttemptID As AttemptID, u.Name as UserName, q.Name as QuizName 
           FROM Results r 
           JOIN User u ON r.UserID = u.UserID 
           JOIN Quiz q ON r.QuizCode = q.QuizCode
           WHERE 1=1"
        : "SELECT r.*,r.AttemptID As AttemptID, q.Name as QuizName 
           FROM Results r 
           JOIN Quiz q ON r.QuizCode = q.QuizCode
           WHERE r.UserID = " . $_SESSION['userid'];

    if ($quiz_id) {
        $result_query .= " AND q.QuizID = '$quiz_id'";
    }
    $result_query_result = mysqli_query($conn, $result_query);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChintanTrivia - Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen text-white">
    <header class="bg-indigo-800 shadow-lg fixed w-full z-10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center justify-between flex-wrap">
                <div class="flex items-center flex-shrink-0 text-white mr-6" onclick="window.location.href='index.php'">
                    <span class="font-extrabold text-xl opacity-100">ChintanTrivia</span>
                </div>
                <div class="block lg:hidden">
                    <button id="nav-toggle"
                        class="flex items-center px-3 py-2 border rounded text-indigo-200 border-indigo-400 hover:text-white hover:border-white">
                        <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <title>Menu</title>
                            <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
                        </svg>
                    </button>
                </div>
                <div id="nav-content" class="w-full block flex-grow lg:flex lg:items-center lg:w-auto hidden">
                    <div class="text-sm lg:flex-grow">
                        <a href="DashBoard.php"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Dashboard
                        </a>
                    </div>
                    <div>
                        <?php echo "<span class='mr-4'>👋  Welcome, " . $_SESSION['name'] . "</span>"; ?>
                        <a href="LogOut.php"
                            class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0">
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 pt-20 pb-12">
        <h1 class="text-3xl font-bold mb-8">Quiz Details And Attempts</h1>
        <form id="filter-form" method="POST" class="mb-8 bg-white p-6 rounded-lg shadow-lg" action="QuizDetails.php">
            <div class="flex flex-wrap -mx-2 mb-4">
                <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                    <label for="quiz_id" class="block text-gray-700 font-bold mb-2">Select Quiz:</label>
                    <select name="quiz_id" id="quiz_id"
                        class="block w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-black">
                        <option value="">All Quizzes</option>
                        <?php if (mysqli_num_rows($quiz) != 0) {
                            while ($row = mysqli_fetch_assoc($quiz)) { ?>
                                <option value="<?php echo $row['QuizID']; ?>">
                                    <?php echo htmlspecialchars($row['Name']); ?>
                                </option>
                            <?php }
                        } ?>
                        <?php if (mysqli_num_rows($quiz) != 0) {
                            while ($row = mysqli_fetch_assoc($quiz)) { ?>
                                <option value="<?php echo $row['QuizID']; ?>">
                                    <?php echo htmlspecialchars($row['Name']); ?>
                                </option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Apply Filters
                </button>
            </div>
        </form>
        <?php if ($is_admin) { ?>
            <h1 class="text-3xl font-bold mb-8">Quiz Details</h1>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <table class="w-full text-left table-auto">
                    <thead class="bg-gray-200 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">Quiz Name</th>
                            <th class="px-4 py-2">Quiz Description</th>
                            <th class="px-4 py-2">Quiz Code</th>
                            <th class="px-4 py-2">Time Limit</th>
                            <th class="px-4 py-2">Start Date</th>
                            <th class="px-4 py-2">Due Date</th>
                            <th class="px-4 py-2">Start Time</th>
                            <th class="px-4 py-2">Due Time</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600">
                        <?php if ($check == 1) { ?>
                            <?php while ($result = mysqli_fetch_assoc($quizdata)) { ?>
                                <tr class="hover:bg-gray-100">
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($result['Name']); ?></td>
                                    <td class="border px-4 py-2"><?php echo $result['Description']; ?></td>
                                    <td class="border px-4 py-2"><span id='quizCode' class='cursor-pointer underline'
                                            onclick='copyQuizCode()'><?php echo $result['QuizCode']; ?></span></td>
                                    <td class="border px-4 py-2"><?php echo $result['TimeLimit']; ?></td>
                                    <td class="border px-4 py-2"><?php echo $result['StartDate']; ?></td>
                                    <td class="border px-4 py-2"><?php echo $result['DueDate']; ?></td>
                                    <td class="border px-4 py-2"><?php echo $result['StartTime']; ?></td>
                                    <td class="border px-4 py-2"><?php echo $result['DueTime']; ?></td>
                                </tr>
                            <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
            <BR><BR><BR><BR>
        <?php } ?>
        <h1 class="text-3xl font-bold mb-8">Quiz Attempts</h1>
        <div class="bg-white rounded-lg shadow-lg max-h-96 overflow-y-auto">
            <table class="w-full text-left table-auto">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="px-4 py-2">Quiz Name</th>
                        <?php if ($is_admin): ?>
                            <th class="px-4 py-2">User Name</th>
                        <?php endif; ?>
                        <th class="px-4 py-2">Score</th>
                        <th class="px-4 py-2">Time Spent</th>
                        <th class="px-4 py-2">Submission Date</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    <?php if ($check == 1) { ?>
                        <?php while ($result = mysqli_fetch_assoc($result_query_result)) { ?>
                            <tr class="hover:bg-gray-100">
                                <td class="border px-4 py-2"><?php echo htmlspecialchars($result['QuizName']); ?></td>
                                <?php if ($is_admin): ?>
                                    <td class="border px-4 py-2"><?php echo htmlspecialchars($result['UserName']); ?></td>
                                <?php endif; ?>
                                <td class="border px-4 py-2"><?php echo $result['Score']; ?>%</td>
                                <td class="border px-4 py-2"><?php echo gmdate("H:i:s", $result['TimeSpent']); ?></td>
                                <td class="border px-4 py-2">
                                    <?php echo date('Y-m-d H:i:s', strtotime($result['Submission'])); ?>
                                </td>
                                <td class="border px-4 py-2">
                                    <button
                                        onclick="viewDetails('<?php echo $result['QuizCode']; ?>', '<?php echo $result['AttemptID']; ?>')"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-xs">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="pop" class="fixed z-10 inset-0 overflow-y-auto hidden">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="pop-title">
                        Quiz Details
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500" id="pop-content">
                            Loading...
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="generatePDF()" id="download-pdf"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Download PDF
                    </button>
                    <button type="button" onclick="closepop()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewDetails(quizCode, attemptId)
        {
            const pop = document.getElementById('pop');
            const popContent = document.getElementById('pop-content');
            pop.classList.remove('hidden');
            popContent.innerHTML = 'Loading...';

            fetch(`get_quiz_details.php?quiz_code=${quizCode}&attempt_id=${attemptId}`)
                .then(response => response.json())
                .then(data =>
                {
                    let content = `<h4 class="font-bold mb-2 text-lg">Quiz: ${data.quiz_name}</h4>`;
                    content += `<p class="mb-1">Total Questions: ${data.total_questions}</p>`;
                    content += `<p class="mb-1">Correct Answers: ${data.correct_answers}</p>`;
                    content += `<p class="mb-1">Incorrect Answers: ${data.incorrect_answers}</p>`;
                    content += `<p class="mb-1">Score: ${data.score}%</p>`;
                    content += `<p class="mb-4">Time Spent: ${new Date(data.time_spent * 1000).toISOString().substr(11, 8)}</p>`;

                    content += `<h5 class="font-bold mt-4 mb-2 text-lg">Question Details:</h5>`;
                    content += `<ul class="space-y-4">`;
                    data.questions.forEach((q, index) =>
                    {
                        content += `<li class="p-4 rounded-lg ${q.user_answer == q.correct_answer ? 'bg-green-50' : 'bg-red-50'}">
                    <p class="mb-2"><strong>Q: ${q.question}</strong></p>
                    <p class="mb-1">Your Answer: ${q.user_answer || 'No answer provided'}</p>
                    <p class="mb-1">Correct Answer: ${q.correct_answer}</p>
                    <p class="${q.user_answer == q.correct_answer ? 'text-green-600' : 'text-red-600'} font-medium">
                        ${q.user_answer == q.correct_answer ? 'Correct' : 'Incorrect'}
                    </p>
                </li>`;
                    });
                    content += `</ul>`;
                    popContent.innerHTML = content;
                })
                .catch(error =>
                {
                    console.error('Error:', error);
                    popContent.innerHTML = 'An error occurred while fetching quiz details.';
                });
        }
        function closepop()
        {
            document.getElementById('pop').classList.add('hidden');
        }

        // Navigation toggle functionality
        const navToggle = document.getElementById('nav-toggle');
        const navContent = document.getElementById('nav-content');
        navToggle.addEventListener('click', () =>
        {
            navContent.classList.toggle('hidden');
        });

        function generatePDF()
        {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                size: 'A4',
                orientation: 'p',
                compressPdf: true
            });

            // Header
            doc.setFont('Helvetica', 'bold');
            doc.setTextColor(75, 85, 99);
            doc.setFontSize(24);
            doc.text("ChintanTrivia", 105, 20, null, null, "center");

            const popContent = document.getElementById('pop-content');
            const quizTitle = popContent.querySelector('h4').innerText;

            // Quiz Title
            doc.setFont('Helvetica', 'normal');
            doc.setFontSize(18);
            doc.setTextColor(0, 0, 0);
            doc.text(quizTitle, 105, 40, null, null, 'center');

            // Summary
            doc.setFontSize(12);
            const summaryText = popContent.querySelectorAll('p');
            let yPos = 60;

            summaryText.forEach((p, index) =>
            {
                if (index < 3)
                {
                    doc.text(p.innerText, 20, yPos);
                    yPos += 10;
                }
            });

            yPos += 10;

            // Questions
            const questions = popContent.querySelectorAll('li');
            doc.setFontSize(11);
            questions.forEach((question, index) =>
            {
                if (yPos > 270)
                {
                    doc.addPage();
                    yPos = 20;
                }
                const questionText = question.querySelector('strong').innerText;
                const answers = question.querySelectorAll('p');
                doc.setFont('Helvetica', 'bold');
                doc.text(`Question ${index + 1}:`, 20, yPos);
                const questionLines = doc.splitTextToSize(questionText.replace('Q: ', ''), 170);
                doc.text(questionLines, 20, yPos + 5);
                yPos += 5 + (questionLines.length * 5);

                doc.setFont('Helvetica', 'normal');
                answers.forEach((answer, ansIndex) =>
                {
                    if (ansIndex > 0)
                    {
                        if (yPos > 270)
                        {
                            doc.addPage();
                            yPos = 20;
                        }
                        const answerLines = doc.splitTextToSize(answer.innerText, 170);
                        doc.setTextColor(answer.classList.contains('text-green-600') ? 0 : 0,
                            answer.classList.contains('text-green-600') ? 128 : 0, 0);
                        doc.text(answerLines, 20, yPos);
                        yPos += answerLines.length * 5;
                    }
                });
                yPos += 10;
            });


            doc.setFont('Helvetica', 'italic');
            doc.setFontSize(10);
            doc.setTextColor(128, 128, 128);
            doc.text(`Generated on ${new Date().toLocaleString()}`, 20, 285);
            doc.save(`${quizTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}-results.pdf`);
        }

        function copyQuizCode()
        {
            const quizCode = document.getElementById('quizCode').textContent;
            navigator.clipboard.writeText(quizCode).then(() =>
            {
                alert('Quiz code copied to clipboard!');
            }).catch(err =>
            {
                console.error('Failed to copy text: ', err);
            });
        }
    </script>
</body>

</html>