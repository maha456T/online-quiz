<?php
session_start();

// Only allow logged-in students
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['userid'];

// Connect to the database
$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all quiz attempts by the student
$sql = "SELECT 
            q.title AS quiz_title, 
            a.score, 
            a.percentage, 
            a.quizid
        FROM attemptq a 
        JOIN quizq q ON a.quizid = q.quizid 
        WHERE a.student_id = $student_id
        ORDER BY a.quizid DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Quiz Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px gray;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Quiz Results</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Quiz Title</th>
                <th>Score</th>
                <th>Total Questions</th>
                <th>Percentage</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): 
                // Count total questions for each quiz
                $quizid = $row['quizid'];
                $qcount_sql = "SELECT COUNT(*) as total FROM questionq WHERE quizid = $quizid";
                $qcount_result = $conn->query($qcount_sql);
                $total_questions = ($qcount_result->num_rows > 0) ? $qcount_result->fetch_assoc()['total'] : 0;
            ?>
            <tr>
                <td><?= htmlspecialchars($row['quiz_title']) ?></td>
                <td><?= $row['score'] ?></td>
                <td><?= $total_questions ?></td>
                <td><?= $row['percentage'] ?>%</td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center;">You haven't attempted any quizzes yet.</p>
    <?php endif; ?>

    <div style="text-align:center;">
        <a href="student_home.php" class="back-btn">Back to Home</a>
    </div>
</div>

</body>
</html>
