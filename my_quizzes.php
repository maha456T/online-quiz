<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'creator') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$creator_id = $_SESSION['userid'];
$sql = "SELECT quizid, title, description, created_at FROM quizq WHERE created_by = $creator_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Quizzes</title>
    <style>
        body { font-family: Arial; background: #f9f9f9; }
        table {
            width: 80%; margin: 40px auto; border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        th, td {
            padding: 12px; border: 1px solid #ccc; text-align: left;
        }
        th { background: #3498db; color: white; }
    </style>
</head>
<body>

<h2 style="text-align:center;">My Quizzes</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Description</th>
        <th>Created At</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['quizid'] ?></td>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= $row['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
