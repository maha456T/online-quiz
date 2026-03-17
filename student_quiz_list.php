<?php
session_start();

// Only allow students
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['userid'];

$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all quizzes (you can filter out attempted ones later)
$sql = "SELECT quizid, title, description, created_at FROM quizq ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Quizzes</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f0f2f5;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            box-shadow: 0 0 10px gray;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .quiz {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .quiz h3 {
            margin: 0;
            color: #3498db;
        }
        .quiz p {
            margin: 10px 0;
            color: #555;
        }
        .quiz .meta {
            font-size: 13px;
            color: #888;
        }
        .btn {
            padding: 10px 15px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn:hover {
            background: #27ae60;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Available Quizzes</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="quiz">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <p class="meta">Created on: <?= $row['created_at'] ?></p>
                <a href="start_quiz.php?quizid=<?= $row['quizid'] ?>" class="btn">Start Quiz</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No quizzes available right now.</p>
    <?php endif; ?>
</div>

</body>
</html>
