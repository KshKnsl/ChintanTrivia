<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'Connect.php';
if (!isset($_SESSION['username']) || $_SESSION['loggedin'] != true) {
    header("location:index.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!$conn) {
        die(mysqli_connect_error());
    }
    $name = $_POST['Group_Name'];
    $desc = $_POST['Group_Desc'];
    $members = $_POST['memberCount'];
    function generateRandomString($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = random_int(0, $charactersLength - 1);
            $randomString .= $characters[$randomIndex];
        }

        return $randomString;
    }
    function isUnique($code, $conn)
    {
        $Query = "Select * From QuizGroups Where GroupCode='$code' ";
        $result = mysqli_query($conn, $Query);
        $num = mysqli_num_rows($result);
        return $num == 0;
    }

    do {
        $randomCode = generateRandomString(6);
    } while (!isUnique($randomCode, $conn));

    $Query = "Insert Into QuizGroups(Name,Description,GroupCode) Values('$name','$desc','$randomCode') ";
    $result = mysqli_query($conn, $Query);
    $Query = "Select * From QuizGroups Where GroupCode='$randomCode' ";
    $result = mysqli_query($conn, $Query);
    $groupdata = mysqli_fetch_assoc($result);
    $Query = "Insert Into GroupMembers Values('{$_SESSION['userid']}','{$groupdata['GroupID']}','1') ";
    $result = mysqli_query($conn, $Query);
    $error = "";
    for ($i = 1; $i <= $members; $i++) {
        $mail = $_POST['grp_member' . $i . '_email'];
        if (isset($_POST['grp_member' . $i . '_isAdmin'])) {
            $isAdmin = 1;
        } else {
            $isAdmin = 0;
        }
        $Query = "Select * From User Where Email='$mail' ";
        $result = mysqli_query($conn, $Query);
        $num = mysqli_num_rows($result);
        if ($num == 0) {
            $error = $error . $mail . "<BR>";
        } else {
            $userdata = mysqli_fetch_assoc($result);
            $UserID = (int) $userdata['UserID'];
            $GroupID = (int) $groupdata['GroupID'];
            $Query = "SELECT * FROM GroupMembers WHERE UserID = $UserID AND GroupID = $GroupID";
            $result2 = mysqli_query($conn, $Query);
            $num2 = mysqli_num_rows($result2);
            if ($num2 == 0) {
                $Query = "Insert Into GroupMembers Values('{$userdata['UserID']}','{$groupdata['GroupID']}','{$isAdmin}')";
                $result = mysqli_query($conn, $Query);
            }
        }
    }
    if ($error != "") {
        $error = "Check The Following Mails:\n" . str_replace("<BR>", "\n", $error);
        echo "<script>
                alert(" . json_encode($error) . ");
                window.location.href = 'DashBoard.php';
                </script>";
    } else {
        header("location:DashBoard.php");
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

        header {
            opacity: 0;
            transition: all 0.4s ease-in-out;
        }

        #text1 {
            position: relative;
            right: 5vw;
            opacity: 0;
            transition: all 0.4s ease-in-out 0.3s;
        }

        #text2 {
            position: relative;
            right: 5vw;
            opacity: 0;
            transition: all 0.4s ease-in-out 0.6s;
        }

        #b1,
        #b2 {
            position: relative;
            right: 5vw;
            opacity: 0;
        }

        .started {
            transition: all 0.4s ease-in-out;
        }

        #b1.started {
            transition-delay: 0.9s;
        }

        #b2.started {
            transition-delay: 1.0s;
        }

        .but {
            transition: transform 0.2s, background-color 0.2s, color 0.2s;
        }

        #b1.but:hover,
        #b2.but:hover {
            transition-delay: 0s;
        }

        #img1 {
            animation: bounce 2s linear infinite;
        }

        @keyframes bounce {
            0% {
                transform: translateY(0);
            }

            25% {
                transform: translateY(20px);
            }

            50% {
                transform: translateY(0);
            }

            75% {
                transform: translateY(-20px);
            }

            100% {
                transform: translateY(0);
            }
        }

        #img2 {
            animation: crisscross 2.5s ease-in-out infinite;
        }

        @keyframes crisscross {
            20% {
                transform: rotate(0deg);
            }

            30% {
                transform: rotate(-10deg);
            }

            40% {
                transform: rotate(10deg);
            }

            50% {
                transform: rotate(-10deg);
            }

            60% {
                transform: rotate(10deg);
            }

            70% {
                transform: rotate(0deg);
            }
        }

        #text3 {
            position: relative;
            left: 5vw;
            opacity: 0;
            transition: all 0.4s ease-out 1.2s;
        }

        #b3,
        #b4 {
            position: relative;
            left: 5vw;
            opacity: 0;
        }

        #b3.started {
            transition-delay: 1.5s;
        }

        #b4.started {
            transition-delay: 1.6s;
        }

        #b3.but:hover,
        #b4.but:hover {
            transition-delay: 0s;
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 500px;
            width: 100%;
            max-height: 70vh;
            overflow: scroll;
        }

        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        .close2 {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        #joinQuizPopup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        #joinQuizPopup .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 400px;
            width: 100%;
        }

        #joinQuizPopup .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            color: #4a5568;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen text-white">
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
                        <a href="#" class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Dashboard
                        </a>
                        <a href="QuizDetails.php"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            My Quizzes
                        </a>
                        <a href="#groups"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Groups
                        </a>
                        <a href="QuizCreate.php"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Create Quiz
                        </a>
                    </div>
                    <div class="flex items-center">

                        <?php
                        echo "<span class='mr-4'>👋  Welcome, " . $_SESSION['name'] . "</span>";
                        ?>
                        <a href="LogOut.php"
                            class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0 mr-2">
                            Logout
                        </a>
                        <button
                            class="inline-block bg-red-500 text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-white-500 hover:bg-red mt-4 lg:mt-0"
                            onclick="confirmDeleteAccount()"> Delete Account
                        </button>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main class="pt-16">
        <section id="home" class="flex justify-center mx-auto px-4 pt-12 h-full">
            <div class="w-full h-full pl-16 pt-16 flex flex-col ">
                <span id="text1" class="text-5xl font-semibold">Ready to conquer the world with your knowledge?</span>
                <span id="text2" class="text-5xl pt-8 font-semibold text-yellow-300">Go Ahead!</span>
                <div class="gap-5 flex flex-row pt-12">
                    <button id="b1"
                        class="bg-indigo-800 shadow-lg w-fit p-3 px-5 rounded-full active:shadow-none active:scale-95 hover:scale-105 hover:bg-white hover:text-indigo-800 transition-all duration-300">Create
                        group</button>
                    <button id="b2"
                        class="bg-indigo-800 shadow-lg w-fit p-3 px-5 rounded-full active:shadow-none active:scale-95 hover:scale-105 hover:bg-white hover:text-indigo-800 transition-all duration-300">Join
                        group</button>
                </div>
            </div>
            <div class="w-2/3 h-full flex justify-center items-center">
                <img id="img1" src="./Assets/lander1.png" class="w-4/5 mr-40 mt-8" alt="Quiz illustration" />
            </div>
        </section>
        <section id="home" class="flex justify-center mx-auto px-4 h-full">
            <div class="w-2/3 h-full">
                <img id="img2" src="./Assets/lander2.png" class="ml-16 w-4/5" alt="Quiz illustration" />
            </div>
            <div class="w-full h-full pl-16 flex flex-col ">
                <span id="text3" class="text-5xl font-semibold mt-16">Quiz Time</span>
                <div class="gap-5 flex flex-row pt-12">
                    <button id="b3"
                        class="bg-indigo-800 shadow-lg w-fit p-3 px-5 rounded-full active:shadow-none active:scale-95 hover:scale-105 hover:bg-white hover:text-indigo-800 transition-all duration-300"
                        onclick="window.location.href='QuizCreate.php'">Create Quiz</button>
                    <button id="b4"
                        class="bg-indigo-800 shadow-lg w-fit p-3 px-5 rounded-full active:shadow-none active:scale-95 hover:scale-105 hover:bg-white hover:text-indigo-800 transition-all duration-300"
                        onclick="openJoinQuizPopup()">Join Quiz</button>
                </div>
            </div>
        </section>
        <section class="container mx-auto px-4 pt-12 mb-8" id="groups">
            <h1 class="text-4xl font-bold text-center mb-8 text-yellow-300">Classroom Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $Query = "SELECT DISTINCT QuizGroups.* FROM QuizGroups 
                  INNER JOIN GroupMembers ON QuizGroups.GroupID = GroupMembers.GroupID 
                  WHERE GroupMembers.UserID = '" . $_SESSION['userid'] . "'";
                $result = mysqli_query($conn, $Query);
                $num = mysqli_num_rows($result);
                if ($num != 0) {
                    while ($data = mysqli_fetch_assoc($result)) {
                        $code = mysqli_real_escape_string($conn, $data['GroupCode']);
                        $Query = "SELECT COUNT(*) as member_count FROM GroupMembers WHERE GroupID = '" . $data['GroupID'] . "'";
                        $result2 = mysqli_query($conn, $Query);
                        $member_data = mysqli_fetch_assoc($result2);
                        $Query = "Select * From GroupMembers Where GroupID={$data['GroupID']} And IsAdmin=1";
                        $result2 = mysqli_query($conn, $Query);
                        $countAdmin = mysqli_num_rows($result2);
                        $countmembers = $member_data['member_count'] - $countAdmin;
                        $hashedcode = password_hash($data['GroupCode'], PASSWORD_DEFAULT);
                        echo "
                <div class='classroom-card bg-gradient-to-br from-blue-500 to-blue-700 text-white transform hover:scale-105 transition-all duration-300 rounded-lg shadow-lg p-6' onclick=\"window.location.href='Groups.php?title=" . urlencode($hashedcode) . "';\">
                    <div class='classroom-title text-2xl mb-2'>" . htmlspecialchars($data['Name']) . "</div>
                    <div class='classroom-description text-blue-100'>" . htmlspecialchars($data['Description']) . "</div>
                    <div class='mt-4 flex justify-between items-center'>
                        <span class='bg-blue-800 text-xs font-semibold px-2 py-1 rounded-full'>" . $countmembers . " Students </span>
                        <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7' />
                        </svg>
                    </div>
                </div>
                ";
                    }
                } else {
                    echo "<h1 class='text-2xl font-bold text-center mb-8 text-white-300'>No Groups Joined Yet</h1>";
                }
                ?>
            </div>
        </section>
    </main>

    <div id="createGroupPopup" class="popup">
        <div class="popup-content bg-white text-indigo-900 rounded-lg shadow-xl">
            <div class="close text-red-500 font-extrabold">x</div>
            <h2 class="text-3xl font-bold mb-6 text-center text-indigo-600">Create a Group</h2>
            <form id="createGroupForm" class="space-y-4" action="DashBoard.php" method="post">
                <div>
                    <label for="Group_Name" class="block text-sm font-medium mb-1">Group Name</label>
                    <input type="text" id="Group_Name" name="Group_Name" placeholder="Enter a Group Name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="Group_Desc" class="block text-sm font-medium mb-1">Group Description</label>
                    <textarea id="Group_Desc" name="Group_Desc" placeholder="Enter your Group description"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <input type="hidden" id="memberCount" name="memberCount" value="">
                <button type="button" id="add"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors">Add
                    a member</button>

                <div id="Group_Members" class="space-y-2"></div>
                <input type="submit"
                    class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors" />
            </form>
        </div>
    </div>

    <div id="joinGroupPopup" class="popup">
        <div class="popup-content bg-white text-indigo-900 rounded-lg shadow-xl">
            <div class="close2 text-red-500 font-extrabold">x</div>
            <h2 class="text-3xl font-bold mb-6 text-center text-indigo-600">Join a Group</h2>
            <form id="joinGroupForm" class="space-y-4" action="JoinGroup.php" method="post">
                <div>
                    <label for="Group_Code" class="block text-sm font-medium mb-1">Group Code</label>
                    <input type="text" id="Group_Code" name="Group_Code" placeholder="Enter the Group Code"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">Join
                    Group</button>
            </form>
        </div>
    </div>


    <div id="joinQuizPopup" class="popup">
        <div class="popup-content bg-white text-indigo-900 rounded-lg shadow-xl">
            <div class="close text-gray-600 font-extrabold">&times;</div>
            <h2 class="text-3xl font-bold mb-6 text-center text-indigo-600">Join a Quiz</h2>
            <form id="joinQuizForm" class="space-y-4">
                <div>
                    <label for="quizCode" class="block text-sm font-medium mb-1">Quiz Code</label>
                    <input type="text" id="quizCode" name="quizCode" placeholder="Enter the quiz code"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit"
                    class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">Join
                    Quiz</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", () =>
        {
            const nav = document.getElementsByTagName("header")[0];
            const t1 = document.getElementById("text1");
            const t2 = document.getElementById("text2");
            const b1 = document.getElementById("b1");
            const b2 = document.getElementById("b2");
            const t3 = document.getElementById("text3");
            const b3 = document.getElementById("b3");
            const b4 = document.getElementById("b4");

            b1.classList.add("started");
            b2.classList.add("started");
            b3.classList.add("started");
            b4.classList.add("started");

            nav.style.opacity = 1;
            t1.style.right = 0;
            t1.style.opacity = 1;
            t2.style.right = 0;
            t2.style.opacity = 1;
            t3.style.left = 0;
            t3.style.opacity = 1;
            b1.style.right = 0;
            b1.style.opacity = 1;
            b2.style.right = 0;
            b2.style.opacity = 1;
            b3.style.left = 0;
            b3.style.opacity = 1;
            b4.style.left = 0;
            b4.style.opacity = 1;

            setTimeout(() =>
            {
                b1.classList.remove("started");
                b2.classList.remove("started");
                b1.classList.add("but");
                b2.classList.add("but");
                b3.classList.remove("started");
                b4.classList.remove("started");
                b3.classList.add("but");
                b4.classList.add("but");
            }, 1200);
        });

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

        // Group Popup
        const createGroupBtn = document.getElementById('b1');
        const createGroupPopup = document.getElementById('createGroupPopup');
        const closePopup = document.querySelector('.close');

        createGroupBtn.addEventListener('click', () =>
        {
            createGroupPopup.style.display = 'block';
        });

        closePopup.addEventListener('click', () =>
        {
            createGroupPopup.style.display = 'none';
        });
        function openJoinQuizPopup()
        {
            document.getElementById('joinQuizPopup').style.display = 'block';
        }

        document.querySelector('#joinQuizPopup .close').addEventListener('click', () =>
        {
            document.getElementById('joinQuizPopup').style.display = 'none';
        });

        document.getElementById('joinQuizForm').addEventListener('submit', (e) =>
        {
            e.preventDefault();
            const quizCode = document.getElementById('quizCode').value.trim();
            if (quizCode)
                window.location.href = `QuizArea.php?code=${encodeURIComponent(quizCode)}`;
            else
                alert('Please enter a valid quiz code.');
        });
        document.getElementById('b4').onclick = openJoinQuizPopup;


        // Join Group Popup
        const joinGroupBtn = document.getElementById('b2');
        const joinGroupPopup = document.getElementById('joinGroupPopup');
        const closepop = document.querySelector('.close2');
        joinGroupBtn.addEventListener('click', () =>
        {
            joinGroupPopup.style.display = 'block';
        });

        closepop.addEventListener('click', () =>
        {
            joinGroupPopup.style.display = 'none';
        });

        // Add member functionality
        let memberCount = 0;
        document.getElementById('add').addEventListener('click', () =>
        {
            memberCount++;
            const memberDiv = document.createElement('div');
            memberDiv.innerHTML = `
                <div class="flex space-x-2">
                    <input type="email" id="grp_member${memberCount}_email" name="grp_member${memberCount}_email" placeholder="Enter email of member ${memberCount}" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 mr-1">
                    <div class="flex items-center">
                        <label for="grp_member${memberCount}_isAdmin" class="mr-1">Is Admin?</label>
                        <input type="checkbox" id="grp_member${memberCount}_isAdmin" name="grp_member${memberCount}_isAdmin" value="yes" class="ml-2">
                    </div>
                </div>
            `;
            document.getElementById("memberCount").value = memberCount;
            document.getElementById('Group_Members').appendChild(memberDiv);
        });


        function confirmDeleteAccount()
        {
            if (confirm("Are you sure you want to delete this Account? This action cannot be undone."))
            {
                window.location.href = 'DeleteAccount.php';
            }
        }

    </script>
</body>

</html>