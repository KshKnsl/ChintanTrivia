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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChintanTrivia - Quiz Area</title>
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

                        <?php
                        echo "<span class='mr-4'>👋  Welcome, " . $_SESSION['name'] . "</span>";
                        ?>
                        <a href="LogOut.php"
                            class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0">
                            Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="pt-16">
        <div class="container mx-auto px-4 py-8">
            <h1 id="quiz-title" class="text-3xl font-bold mb-8 text-yellow-400">Quiz Title</h1>
            <div id="quiz-description" class="mb-8 text-lg"></div>
            <div id="timer" class="text-2xl font-bold mb-8"></div>
            <div id="question-container" class="bg-white text-indigo-900 p-6 rounded-lg shadow-lg mb-8">
                <div class="flex justify-between items-center">
                    <h2 id="question-text" class="text-2xl font-bold mb-4"></h2>
                    <div class="flex flex-col items-center justify-center">
                        <div id="question-timer" class="w-32 text-center"></div>
                        <div class="flex w-32">
                            <div id="question-timer-line-done" class="max-w-32 h-4 bg-green-500 rounded-l-lg"></div>
                            <div id="question-timer-line-remaining" class="max-w-32 h-4 bg-gray-400 rounded-r-lg"></div>
                        </div>
                    </div>
                </div>
                <div id="answer-options" class="space-y-4"></div>
                <div id="marks-info" class="mt-4 text-sm">
                    <span id="positive-marks"></span>
                    <span id="negative-marks"></span>
                </div>
            </div>
            <div id="feedback-container" class="hidden bg-white text-indigo-900 p-6 rounded-lg shadow-lg mb-8">
                <h3 class="text-xl font-bold mb-2">Feedback</h3>
                <p id="feedback-text"></p>
            </div>
            <div class="flex justify-between">
                <button id="prev-question"
                    class="text-lg bg-gray-600 text-white px-6 py-3 rounded-md hover:bg-[#1C1917] transition-colors hidden"
                    disabled>Previous</button>
                <div class="ml-auto">
                    <button id="save-next"
                        class="text-lg bg-yellow-500 text-white font-semibold px-6 py-3 rounded-md hover:bg-[#ECCC7B] transition-colors">Save
                        & Next</button>
                </div>
            </div>
            <button id="submit-quiz"
                class="mt-8 bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 transition-colors">Submit
                Quiz
            </button>
            <button id="download-pdf"
                class="hidden mt-4 bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors">
                Download Results as PDF
            </button>
            <div id="Leaderboard" class="hidden bg-white rounded-lg shadow-lg overflow-hidden mt-8">
                <div class="p-4 md:p-6">
                    <h2 class="text-xl md:text-2xl font-semibold mb-4 text-gray-800">Leaderboard</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white text-black">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left">Rank</th>
                                    <th class="py-2 px-4 text-left">Name</th>
                                    <th class="py-2 px-4 text-left">Score</th>
                                </tr>
                            </thead>
                            <tbody id="leaderboard-data">


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        const correctAnswers = [];
        let questionids = [];
        let currentQuestionIndex = 0;
        let quizData;
        console.log(<?php $_GET['code'] ?>);
        <?php
        $hashedquizcode = $_GET['code'] ?? '';
        $quizcode = '';
        if (strlen($hashedquizcode) > 6) {
            $quizcode = '';
            $Query = "SELECT * FROM Quiz";
            $result = mysqli_query($conn, $Query);
            while ($quizdata = mysqli_fetch_assoc($result)) {
                if (password_verify($quizdata['QuizCode'], $hashedquizcode)) {
                    $quizcode = $quizdata['QuizCode'];
                    break;
                }
            }
        } else {
            $quizcode = $hashedquizcode;
        }
        ?>
        quizData = <?php $code = $quizcode;
    
        json_encode(include 'FetchQuiz.php'); ?>;
        let userAnswers = [];
        let quizTimer;
        let questionTimer;
        let quizSubmitted = false;

        function startQuiz()
        {
            document.getElementById('quiz-title').textContent = quizData.title;
            document.getElementById('quiz-description').textContent = quizData.description;
            startQuizTimer();
            showQuestion(currentQuestionIndex);
        }

        function startQuizTimer()
        {
            let timeLeft = quizData.timeLimit * 60;
            quizTimer = setInterval(() =>
            {
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                document.getElementById('timer').textContent = `Time left: ${minutes}:${seconds.toString().padStart(2, '0')}`;
                if (timeLeft <= 0)
                {
                    clearInterval(quizTimer);
                    submitQuiz();
                }
                timeLeft--;
                timespent++;
            }, 1000);
        }

        function startQuestionTimer(timeLimit)
        {
            let questionTimerDiv = document.getElementById('question-timer');
            let questionTimerLineDone = document.getElementById('question-timer-line-done');
            let questionTimerLineRemaining = document.getElementById('question-timer-line-remaining');
            clearInterval(questionTimer);
            let timeLeft = timeLimit;
            questionTimer = setInterval(() =>
            {
                if (timeLeft <= 0)
                {
                    clearInterval(questionTimer);
                    saveAndNext();
                }
                let percentageDone = ((timeLimit - timeLeft) / timeLimit) * 100;
                questionTimerLineDone.style.width = `${percentageDone}%`;
                questionTimerLineRemaining.style.width = `${100 - percentageDone}%`;
                questionTimerDiv.textContent = `Time left: ${timeLeft}s`;
                timeLeft--;
            }, 1000);
        }

        function showQuestion(index)
        {
            const question = quizData.questions[index];
            document.getElementById('question-text').textContent = question.text;
            const answerOptions = document.getElementById('answer-options');
            answerOptions.innerHTML = '';

            switch (question.type)
            {
                case 'multiple-choice-single':
                case 'multiple-choice-multiple':
                    question.options.forEach((option, i) =>
                    {
                        const input = document.createElement('input');
                        input.type = question.type === 'multiple-choice-single' ? 'radio' : 'checkbox';
                        input.name = 'answer';
                        input.value = option;
                        input.id = `option-${i}`;
                        input.disabled = quizSubmitted;
                        const label = document.createElement('label');
                        label.phpFor = `option-${i}`;
                        label.textContent = option;
                        const div = document.createElement('div');
                        div.classList.add('flex', 'items-center', 'space-x-2');
                        div.appendChild(input);
                        div.appendChild(label);
                        answerOptions.appendChild(div);
                    });
                    break;
                case 'true-false':
                    ['True', 'False'].forEach((option, i) =>
                    {
                        const input = document.createElement('input');
                        input.type = 'radio';
                        input.name = 'answer';
                        input.value = option.toLowerCase();
                        input.id = `option-${i}`;
                        input.disabled = quizSubmitted;
                        const label = document.createElement('label');
                        label.phpFor = `option-${i}`;
                        label.textContent = option;
                        const div = document.createElement('div');
                        div.classList.add('flex', 'items-center', 'space-x-2');
                        div.appendChild(input);
                        div.appendChild(label);
                        answerOptions.appendChild(div);
                    });
                    break;
                case 'fill-in-the-blank':
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'answer';
                    input.classList.add('w-full', 'px-3', 'py-2', 'border', 'rounded-md');
                    input.disabled = quizSubmitted;
                    answerOptions.appendChild(input);
                    break;
            }

            document.getElementById('positive-marks').textContent = `Correct: +${question.positiveMarks} `;
            document.getElementById('negative-marks').textContent = `Incorrect: -${question.negativeMarks}`;

            if (!quizSubmitted)
            {
                startQuestionTimer(question.timeLimit);
            }
            document.getElementById('feedback-container').classList.add('hidden');

            if (userAnswers[index])
            {
                const savedAnswer = userAnswers[index];
                if (Array.isArray(savedAnswer))
                {
                    savedAnswer.forEach(answer =>
                    {
                        const input = document.querySelector(`input[value="${answer}"]`);
                        if (input) input.checked = true;
                    });
                } else
                {
                    const input = document.querySelector(`input[value="${savedAnswer}"]`) || document.querySelector('input[name="answer"]');
                    if (input)
                    {
                        if (input.type === 'text')
                        {
                            input.value = savedAnswer;
                        } else
                        {
                            input.checked = true;
                        }
                    }
                }
            }
        }

        function saveAndNext()
        {
            saveAnswer();
            if (quizData.showAnswersImmediately)
            {
                showFeedback();
            }
            if (currentQuestionIndex < quizData.questions.length - 1)
            {
                currentQuestionIndex++;
                showQuestion(currentQuestionIndex);
            } else
            {
                submitQuiz();

            }
        }
        let timespent = 0;

        function previousQuestion()
        {
            if (currentQuestionIndex > 0)
            {
                currentQuestionIndex--;
                showQuestion(currentQuestionIndex);
            }
        }

        function saveAnswer()
        {
            const question = quizData.questions[currentQuestionIndex];
            let answer;
            switch (question.type)
            {
                case 'multiple-choice-single':
                case 'true-false':
                    answer = document.querySelector('input[name="answer"]:checked')?.value;
                    break;
                case 'multiple-choice-multiple':
                    answer = Array.from(document.querySelectorAll('input[name="answer"]:checked')).map(input => input.value);
                    break;
                case 'fill-in-the-blank':
                    answer = document.querySelector('input[name="answer"]').value.toString().trim();
                    break;
            }
            userAnswers[currentQuestionIndex] = answer;
        }

        function showFeedback()
        {
            const question = quizData.questions[currentQuestionIndex];
            const userAnswer = userAnswers[currentQuestionIndex];
            let isCorrect = false;
            let feedbackText = '';

            switch (question.type)
            {
                case 'multiple-choice-single':
                case 'true-false':
                case 'fill-in-the-blank':
                    isCorrect = userAnswer === question.correctAnswer;
                    feedbackText = isCorrect ? 'Correct!' : `Incorrect. The correct answer is: ${question.correctAnswer}`;
                    break;
                case 'multiple-choice-multiple':
                    isCorrect = JSON.stringify(userAnswer?.sort()) === JSON.stringify(question.correctAnswer.sort());
                    feedbackText = isCorrect ? 'Correct!' : `Incorrect. The correct answers are: ${question.correctAnswer}`;
                    break;
            }

            const feedbackContainer = document.getElementById('feedback-container');
            feedbackContainer.classList.remove('hidden');
            document.getElementById('feedback-text').textContent = feedbackText;
            feedbackContainer.classList.add(isCorrect ? 'bg-green-100' : 'bg-red-100');
        }

        function submitQuiz(e)
        {
            e.preventDefault();
            this.disabled = true;
            saveAnswer();
            clearInterval(quizTimer);
            clearInterval(questionTimer);
            quizSubmitted = true;

            if (!quizData.showAnswersImmediately)
            {
                showAllFeedback();
            }
            updateLeaderboard();

            // Log user answers
            console.log('User answers:', userAnswers);
            console.log('Quiz submitted successfully!');

            document.getElementById('prev-question').disabled = false;
            document.getElementById('save-next').textContent = 'Next';
            document.getElementById('submit-quiz').style.display = 'none';

            showQuestion(currentQuestionIndex);
        }

        function showAllFeedback()
        {
            let correctCount = 0;
            let totalScore = 0;
            let feedbackHtml = '';
            quizData.questions.forEach((question, index) =>
            {
                const userAnswer = userAnswers[index];
                let isCorrect = false;
                questionids.push(Number(question.questionid));
                switch (question.type)
                {
                    case 'multiple-choice-single':
                    case 'true-false':
                    case 'fill-in-the-blank':
                        isCorrect = userAnswer == question.correctAnswer;
                        break;
                    case 'multiple-choice-multiple':
                        isCorrect = JSON.stringify(userAnswer?.sort()) === JSON.stringify(question.correctAnswer.sort());
                        break;
                }
                if (isCorrect)
                {
                    correctCount++;
                    totalScore += Number(question.positiveMarks);
                } else if (userAnswer)
                    totalScore += Number(question.negativeMarks);

                console.log(question.correctAnswer);
                correctAnswers.push(question.correctAnswer);
                feedbackHtml += `
                    <div class="mb-4 p-4 ${isCorrect ? 'bg-green-100' : 'bg-red-100'} rounded-lg">
                        <p class="font-bold">${question.text}</p>
                        <p>Your answer: ${Array.isArray(userAnswer) ? userAnswer.join(', ') : userAnswer || 'Not answered'}</p>
                        <p>Correct answer: ${Array.isArray(question.correctAnswer) ? question.correctAnswer.join(', ') : question.correctAnswer}</p>
                        <p>Marks: ${isCorrect ? `+${question.positiveMarks}` : userAnswer ? `${question.negativeMarks}` : '0'}</p>
                    </div>
                `;
            });

            const score = Math.round((correctCount / quizData.questions.length) * 100);
            feedbackHtml = `
                <h2 class="text-2xl font-bold mb-4">Quiz Results</h2>
                <p class="text-xl mb-4">Your score: ${score}% (${correctCount}/${quizData.questions.length}) \t ${score == 100 ? 'Well Done' : ' '}</p>
                <p class="text-xl mb-4">Total marks: ${totalScore}</p>
                ${feedbackHtml}
            `;

            fetch('Results.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    'QuizCode': "<?php echo $quizcode; ?>",
                    'UserID': <?php echo (int) $_SESSION['userid'] ?>,
                    'score': score,
                    'timespent': timespent,
                    'GroupID': <?php echo isset($quizdata['GroupID']) ? (int) $quizdata['GroupID'] : 0; ?>,
                    'userAnswers': userAnswers,
                    'correctAnswers': correctAnswers,
                    'questionids': questionids
                })
            }).then(response => response.text())
                .then(data =>
                {
                    console.log('Raw Response:', data);
                    const jsonData = JSON.parse(data);
                    console.log('Parsed JSON:', jsonData);
                })
                .catch(error => console.error('Fetch Error:', error));

            document.getElementById('question-container').innerHTML = feedbackHtml;
            document.getElementById('prev-question').style.display = 'none';
            document.getElementById('save-next').style.display = 'none';
            document.getElementById('submit-quiz').style.display = 'none';
            document.getElementById('download-pdf').classList.remove('hidden');
            document.getElementById('Leaderboard').classList.remove('hidden');
        }

        document.getElementById('save-next').addEventListener('click', saveAndNext);
        document.getElementById('prev-question').addEventListener('click', previousQuestion);
        document.getElementById('submit-quiz').addEventListener('click', submitQuiz);

        startQuiz();

        document.querySelectorAll('a').forEach(link =>
        {
            link.addEventListener('mouseover', () =>
            {
                link.classList.add('animate-pulse');
            });
            link.addEventListener('mouseout', () =>
            {
                link.classList.remove('animate-pulse');
            });
        });

        const navToggle = document.getElementById('nav-toggle');
        const navContent = document.getElementById('nav-content');
        navToggle.addEventListener('click', () =>
        {
            navContent.classList.toggle('hidden');
        });

        function generatePDF()
        {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                size: 'A4',
                orientation: 'p',
                compressPdf: true
            });

            doc.text('', 10, 10);
            doc.setFont('Helvetica', 'bold');
            doc.setTextColor(255, 235, 59);
            doc.setFontSize(50);
            doc.text("ChintanTrivia", 105, 20, null, null, "center");
            const content = document.getElementById('question-container');

            doc.setFont('times', 'normal');
            doc.setFontSize(18);
            doc.setTextColor(0, 0, 0);
            doc.text(quizData.title, 105, 40, null, null, 'center');

            doc.setFontSize(14);
            let yPos = 60;

            content.querySelectorAll('p, div').forEach((element, index) =>
            {
                if (yPos > 280)
                {
                    doc.addPage();
                    yPos = 20;
                }
                if (element.tagName === 'DIV')
                {
                    doc.setFillColor(element.classList.contains('bg-green-100') ? 200 : 255, 200, 200);
                    doc.roundedRect(10, yPos - 5, 190, 37, 5, 5, 'F');
                    doc.setTextColor(0, 0, 0);
                } else if (index < 3)
                    doc.setTextColor(0, 0, 255);
                else
                {
                    doc.setTextColor(0, 0, 0);

                    const text = element.innerText;
                    const lines = doc.splitTextToSize(text, 180);
                    doc.text(lines, 15, yPos);
                    yPos += 12 * lines.length;
                }
            });

            doc.save('quiz-results.pdf');
        }
        document.getElementById('download-pdf').addEventListener('click', generatePDF);

        async function updateLeaderboard()
        {
            try
            {
                const quizCode = "<?php echo $quizcode; ?>";
                console.log('Fetching leaderboard for quiz:', quizCode);

                const response = await fetch(`fetch_leaderboard.php?quiz_id=${quizCode}`);
                console.log('Raw Response:', response);

                const contentType = response.headers.get('content-type');
                const responseText = await response.text();
                console.log('Raw Text:', responseText); // Debug raw text
                const leaderboardData = JSON.parse(responseText); // Parse manually for debugging
                console.log('Parsed JSON:', leaderboardData);

                const leaderboardContainer = document.getElementById('leaderboard-data');
                leaderboardContainer.innerHTML = '';
                leaderboardData.forEach((entry, index) =>
                {
                    const row = document.createElement('tr');
                    row.className = index % 2 === 0 ? 'bg-gray-50' : 'bg-white';

                    row.innerHTML = `
                <td class="py-2 px-4 text-black">${index + 1}</td>
                <td class="py-2 px-4 text-black">${entry.name}</td>
                <td class="py-2 px-4 text-black">${entry.score}</td>
            `;
                    leaderboardContainer.appendChild(row);
                });
            } catch (error)
            {
                console.error('Error fetching or parsing leaderboard data:', error);
            }
        }
    </script>
</body>

</html>