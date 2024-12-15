<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['username']) || $_SESSION['loggedin'] != true) {
    header("location:index.php");
    exit;
}
include 'Connect.php';

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $result = mysqli_query($conn, "SELECT UserId FROM user WHERE email = '" . mysqli_real_escape_string($conn, $username) . "'");
    $row = mysqli_fetch_assoc($result);
    $adminID = $row['UserId'] ?? 0;
    if (isset($_GET['title']) && !empty($_GET['title'])) {
        $groupID = $_GET['title'];
    } else {
        $groupID = "NULL";
    }
} else {
    $adminID = 0;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChintanTrivia - Create Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
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
                        <a href="#" class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            My Quizzes
                        </a>
                        <a href="DashBoard.php#groups"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Groups
                        </a>
                        <a href="#" class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Analytics
                        </a>
                        <a href="#" class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Create Quiz
                        </a>
                    </div>
                    <div>

                        <?php
                        $name = $_SESSION['name'] ?? "";
                        echo "<span class='mr-4'>👋  Welcome, {$name}</span>";
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
            <h1 class="text-3xl font-bold mb-8 text-yellow-400">Create a New Quiz</h1>
            <div
                class="form-container p-4 bg-gradient-to-r from-orange-400 via-red-500 to-pink-500 rounded-lg shadow-lg">
                <form id="quiz-form" class="bg-white text-indigo-900 p-6 rounded-lg shadow-lg">
                    <div class="mb-4">
                        <label for="quiz-title" class="block text-sm font-medium mb-2">Quiz Title</label>
                        <input type="text" id="quiz-title" name="quiz-title" class="w-full px-3 py-2 border rounded-md"
                            required placeholder="Enter quiz title">
                    </div>

                    <div class="mb-4">
                        <label for="quiz-description" class="block text-sm font-medium mb-2">Description</label>
                        <textarea id="quiz-description" name="quiz-description" rows="3"
                            class="w-full px-3 py-2 border rounded-md" placeholder="Describe your quiz"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="time-limit" class="block text-sm font-medium mb-2">Total Time Limit
                            (minutes)</label>
                        <input type="number" id="time-limit" name="time-limit"
                            class="w-full px-3 py-2 border rounded-md" min="1"
                            placeholder="Enter time limit in minutes">
                    </div>
                    <!-- 
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="show-answers-immediately" name="show-answers-immediately"
                                class="mr-2">
                            <span class="text-sm">Show correct answers after each question</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">If unchecked, answers will be shown at the end of the quiz
                        </p>
                    </div> -->
                    <div class="mb-4 flex flex-wrap -mx-2">
                        <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                            <label for="start-date" class="block text-sm font-medium mb-2">Start Date</label>
                            <input type="date" id="start-date" name="start-date"
                                class="w-full px-3 py-2 border rounded-md" required
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="w-full md:w-1/2 px-2">
                            <label for="start-time" class="block text-sm font-medium mb-2">Start Time</label>
                            <input type="time" id="start-time" name="start-time"
                                class="w-full px-3 py-2 border rounded-md" required value="<?php echo date('H:i'); ?>">
                        </div>
                    </div>
                    <div class="mb-4 flex flex-wrap -mx-2">
                        <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
                            <label for="due-date" class="block text-sm font-medium mb-2">Due Date</label>
                            <input type="date" id="due-date" name="due-date" class="w-full px-3 py-2 border rounded-md"
                                required value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                        </div>
                        <div class="w-full md:w-1/2 px-2">
                            <label for="due-time" class="block text-sm font-medium mb-2">Due Time</label>
                            <input type="time" id="due-time" name="due-time" class="w-full px-3 py-2 border rounded-md"
                                required value="<?php echo date('H:i'); ?>">
                        </div>
                    </div>

                    <div id="questions-container" class="space-y-6">
                        <!-- Questions will be added dynamically here-->
                    </div>

                    <button type="button" id="add-question"
                        class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-[#183A23] transition-colors">Add
                        Question</button>

                    <div class="mt-8 mb-8 bg-gradient-to-r from-[#2E2F2F] to-teal-500 p-6 rounded-lg">
                        <div class="flex items-center mb-4">
                            <img src="Assets/Gem.svg" width="30px" alt="AI Icon">
                            <h2 class="text-xl font-bold p-2 text-white">
                                AI Question Generator
                            </h2>
                        </div>
                        <textarea id="quizPrompt" class="w-full h-24 p-2 border border-gray-300 rounded"
                            placeholder="Enter your quiz topic and any specific instructions..."></textarea>
                        <button type="button" id="generateQuiz"
                            class="mt-4 w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition-colors">Generate
                            AI
                            Questions</button>
                        <p class="text-sm text-white mt-2">⚠️Disclaimer: AI is smart, but it's not perfect. Don't blame
                            it if you end up in a disaster zone💀 — double-check to dodge disaster!</p>
                        <div id="loadingSVG" class="mt-2 w-full flex justify-center hidden"><img
                                src="./Assets/loading.svg" width="100px" alt="Loading"></div>
                        <div id="aiGeneratedQuestions" class="mt-4">
                            <!-- AI generated questions will be displayed here -->
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit"
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-md hover:from-indigo-700 hover:to-purple-700 transition-colors">Create
                            Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        let questionCount = 0;

        function createQuestionBox(questionData)
        {
            questionCount++;
            let questionBox = document.createElement('div');
            questionBox.className = 'question-box mb-6 p-4 border border-l-4 border-l-blue-500 rounded-md shadow-xl text-black';
            questionBox.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Question ${questionCount}</h3>
                    <button type="button" class="remove-question bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600 transition-colors">Remove</button>
                </div>
                <div class="mb-4">
                    <label for="question-${questionCount}" class="block text-sm font-medium mb-2">Question Text</label>
                    <input type="text" id="question-${questionCount}" name="question-${questionCount}" class="w-full px-3 py-2 border rounded-md" required value="${questionData.text}" placeholder="Enter the Question here">
                </div>
                <div class="mb-4 flex w-full justify-between align-center gap-4">
                    <div class="w-full">
                        <label for="question-${questionCount}-positive-marks" class="block text-sm font-medium mb-2">Question Positive Marks(+ve)</label>
                        <input type="number" id="question-${questionCount}-positive-marks" name="question-${questionCount}-positive-marks" class="w-full px-3 py-2 border rounded-md" required value="${questionData.positiveMarks || 1}">
                    </div>
                    <div class="w-full">
                        <label for="question-${questionCount}-negative-marks" class="block text-sm font-medium mb-2">Question Negative Marks(-ve)</label>
                        <input type="number" id="question-${questionCount}-negative-marks" name="question-${questionCount}-negative-marks" class="w-full px-3 py-2 border rounded-md" required value="${questionData.negativeMarks || 0}">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="question-time-${questionCount}" class="block text-sm font-medium mb-2">Time Limit for this Question (seconds)</label>
                    <input type="number" id="question-time-${questionCount}" name="question-time-${questionCount}" class="w-full px-3 py-2 border rounded-md" min="1" value="${questionData.timeLimit || 30}">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Question Type</label>
                    <select name="question-type-${questionCount}" class="question-type w-full px-3 py-3 border rounded-md">
                        <option value="multiple-choice-single" ${questionData.type === 'multiple-choice-single' ? 'selected' : ''}>Multiple Choice (Single Correct)</option>
                        <option value="multiple-choice-multiple" ${questionData.type === 'multiple-choice-multiple' ? 'selected' : ''}>Multiple Choice (Multiple Correct)</option>
                        <option value="true-false" ${questionData.type === 'true-false' ? 'selected' : ''}>True/False</option>
                        <option value="fill-in-the-blank" ${questionData.type === 'fill-in-the-blank' ? 'selected' : ''}>Fill in the Blank</option>
                    </select>
                </div>
                <div class="answer-options mb-4">
                    <!-- Answer options will be dynamically updated based on question type -->
                </div>
            `;

            let removeButton = questionBox.querySelector('.remove-question');
            removeButton.addEventListener('click', () =>
            {
                questionBox.remove();
                updateQuestionNumbers();
            });

            let questionTypeSelect = questionBox.querySelector('.question-type');
            questionTypeSelect.addEventListener('change', () =>
            {
                updateAnswerOptions(questionBox, questionTypeSelect.value, questionData.options, questionData.correctAnswer);
            });

            document.getElementById('questions-container').appendChild(questionBox);
            updateAnswerOptions(questionBox, questionData.type, questionData.options, questionData.correctAnswer);
        }

        function updateAnswerOptions(questionBox, questionType, options = [], correctAnswer = null)
        {
            const answerOptionsContainer = questionBox.querySelector('.answer-options');
            answerOptionsContainer.innerHTML = '';

            switch (questionType)
            {
                case 'multiple-choice-single':
                case 'multiple-choice-multiple':
                    answerOptionsContainer.innerHTML = `
                        <label class="block text-sm font-medium mb-2">Answer Options</label>
                        <div class="space-y-2 mb-2 overflow-y-scroll" id="options-container">
                            ${options.map((option, index) => `
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="option-${index + 1}" class="flex-grow px-3 py-2 border rounded-md" value="${option}" placeholder="Option ${index + 1}">
                                    <button type="button" class="remove-option bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600 transition-colors">Remove</button>
                                </div>
                            `).join('')}
                        </div>
                        <button type="button" class="add-option bg-green-500 text-white px-2 py-1 rounded-md hover:bg-green-600 transition-colors">Add Option</button>
                    `;
                    if (questionType === 'multiple-choice-single')
                    {
                        answerOptionsContainer.innerHTML += `
                            <div class="mt-2 text-black">
                                <label class="block text-sm font-medium mb-2">Correct Answer</label>
                                <select name="correct-answer" class="w-full px-3 py-2 border rounded-md" placeholder="Choose the correct answer here">
                                    ${options.map((option, index) => `
                                        <option value="${index + 1}" ${correctAnswer === index + 1 ? 'selected' : ''}>${option}</option>
                                    `).join('')}
                                </select>
                            </div>
                        `;
                    } else
                    {
                        answerOptionsContainer.innerHTML += `
                            <div class="mt-2">
                                <label class="block text-sm font-medium mb-2">Correct Answers (select multiple)</label>
                                <div class="flex flex-wrap items-center gap-4">
                                    ${options.map((option, index) => `
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="correct-answer" value="${index + 1}" ${Array.isArray(correctAnswer) && correctAnswer.includes(index + 1) ? 'checked' : ''}>
                                            <span>${option}</span>
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }
                    const addOptionButton = answerOptionsContainer.querySelector('.add-option');
                    addOptionButton.addEventListener('click', () =>
                    {
                        const optionsContainer = answerOptionsContainer.querySelector('#options-container');
                        const newOptionNumber = optionsContainer.children.length + 1;
                        const newOptionDiv = document.createElement('div');
                        newOptionDiv.className = 'flex items-center space-x-2';
                        newOptionDiv.innerHTML = `
                            <input type="text" 
                            name="option-${newOptionNumber}" class="flex-grow px-3 py-2 border rounded-md" placeholder="New option">
                            <button type="button" class="remove-option bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600 transition-colors">Remove</button>
                        `;
                        optionsContainer.appendChild(newOptionDiv);
                        updateCorrectAnswerOptions(answerOptionsContainer, questionType);
                    });
                    answerOptionsContainer.addEventListener('click', (e) =>
                    {
                        if (e.target.classList.contains('remove-option'))
                        {
                            e.target.closest('div').remove();
                            updateCorrectAnswerOptions(answerOptionsContainer, questionType);
                        }
                    });
                    break;
                case 'true-false':
                    answerOptionsContainer.innerHTML = `
                        <label class="block text-sm font-medium mb-2">Correct Answer</label>
                        <select name="correct-answer" class="w-full px-4 py-2 border rounded-md">
                            <option value="true" ${correctAnswer === true ? 'selected' : ''}>True</option>
                            <option value="false" ${correctAnswer === false ? 'selected' : ''}>False</option>
                        </select>
                    `;
                    break;
                case 'fill-in-the-blank':
                    answerOptionsContainer.innerHTML = `
                        <label class="block text-sm font-medium mb-2">Correct Answer</label>
                        <input type="text" name="correct-answer" class="w-full px-4 py-2 border rounded-md" placeholder="Enter the correct answer" value="${correctAnswer || ''}">
                    `;
                    break;
            }
        }

        function updateCorrectAnswerOptions(container, questionType)
        {
            const options = Array.from(container.querySelectorAll('input[name^="option-"]')).map(input => input.value);
            const correctAnswerContainer = container.querySelector('select[name="correct-answer"]') || container.querySelector('div:has(> label > input[type="checkbox"])');

            if (questionType === 'multiple-choice-single')
            {
                correctAnswerContainer.innerHTML = options.map((option, index) => `
                    <option value="${index + 1}">${option}</option>
                `).join('');
            } else if (questionType === 'multiple-choice-multiple')
            {
                correctAnswerContainer.innerHTML = options.map((option, index) => `
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="correct-answer" value="${index + 1}">
                        <span>${option}</span>
                    </label>
                `).join('');
            }
        }

        function updateQuestionNumbers()
        {
            const questionBoxes = document.querySelectorAll('.question-box');
            questionBoxes.forEach((box, index) =>
            {
                const questionNumber = index + 1;
                box.querySelector('h3').textContent = `Question ${questionNumber}`;
            });
        }


        // AI Question Generation
        const apiKey = "KEEP YOUR API KEY FROM GEMINI HERE";
        const url = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${apiKey}`;

        document.getElementById('generateQuiz').addEventListener('click', generateQuiz);

        async function generateQuiz()
        {
            const loader = document.getElementById('loadingSVG');
            const prompt = document.getElementById('quizPrompt').value;

            if (!prompt)
            {
                alert("Please enter a prompt for the quiz.");
                return;
            }
            loader.classList.remove('hidden');

            const refinedPrompt = `
                Generate a quiz based on the following topic: "${prompt}"
                
                Please provide 5 hard questions in the following JSON format:
                {
                    "questions": [
                        {
                            "text": "Question text here",
                            "type": "multiple-choice-single",
                            "options": ["Option 1", "Option 2", "Option 3", "Option 4"],
                            "correctAnswer": 1,
                            "positiveMarks": 1,
                            "negativeMarks": 0,
                            "timeLimit": 30
                        },
                        // ... more questions ...
                        ]
                        }
                        
                        Ensure that:
                        1. Each question has a clear and concise text.
                        2. The "type" can be "multiple-choice-single", "multiple-choice-multiple", "true-false", or "fill-in-the-blank".
                        3. For "multiple-choice-multiple", the "correctAnswer" should be an array of correct option indices.
                        4. For "true-false", use only two options: ["True", "False"].
                        5. For "fill-in-the-blank", omit the "options" field and make "correctAnswer" a string.
                        6. Assign appropriate positive and negative marks, and time limits for each question based on its difficulty.
                        7. Keep the questions challenging and engaging.
                        8. Always ask relevant and not straightforward questions.
                        9. Avoid asking questions that can be easily answered by a simple Google search.
                        10. Avoid asking questions that are too easy or too difficult.
                        11. do not answer if the topic given is not clear or irrelevant.
                        12. Do not use quotes and slashes in the JSON keys or values.
                        
                        Please provide only the JSON output(as text not as formatted json), without any additional text or explanation.
                        `;

            try
            {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        contents: [{
                            parts: [{
                                text: refinedPrompt
                            }]
                        }]
                    })
                });

                const jsonResponse = await response.json();
                console.log(jsonResponse);
                const generatedQuestions = parseGeneratedQuestions(jsonResponse);
                loader.classList.add('hidden');
                displayAiQuestions(generatedQuestions);
            }
            catch (error)
            {
                console.log("Error:", error);
                alert("An error occurred while generating questions. Please try again.");
            }
        }

        function parseGeneratedQuestions(jsonResponse)
        {
            if (jsonResponse.candidates && jsonResponse.candidates.length > 0)
            {
                const content = jsonResponse.candidates[0].content;
                if (content && content.parts && content.parts.length > 0)
                {
                    try
                    {
                        const parsedData = JSON.parse(content.parts[0].text);
                        return parsedData.questions || [];
                    }
                    catch (error)
                    {
                        console.error("Error parsing JSON:", error);
                        return [];
                    }
                }
            }
            return [];
        }

        function displayAiQuestions(questions)
        {
            const aiGeneratedQuestionsDiv = document.getElementById('aiGeneratedQuestions');
            aiGeneratedQuestionsDiv.innerHTML = "";

            if (questions.length > 0)
            {
                questions.forEach((question, index) =>
                {
                    const questionElement = document.createElement('div');
                    questionElement.className = "p-4 border-b border-gray-300 flex justify-between items-center bg-white rounded-md shadow-md m-2";
                    questionElement.innerHTML = `
                    <div>
                    <p class="font-bold">${question.text}</p>
                    <p class="text-sm text-gray-600">Type: ${question.type}</p>
                    </div>
                    <button class="add-ai-question bg-green-500 text-white px-2 py-1 rounded-md hover:bg-green-600 transition-colors">Add</button>
                    `;

                    const addButton = questionElement.querySelector('.add-ai-question');
                    addButton.addEventListener('click', () =>
                    {
                        createQuestionBox(question);
                        questionElement.remove();
                    });

                    aiGeneratedQuestionsDiv.appendChild(questionElement);
                });
            }
            else
            {
                aiGeneratedQuestionsDiv.innerHTML = "No questions generated. Please try a different prompt or refine this further.";
            }
        }
        document.getElementById('add-question').addEventListener('click', () =>
        {
            createQuestionBox({
                text: '',
                type: 'multiple-choice-single',
                options: ['', '', '', ''],
                correctAnswer: null,
                positiveMarks: 1,
                negativeMarks: 0,
                timeLimit: 30
            });
        });

        document.getElementById('quiz-form').addEventListener('submit', (e) =>
        {
            e.preventDefault();
            const adminID = <?php echo json_encode($adminID); ?>;
            const groupID = <?php echo json_encode($groupID); ?>;
            const formData = new FormData(e.target);
            const quizData = {
                title: formData.get('quiz-title'),
                description: formData.get('quiz-description'),
                timeLimit: formData.get('time-limit'),
                showAnswersImmediately: formData.get('show-answers-immediately') === 'on',
                startDate: formData.get('start-date'),
                startTime: formData.get('start-time'),
                dueDate: formData.get('due-date'),
                dueTime: formData.get('due-time'),
                questions: [],
                adminID: adminID,
                groupID: groupID
            };

            document.querySelectorAll('.question-box').forEach((questionBox, index) =>
            {
                const questionNumber = index + 1;
                const questionData = {
                    text: formData.get(`question-${questionNumber}`),
                    timeLimit: formData.get(`question-time-${questionNumber}`),
                    type: formData.get(`question-type-${questionNumber}`),
                    positiveMarks: formData.get(`question-${questionNumber}-positive-marks`),
                    negativeMarks: -Math.abs(formData.get(`question-${questionNumber}-negative-marks`)),
                    options: [],
                    correctAnswer: null
                };

                switch (questionData.type)
                {
                    case 'multiple-choice-single':
                    case 'multiple-choice-multiple':
                        questionBox.querySelectorAll('input[name^="option-"]').forEach(option =>
                        {
                            questionData.options.push(option.value);
                        });
                        if (questionData.type === 'multiple-choice-single')
                            questionData.correctAnswer = formData.get(`correct-answer`);
                        else
                            questionData.correctAnswer = Array.from(questionBox.querySelectorAll('input[name="correct-answer"]:checked')).map(input => input.value);
                        break;
                    case 'true-false':
                        questionData.options = ['True', 'False'];
                        questionData.correctAnswer = formData.get(`correct-answer`) === 'true';
                        break;
                    case 'fill-in-the-blank':
                        questionData.correctAnswer = formData.get(`correct-answer`);
                        break;
                }

                quizData.questions.push(questionData);
            });

            console.log('Quiz data:', quizData);
            fetch('QuizAdd.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(quizData)
            })
                .then(response => response.json())
                .then(result =>
                {
                    console.log('Success:', result);
                    if (result.success)
                    {
                        document.querySelector('.form-container').innerHTML = `
                      <div class="p-6 bg-green-500 text-white rounded-lg shadow-lg">
                          <h2 class="text-2xl font-bold mb-4">Quiz created successfully!</h2>
                          <p class="mb-4">Quiz code: 
                              <span id="quizCode" class="font-mono bg-white text-green-500 px-2 py-1 rounded">${result.quizCode}</span>
                              <button id="copyButton" class="ml-2 bg-white text-green-500 px-4 py-2 rounded-full hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-300 ease-in-out transform hover:scale-105">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                      <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                      <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                  </svg>
                                  Copy
                              </button>
                          </p>
                          <div class="flex space-x-4 mt-6">
                              <button onclick="window.location.href='DashBoard.php'" class="bg-blue-500 text-white px-6 py-3 rounded-md hover:bg-blue-600 transition-colors duration-300 transform hover:scale-105">Return to Dashboard</button>
                              <button onclick="window.location.href='QuizArea.php?code=${encodeURIComponent(result.quizCode)}'" class="bg-yellow-500 text-white px-6 py-3 rounded-md hover:bg-yellow-600 transition-colors duration-300 transform hover:scale-105">Go to Quiz Area</button>
                          </div>
                      </div>
                  `;

                        document.getElementById('copyButton').addEventListener('click', function ()
                        {
                            const quizCode = document.getElementById('quizCode').textContent;
                            navigator.clipboard.writeText(quizCode).then(() =>
                            {
                                this.innerHTML = `
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                  </svg>
                                  Copied!
                              `;
                                this.classList.add('animate-pulse');
                                setTimeout(() =>
                                {
                                    this.innerHTML = `
                                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                          <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                          <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                      </svg>
                                      Copy
                                  `;
                                    this.classList.remove('animate-pulse');
                                }, 2000);
                            }).catch(err =>
                            {
                                console.error('Failed to copy text: ', err);
                            });
                        });
                    } else
                    {
                        alert(`Error: ${result.message}`);
                    }
                })
                .catch(error =>
                {
                    console.error('Error:', error);
                    alert('An error occurred while creating the quiz. Please try again.');
                });
        });
    </script>
</body>

</html>