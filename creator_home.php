<?php
// File: creator_home.php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'creator') {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$user = "root";
$pass = "root123";
$dbname = "collegems"; // Change to your actual DB name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$creator_id = $_SESSION['userid'];
$sql = "SELECT name, email, department, created_at FROM userq WHERE id = $creator_id";
$result = $conn->query($sql);
$creator = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Home - Quiz Creator</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f0f0;
        }
        .container {
            width: 90%;
            max-width: 700px;
            margin: 40px auto;
            background-color: white;
            padding: 25px;
            box-shadow: 0px 0px 10px gray;
        }
        h2 {
            text-align: center;
        }
        .info {
            padding: 10px;
            background-color: #eee;
            margin-bottom: 20px;
        }
        .menu a {
            display: block;
            background-color: #3498db;
            color: white;
            padding: 12px;
            margin: 10px 0;
            text-decoration: none;
            text-align: center;
        }
        .menu a:hover {
            background-color: #2c80b4;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?php echo $creator['name']; ?> (Quiz Creator)</h2>

    <div class="info">
        <strong>Email:</strong> <?php echo $creator['email']; ?><br>
        <strong>Department:</strong> <?php echo $creator['department']; ?><br>
        <strong>Joined:</strong> <?php echo $creator['created_at']; ?>
    </div>

    <div class="menu">
        <a href="create_quiz.php">Create New Quiz</a>
        <a href="add_questions.php">Add Questions to Quiz</a>
        <a href="my_quizzes.php">My Quizzes</a>
        <a href="quiz_submissions.php">View Submissions</a>
        <a href="view_questions.php">View Questions</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
