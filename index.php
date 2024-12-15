<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
if (isset($_SESSION['username']) && $_SESSION['loggedin'] == true) {
    header("location:DashBoard.php");
    exit;
}
include "Connect.php";
$Query = "Select Count(*) As Count From User";
$result = mysqli_query($conn, $Query);
$total = mysqli_fetch_assoc($result);
$totalusers = $total['Count'];
$Query = "Select Count(*) As Count From Quiz";
$result = mysqli_query($conn, $Query);
$total = mysqli_fetch_assoc($result);
$totalquizs = $total['Count'];
$Query = "Select Count(*) As Count From Results";
$result = mysqli_query($conn, $Query);
$total = mysqli_fetch_assoc($result);
$totalattempts = $total['Count'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChintanTrivia - The Quiz Platform</title>
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
                    <span class="font-extrabold text-xl">ChintanTrivia</span>
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
                        <a href="#home"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Home
                        </a>
                        <a href="#features"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Features
                        </a>
                        <a href="#use-cases"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Use Cases
                        </a>
                        <a href="#how-it-works"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            How It Works
                        </a>
                        <a href="#testimonials"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Testimonials
                        </a>
                    </div>
                    <div>
                        <a href="Login.php"
                            class="mx-2 inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0">Login</a>
                        <a href="SignUp.php"
                            class="mx-2 inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0">Sign
                            Up</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="pt-16">
        <section id="home" class="flex items-center justify-center mx-auto px-24 py-12 min-h-screen">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-6">Welcome to ChintanTrivia
                    </h1>
                    <h1 class="text-2xl sm:text-2xl md:text-2xl lg:text-2xl font-bold mb-6">Engage Your Audience with
                        Interactive Quizzes</h1>
                    <p class="text-lg sm:text-xl mb-8">Create, share, and analyze interactive quizzes in a user-friendly
                        environment. Perfect for educators, trainers and surveys</p>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="SignUp.php"
                            class="bg-yellow-400 text-indigo-900 px-6 py-3 rounded-full text-xl font-semibold hover:bg-yellow-300 transition-colors inline-block text-center">Get
                            Started</a>
                        <a href="#demo"
                            class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-full text-xl font-semibold hover:bg-white hover:text-indigo-900 transition-colors inline-block text-center">Watch
                            Demo</a>
                    </div>
                </div>

                <div>
                    <img src="./Assets/Quiz.png" alt="ChintanTrivia Illustration"
                        class="w-full max-w-md mx-auto md:max-w-lg lg:max-w-xl">
                </div>
            </div>
        </section>

        <section id="features" class="bg-[#ECCC7B] text-white py-16">
            <div class="container mx-auto px-4 min-h-[50vh] flex items-center justify-center flex-col">
                <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12 text-black">Key Features</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 text-yellow-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-4">Create Groups</h3>
                        <p>Easily create and manage groups with multiple admins and users for efficient quiz sharing.
                        </p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 text-yellow-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-4">Time-Based Quizzes</h3>
                        <p>Set time limits for entire quizzes or individual questions to keep participants engaged.</p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 text-yellow-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-4">Real-Time Statistics</h3>
                        <p>Get insights into user activity and quiz performance with our powerful dynammic analytics
                            tools.</p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 text-yellow-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-4">Dynamic Questions</h3>
                        <p> Create quizzes with multiple question types like multiple-choice, true/false, and
                            fill-in-the-blank.</p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4 text-yellow-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="text-xl font-semibold mb-4">Group Assignments</h3>
                        <p>Assign quizzes to groups of users and track their overall performance.</p>
                    </div>

                </div>
            </div>
        </section>

        <section id="use-cases" class="py-16 bg-[#ECEBE3] min-h-[50vh] flex flex-col items-center justify-center">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12 text-black">About Us</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white text-indigo-900 p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4">Education</h3>
                        <p>Engage students with interactive quizzes, track progress, and identify areas for improvement.
                        </p>
                    </div>
                    <div class="bg-white text-indigo-900 p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4">Corporate Training</h3>
                        <p>Enhance employee learning with gamified quizzes and measure training effectiveness.</p>
                    </div>
                    <div class="bg-white text-indigo-900 p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-4">Events & Conferences</h3>
                        <p>Boost audience participation and gather valuable feedback during presentations.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="bg-[#9BCF60] py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12 text-black">How It Works</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div
                            class="bg-yellow-400 text-indigo-900 rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                            1</div>
                        <h3 class="text-xl font-semibold mb-2">Create Your Quiz</h3>
                        <p>Design engaging quizzes with various question types and time limits.</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="bg-yellow-400 text-indigo-900 rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                            2</div>
                        <h3 class="text-xl font-semibold mb-2">Share with Your Group</h3>
                        <p>Invite participants to join your quiz group and participate.</p>
                    </div>
                    <div class="text-center">
                        <div
                            class="bg-yellow-400 text-indigo-900 rounded-full w-16 h-16 flex items-center justify-center text-2xl font-bold mx-auto mb-4">
                            3</div>
                        <h3 class="text-xl font-semibold mb-2">Analyze Results</h3>
                        <p>Get instant feedback and detailed analytics on quiz performance.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="demo" class="py-16 bg-[#5855CB] min-h-screen flex items-center justify-center">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12">See ChintanTrivia in Action</h2>
                <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="overflow-hidden">
                        <video src="Assets/Say Hello to ChintanTrivia.mp4" alt="ChintanTrivia Demo"
                            class="object-cover w-full h-full" muted autoplay loop>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2 text-indigo-900">Interactive Demo</h3>
                        <p class="text-gray-700 mb-4">Experience the power of ChintanTrivia with our interactive demo.
                            Create a quiz, share it with participants, and analyze the results in real-time.</p>
                        <a href="SignUp.php"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-indigo-700 transition-colors inline-block">Try
                            Demo</a>
                    </div>
                </div>
            </div>
        </section>

        <section id="testimonials" class="bg-[#183A23] text-white py-16 min-h-[50vh] flex items-center justify-center">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12">What Our Users Say</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md">
                        <p class="mb-4">"ChintanTrivia has revolutionized how I engage my students. The gamified
                            approach makes learning fun and interactive!"</p>
                        <p class="font-semibold text-yellow-400">- ABCD E., High School Teacher</p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md">
                        <p class="mb-4">"As a student, I love how ChintanTrivia makes studying more enjoyable. The timed
                            quizzes really help me prepare for exams."</p>
                        <p class="font-semibold text-yellow-400">- XYZ R., JIIT Student</p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md">
                        <p class="mb-4">"The group feature is fantastic for our team training sessions. It's easy to use
                            and provides valuable insights."</p>
                        <p class="font-semibold text-yellow-400">- Arpit Varshney., Lab Trainer</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="cta" class="bg-yellow-400 text-indigo-900 py-16">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-2xl sm:text-3xl font-bold mb-8">Ready to ChintanTrivia Your Learning?</h2>
                <a href="SignUp.php"
                    class="bg-indigo-700 text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full text-lg sm:text-xl font-semibold hover:bg-indigo-600 transition-colors inline-block">Start
                    Your Journey Now</a>
            </div>
        </section>

        <section id="statistics" class="bg-[#183A23] text-white py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-2xl sm:text-3xl font-bold text-center mb-12">ChintanTrivia Statistics</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md text-center">
                        <h3 class="text-xl font-semibold mb-4">Total Users</h3>
                        <p class="text-3xl font-bold" id="total-users"><?php echo $totalusers; ?></p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md text-center">
                        <h3 class="text-xl font-semibold mb-4">Quizzes Created</h3>
                        <p class="text-3xl font-bold" id="quizzes-created"><?php echo $totalquizs; ?></p>
                    </div>
                    <div class="bg-indigo-800 p-6 rounded-lg shadow-md text-center">
                        <h3 class="text-xl font-semibold mb-4">Total Quizzes Taken</h3>
                        <p class="text-3xl font-bold" id="quizzes-taken"><?php echo $totalattempts; ?></p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-stone-900 text-white pt-12 font-semibold">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 hover:text-indigo-500">ChintanTrivia</h3>
                    <p class="hover:text-[#9BCF60]">Revolutionizing online quizzes for every learners.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4 hover:text-[#9BCF60]">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-yellow-400 transition-colors">Home</a></li>
                        <li><a href="#features" class="hover:text-yellow-400 transition-colors">Features</a></li>
                        <li><a href="/quiz.php" class="hover:text-yellow-400 transition-colors">Create Quiz</a></li>
                        <li><a href="#contact" class="hover:text-yellow-400 transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4 hover:text-[#9BCF60]">Connect With Us</h3>
                    <div class="flex flex-col space-y-2">
                        <a href="https://www.facebook.com"
                            class="hover:text-yellow-400 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                            </svg>
                            Facebook
                        </a>
                        <a href="https://www.x.com" class="hover:text-yellow-400 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                            X(Formerly Twitter)
                        </a>
                        <a href="https://www.instagram.com"
                            class="hover:text-yellow-400 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Instagram
                        </a>
                        <a href="https://www.linkedin.com"
                            class="hover:text-yellow-400 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                                    clip-rule="evenodd" />
                            </svg>
                            LinkedIn
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-semibold mb-4 hover:text-[#9BCF60]">Contact Us</h3>
                    <ul class="space-y-2">
                        <a href="tel:+919876543210" class="flex items-center hover:text-[#ECEBE3]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            +91 98765 43210
                        </a>
                        <a href="mailto:connect@ChintanTrivia.in" class="flex items-center hover:text-[#ECEBE3]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            connect@ChintanTrivia.in
                        </a>
                        <a href="https://maps.app.goo.gl/rC3a5JLbBPeVy4ZH7"
                            class="flex items-center hover:text-[#ECEBE3]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Noida, Uttar Pradesh, India
                        </a>
                    </ul>
                </div>
            </div>
            <div class="py-8 text-center">
                <p class="hover:text-[#9BCF60]">&copy; 2024 ChintanTrivia. All rights reserved.</p>
            </div>
        </div>
    </footer>

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

        // Toggle mobile menu
        const navToggle = document.getElementById('nav-toggle');
        const navContent = document.getElementById('nav-content');
        navToggle.addEventListener('click', () =>
        {
            navContent.classList.toggle('hidden');
        });
    </script>
</body>

</html>