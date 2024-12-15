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
$hashedgroupcode = $_GET['title'] ?? '';
$groupcode = '';
$Query = "SELECT GroupCode FROM QuizGroups";
$result = mysqli_query($conn, $Query);
while ($data = mysqli_fetch_assoc($result)) {
    if (password_verify($data['GroupCode'], $hashedgroupcode)) {
        $groupcode = $data["GroupCode"];
        break;
    }
}
if ($groupcode == '') {
    $classroomDetails = null;
} else {
    $Query = "SELECT * FROM QuizGroups WHERE GroupCode='$groupcode'";
    $result = mysqli_query($conn, $Query);
    $groupdata = mysqli_fetch_assoc($result);

    $Query = "SELECT User.* FROM User 
              JOIN GroupMembers ON User.UserID = GroupMembers.UserID 
              WHERE GroupMembers.GroupID = {$groupdata['GroupID']} AND GroupMembers.IsAdmin = 1";
    $teacherName = "";
    $result = mysqli_query($conn, $Query);
    while ($row = mysqli_fetch_assoc($result)) {
        if ($teacherName != "") {
            $teacherName = $teacherName . " , ";
        }
        $teacherName = $teacherName . $row['Name'];
    }
    $Query = "SELECT User.* FROM User 
              JOIN GroupMembers ON User.UserID = GroupMembers.UserID 
              WHERE GroupMembers.GroupID = {$groupdata['GroupID']} AND GroupMembers.IsAdmin = 1 And User.UserID={$_SESSION['userid']}";
    $result = mysqli_query($conn, $Query);
    $num = mysqli_num_rows($result);
    if ($num == 1) {
        $isAdmin = 1;
    } else {
        $isAdmin = 0;
    }
    $Query = "SELECT *, 
              CONCAT(StartDate, ' ', StartTime) AS StartDateTime,
              CONCAT(DueDate, ' ', DueTime) AS DueDateTime 
              FROM Quiz 
              WHERE GroupID = {$groupdata['GroupID']} 
              ORDER BY StartDate ASC, StartTime ASC";
    $result = mysqli_query($conn, $Query);
    $quizzes = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $now = new DateTime();
    $upcomingQuiz = null;
    foreach ($quizzes as $quiz) {
        $startDateTime = new DateTime($quiz['StartDateTime']);
        $dueDateTime = new DateTime($quiz['DueDateTime']);
        if ($startDateTime > $now) {
            $upcomingQuiz = $quiz;
            break;
        } elseif ($startDateTime <= $now && $dueDateTime > $now) {
            $upcomingQuiz = $quiz;
            break;
        }
    }

    $leaderboard = [];
    $results = mysqli_query($conn, "SELECT u.Name, r.score FROM Results r JOIN User u ON r.UserID = u.UserID WHERE r.GroupID = {$groupdata['GroupID']} GROUP BY u.Name ORDER BY r.submission LIMIT 5");
    if ($results && $results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {
            $leaderboard[] = ['name' => $row['Name'], 'score' => $row['score']];
        }
    }

    $classroomDetails = [
        'description' => $groupdata['Description'],
        'quizzes' => $quizzes,
        'upcoming_quiz' => $upcomingQuiz,
        'instructor' => $teacherName,
        'recent_scores' => [
            ['title' => 'Linear Equations', 'score' => '85%'],
            ['title' => 'Geometry Fundamentals', 'score' => '92%'],
        ],
        'leaderboard' => $leaderboard,
        'color' => 'from-blue-500 to-blue-700',
    ];
}

