<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$Error = false;
$Check = false;
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    include 'Connect.php';
    if (!$conn) {
        die(mysqli_connect_error());
    }
    $username = $_POST['username'];
    $password = $_POST['password'];

    $Query = "Select * From User Where Email='$username'";
    $result = mysqli_query($conn, $Query);
    $num = mysqli_num_rows($result);
    $data = mysqli_fetch_assoc($result);
    if ($num != 0) {
        if (password_verify($password, $data['Password'])) {
            $Check = "true";
            session_start();
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['userid'] = $data["UserID"];
            $_SESSION['name'] = $data["Name"];
            header("location: DashBoard.php");
        } else {
            $Error = "Invalid Credentials.";
        }
    } else {
        $Error = "Invalid Credentials.";
    }
}
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
    <?php
    if ($Error) {
        echo "<script>alert('$Error');</script>";
    }
    ?>
    <header class="bg-indigo-800 shadow-lg fixed w-full z-10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center justify-between flex-wrap">
                <div class="flex items-center flex-shrink-0 text-white mr-6" onclick="window.location.href='index.php'">
                    <span class="font-bold text-xl">ChintanTrivia</span>
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
                        <a href="index.php"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Home
                        </a>
                    </div>
                    <div>
                        <a href="Login.php"
                            class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0">Login</a>
                        <a href="SignUp.php"
                            class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0">Sign
                            Up</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="pt-16">
        <section id="home" class="flex items-center justify-center mx-auto px-24 py-12 min-h-screen">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-8/9 mb-8 md:mb-0">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-6">Engage Your Audience with
                        Interactive Quizzes</h1>
                    <p class="text-lg sm:text-xl mb-8">Create, share, and analyze interactive quizzes in a gamified
                        environment. Perfect for educators, trainers, and event organizers.</p>

                </div>
                <div class="container mx-auto px-20 py-8">
                    <h1 class="text-3xl font-bold mb-8">Enter Login Information</h1>

                    <form id="login-form" class="bg-white text-indigo-900 p-6 rounded-lg shadow-md" action="Login.php"
                        method="Post">
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium mb-2">Enter Username</label>
                            <input type="text" id="username" name="username" class="w-full px-3 py-2 border rounded-md"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium mb-2">Enter Password</label>
                            <input type="password" id="password" name="password"
                                class="w-full px-3 py-2 border rounded-md" required>
                        </div>
                        <div class="flex flex-col md:flex-row items-center justify-between">
                            <div>
                                <input type="submit" id="Login"
                                    class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors"
                                    value="Login" />
                            </div>
                            <div>
                                <a href="SignUp.php" class="block text-sm font-medium mb-2" style="text-align:right;">
                                    New User? Sign Up </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>


    </main>

    <footer
        class="bg-stone-900 text-white py-2 px-4 font-semibold flex flex-col lg:flex-row justify-between items-center">
        <div class="py-2 flex justify-center items-center gap-12">
            <div>
                <div class="font-bold text-[#9BCF60] text-xl">Quick Links</div>
            </div>
            <div>
                <ul class="flex justify-center items-center gap-8">
                    <li><a href="index.php" class="hover:text-yellow-300 transition-colors">Home</a></li>
                    <li><a href="#" class="hover:text-yellow-300 transition-colors">Features</a></li>
                    <li><a href="#" class="hover:text-yellow-300 transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-yellow-300 transition-colors">Contact</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-4 text-center text-nowrap">
            <p>&copy; 2024 ChintanTrivia. All rights reserved.</p>
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