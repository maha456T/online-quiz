<?php
session_start();

if (!isset($_SESSION['userid']) || !in_array($_SESSION['role'], ['admin', 'creator'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch quizzes with creator name
$sql = "
    SELECT q.quizid, q.title, q.created_at, u.name AS creator
    FROM quizq q
    LEFT JOIN userq u ON q.created_by = u.id
    ORDER BY q.quizid DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Quizzes</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            background: white;
            padding: 30px;
            margin: auto;
            box-shadow: 0 0 10px #aaa;
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        a.btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a.btn:hover {
            background: #1e8449;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #eee;
        }
        .action-btn {
            padding: 6px 12px;
            margin: 0 5px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            font-size: 14px;
        }
        .edit-btn {
            background: #2980b9;
        }
        .edit-btn:hover {
            background: #1f618d;
        }
        .delete-btn {
            background: #c0392b;
        }
        .delete-btn:hover {
            background: #922b21;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Manage Quizzes</h2>
    <!-- <a href="create_quiz.php" class="btn">+ Add New Quiz</a> -->

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Quiz ID</th>
                    <th>Title</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['quizid'] ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['creator']) ?: 'N/A' ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a class="action-btn edit-btn" href="edit_quiz.php?quizid=<?= $row['quizid'] ?>">Edit</a>
                        <a class="action-btn delete-btn" href="delete_quiz.php?quizid=<?= $row['quizid'] ?>" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No quizzes found.</p>
    <?php endif; ?>
</div>

</body>
</html>