function formatDateTime($dateTime)
{
    return date('F j, Y g:i A', strtotime($dateTime));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($groupdata['Name'] ?? 'Classroom Details'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
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
    </style>
</head>

<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex flex-col">
    <header class="bg-indigo-800 shadow-lg w-full z-10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center justify-between flex-wrap">
                <div class="flex items-center flex-shrink-0 text-white mr-6 cursor-pointer"
                    onclick="window.location.href='index.php'">
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
                        <a href="DashBoard.php"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Dashboard
                        </a>
                        <a href="DashBoard.php#groups"
                            class="block mt-4 lg:inline-block lg:mt-0 text-indigo-200 hover:text-white mr-4">
                            Groups
                        </a>
                    </div>
                    <div>
                        <?php
                        echo "<span class='mr-4 text-white'>👋 Welcome " . htmlspecialchars($_SESSION['name']) . "</span>";
                        ?>
                        <a href="LogOut.php"
                            class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-indigo-500 hover:bg-white mt-4 lg:mt-0 transition duration-300">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="flex-grow flex flex-col px-4 py-8">
        <?php if ($classroomDetails): ?>
            <div class="container mx-auto max-w-6xl">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                    <div class="bg-gradient-to-r <?php echo $classroomDetails['color']; ?> p-6 text-white relative">
                        <h1 class="text-3xl md:text-4xl font-bold mb-2"><?php echo htmlspecialchars($groupdata['Name']); ?>
                        </h1>
                        <p class="text-lg md:text-xl"><?php echo htmlspecialchars($classroomDetails['description']); ?></p>
                        <p class="text-sm md:text-base mt-2">Instructor:
                            <?php echo htmlspecialchars($classroomDetails['instructor']); ?>
                        </p>
                        <?php
                        if ($isAdmin == 1) {
                            echo "<p class='text-sm md:text-base mt-2'>Group Code: <span id='groupCode' class='cursor-pointer underline' onclick='copyGroupCode()'>" . htmlspecialchars($groupcode) . "</span></p>";
                            echo "<div class='absolute top-2 right-2 p-4 flex space-x-4'>";
                            echo "<div class='bg-yellow-500 rounded-lg shadow-md p-2'>";
                            echo "<button id='CreateQuiz' class='text-white font-bold' onclick='createQuiz()'>Create Quiz</button>";
                            echo "</div>";
                            echo "<div class='bg-green-500 rounded-lg shadow-md p-2'>";
                            echo "<button id='AddMember' class='text-white font-bold'>Add Members</button>";
                            echo "</div>";
                            echo "<div class='bg-red-500 rounded-lg shadow-md p-2'>";
                            echo "<button onclick='confirmDelete()' class='text-white font-bold'>Delete Group</button>";
                            echo "</div>";
                            echo "</div>";
                        } else {
                            echo "<div class='absolute top-2 right-2 p-4 bg-red-500 rounded-lg shadow-md'>";
                            echo "<button onclick='confirmUnenroll()' class='text-white font-bold'>Unenroll</button>";
                            echo "</div>";
                        }
                        ?>
                        
                        <style>
                            @media (max-width: 768px) {
                                .absolute.top-2.right-2.p-4 {
                                    position: static;
                                    margin-top: 1rem;
                                }
                            }
                        </style>
                    </div>
                    <div class="p-4 md:p-6 space-y-4 md:space-y-6">


                        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 rounded-r-lg">
                            <h2 class="text-lg md:text-xl font-semibold mb-2 text-blue-800">Upcoming Quiz</h2>
                            <?php if ($classroomDetails['upcoming_quiz']): ?>
                                <div id="quizTimer" class="text-2xl font-bold text-blue-800 mt-2">df</div>
                                <p class="text-blue-700">
                                    <?php echo htmlspecialchars($classroomDetails['upcoming_quiz']['Name']); ?>
                                </p>
                                <p class="text-blue-700 mt-2">Starts:
                                    <?php echo formatDateTime($classroomDetails['upcoming_quiz']['StartDateTime']); ?>
                                </p>
                                <p class="text-blue-700">Due:
                                    <?php echo formatDateTime($classroomDetails['upcoming_quiz']['DueDateTime']); ?>
                                </p>
                                <a href="QuizArea.php?code=<?php echo urlencode($classroomDetails['upcoming_quiz']['QuizCode']); ?>"
                                    class="mt-2 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                                    Start Quiz
                                </a>
                            <?php else: ?>
                                <p class="text-blue-700">No upcoming quizzes</p>
                            <?php endif; ?>
                        </div>

                        <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded-r-lg">
                            <h2 class="text-lg md:text-xl font-semibold mb-2 text-yellow-800">All Quizes</h2>
                            <div class="space-y-4" id="Allquizes">
                                <?php foreach ($classroomDetails['quizzes'] as $index => $quiz): ?>
                                    <div class="bg-white p-4 rounded-lg shadow">
                                        <h3 class="text-lg font-semibold text-gray-800 cursor-pointer"
                                            onclick="toggleQuizDescription(<?php echo $index; ?>)">
                                            <?php echo htmlspecialchars($quiz['Name']); ?>
                                        </h3>
                                        <div id="quizDescription<?php echo $index; ?>" class="hidden mt-2 text-gray-600">
                                            <p><?php echo htmlspecialchars($quiz['Description']); ?></p>
                                            <p class="mt-2">Starts: <?php echo formatDateTime($quiz['StartDateTime']); ?></p>
                                            <p>Due: <?php echo formatDateTime($quiz['DueDateTime']); ?></p>
                                            <?php $hashedquizcode = password_hash($quiz['QuizCode'], PASSWORD_DEFAULT); ?>
                                            <a href="QuizArea.php?code=<?php echo urlencode($hashedquizcode); ?>"
                                                class="mt-2 inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                                                Start Quiz
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div
                            class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="QuizDetails.php"
                                class="w-full sm:w-auto bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded transition duration-300 text-center">
                                View All Quizzes
                            </a>
                            <a href="DashBoard.php"
                                class="w-full sm:w-auto text-indigo-600 hover:text-indigo-800 font-semibold transition duration-300 text-center">
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-4 md:p-6">
                        <h2 class="text-xl md:text-2xl font-semibold mb-4 text-gray-800">Leaderboard</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Rank</th>
                                        <th class="py-2 px-4 text-left">Name</th>
                                        <th class="py-2 px-4 text-left">Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (empty($classroomDetails['leaderboard'])) {
                                        echo '<tr><td colspan="3" class="text-center">No leaderboard data available</td></tr>';
                                    } else {
                                        foreach ($classroomDetails['leaderboard'] as $index => $leader):
                                            $leaderName = isset($leader['name']) ? htmlspecialchars($leader['name']) : 'Unknown';
                                            $leaderScore = isset($leader['score']) ? htmlspecialchars($leader['score']) : 'N/A';
                                            ?>
                                            <tr class="<?php echo $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?>">
                                                <td class="py-2 px-4"><?php echo $index + 1; ?></td>
                                                <td class="py-2 px-4"><?php echo $leaderName; ?></td>
                                                <td class="py-2 px-4"><?php echo $leaderScore; ?></td>
                                            </tr>
                                            <?php
                                        endforeach;
                                    }
                                    ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="container mx-auto max-w-2xl">
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4">Classroom Not Found</h1>
                    <p class="text-lg md:text-xl text-gray-600 mb-6">Sorry, we couldn't find any details for this classroom.
                    </p>
                    <a href="DashBoard.php"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                        Return to Dashboard
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <div id="AddMemberPopup" class="popup">
            <div class="popup-content bg-white text-indigo-900 rounded-lg shadow-xl">
                <div class="close text-red-500 font-extrabold">x</div>
                <h2 class="text-3xl font-bold mb-6 text-center text-indigo-600">Add Members</h2>
                <form id="AddMemberForm" class="space-y-4" action="AddMember.php" method="post">
                    <input type="hidden" id="memberCount" name="memberCount" value="">
                    <input type="hidden" id="Group_Code" name="Group_Code"
                        value="<?php echo htmlspecialchars($groupcode); ?>">
                    <button type="button" id="add"
                        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors">Add
                        a member</button>

                    <div id="Group_Members" class="space-y-2"></div>
                    <input type="submit"
                        class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors" />
                </form>
            </div>
        </div>
    </main>

    <script>
        // Toggle mobile menu
        const navToggle = document.getElementById('nav-toggle');
        const navContent = document.getElementById('nav-content');
        navToggle.addEventListener('click', () =>
        {
            navContent.classList.toggle('hidden');
        });

        function confirmDelete()
        {
            if (confirm("Are you sure you want to delete this group? This action cannot be undone."))
            {
                <?php
                $deletehashedcode = password_hash($groupcode, PASSWORD_DEFAULT);
                ?>
                const sendgroupCode = "<?php echo htmlspecialchars($deletehashedcode); ?>";
                window.location.href = 'DeleteGroup.php?code=' + sendgroupCode;
            }
        }

        function confirmUnenroll()
        {
            if (confirm("Are you sure you want to Unenroll this group? This action cannot be undone."))
            {
                <?php
                $deletehashedcode = password_hash($groupcode, PASSWORD_DEFAULT);
                ?>
                const sendgroupCode = "<?php echo htmlspecialchars($deletehashedcode); ?>";
                window.location.href = 'DeleteGroup.php?code=' + sendgroupCode;
            }
        }

        function copyGroupCode()
        {
            const groupCode = document.getElementById('groupCode').textContent;
            navigator.clipboard.writeText(groupCode).then(() =>
            {
                alert('Group code copied to clipboard!');
            }).catch(err =>
            {
                console.error('Failed to copy text: ', err);
            });
        }

        function toggleQuizDescription(index)
        {
            const description = document.getElementById(`quizDescription${index}`);
            description.classList.toggle('hidden');
        }

        function createQuiz()
        {
            window.location.href = 'QuizCreate.php?title=<?php echo $groupdata['GroupID']; ?>';
        }


        setTimeout(() =>
        {
            // Add Member Popup
            const AddMemberBtn = document.getElementById('AddMember');
            const AddMemberPopup = document.getElementById('AddMemberPopup');
            const closePopup = document.querySelector('.close');
            AddMemberBtn.addEventListener('click', () =>
            {
                AddMemberPopup.style.display = 'block';
            });

            closePopup.addEventListener('click', () =>
            {
                AddMemberPopup.style.display = 'none';
            });

            let memberCount = 0;
            document.getElementById('add').addEventListener('click', () =>
            {
                memberCount++;
                const memberDiv = document.createElement('div');
                memberDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <input type="email" id="grp_member${memberCount}_email" name="grp_member${memberCount}_email" placeholder="Enter email of member ${memberCount}" class="flex-1 w-80 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 mr-2">
                
                <div class="flex items-center">
                    <label for="grp_member${memberCount}_isAdmin" class="mr-2">Is Admin?</label>
                    <input type="checkbox" id="grp_member${memberCount}_isAdmin" name="grp_member${memberCount}_isAdmin" value="yes" class="ml-2">
                </div>
            </div>
            `;
                document.getElementById("memberCount").value = memberCount;
                document.getElementById('Group_Members').appendChild(memberDiv);
            });
        }, 2000);

        // Quiz timer
        <?php if ($classroomDetails && $classroomDetails['upcoming_quiz']): ?>
            const quizStartDate = new Date("<?php echo $classroomDetails['upcoming_quiz']['StartDateTime']; ?>").getTime();
            const quizDueDate = new Date("<?php echo $classroomDetails['upcoming_quiz']['DueDateTime']; ?>").getTime();

            function updateTimer()
            {
                const now = new Date().getTime();
                let distance, timerText;

                if (now < quizStartDate)
                {
                    distance = quizStartDate - now;
                    timerText = "Quiz starts in: ";
                } else if (now < quizDueDate)
                {
                    distance = quizDueDate - now;
                    timerText = "Quiz ends in: ";
                } else
                {
                    document.getElementById("quizTimer").innerHTML = "Quiz has ended";
                    clearInterval(timerInterval);
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById("quizTimer").innerHTML = `${timerText}${days}d ${hours}h ${minutes}m ${seconds}s`;
            }

            updateTimer();
            const timerInterval = setInterval(updateTimer, 1000);
        <?php endif; ?>
    </script>
</body>

</html>