<?php
session_start();

// Only allow creators (faculty)
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'creator') {
    header("Location: login.php");
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$creator_id = $_SESSION['userid'];
$quizid = isset($_GET['quizid']) ? intval($_GET['quizid']) : 0;

// Get creator's quizzes
$quiz_sql = "SELECT quizid, title FROM quizq WHERE created_by = $creator_id";
$quizzes = $conn->query($quiz_sql);

// Get submissions
$submissions = [];
if ($quizid > 0) {
    $sub_sql = "SELECT a.score, a.attempted_at, u.name, u.email
                FROM attemptq a
                JOIN userq u ON a.student_id = u.id
                WHERE a.quizid = $quizid";
    $submissions = $conn->query($sub_sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Submissions</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            box-shadow: 0 0 10px gray;
        }
        h2 {
            text-align: center;
        }
        select, button {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
        }
        table {
            width: 100%; border-collapse: collapse; margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc; padding: 10px;
            text-align: left;
        }
        th {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Quiz Submissions</h2>

    <form method="GET">
        <label>Select Quiz:</label>
        <select name="quizid" onchange="this.form.submit()" required>
            <option value="">-- Select Quiz --</option>
            <?php while ($row = $quizzes->fetch_assoc()): ?>
                <option value="<?= $row['quizid'] ?>" <?= ($quizid == $row['quizid']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['title']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($quizid > 0 && $submissions && $submissions->num_rows > 0): ?>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Score</th>
                <th>Attempted At</th>
            </tr>
            <?php while ($row = $submissions->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['score'] ?></td>
                    <td><?= $row['attempted_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php elseif ($quizid > 0): ?>
        <p>No submissions yet for this quiz.</p>
    <?php endif; ?>
</div>

</body>
</html>
