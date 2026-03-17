<?php
session_start();
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch all quiz results with student and quiz details
$sql = "
    SELECT 
        a.attemptid,
        u.name AS student_name,
        q.title AS quiz_title,
        a.score,
        a.percentage,
        a.attempted_at
    FROM attemptq a
    JOIN userq u ON a.student_id = u.id
    JOIN quizq q ON a.quizid = q.quizid
    ORDER BY a.attempted_at DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Quiz Results - Admin</title>
    <style>
        body {
            font-family: Arial;
            background: #f9f9f9;
            padding: 30px;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background-color: #f2f2f2;
        }
        .back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>All Quiz Results</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Quiz</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Attempted On</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                        <td><?= htmlspecialchars($row['quiz_title']) ?></td>
                        <td><?= $row['score'] ?></td>
                        <td><?= $row['percentage'] ?>%</td>
                        <td><?= $row['attempted_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No quiz results found.</p>
    <?php endif; ?>
    
    <a href="admin_home.php" class="back">← Back to Dashboard</a>
</div>

</body>
</html>
