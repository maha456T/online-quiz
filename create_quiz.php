<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'creator') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $created_by = $_SESSION['userid'];

    $sql = "INSERT INTO quizq (title, description, created_by) 
            VALUES ('$title', '$description', $created_by)";

    if ($conn->query($sql)) {
        $message = "Quiz created successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Quiz</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .container {
            width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px gray;
        }
        input, textarea, button {
            width: 100%; padding: 10px; margin: 10px 0;
        }
        button { background: #3498db; color: white; border: none; }
    </style>
</head>
<body>
<div class="container">
    <h2>Create New Quiz</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Quiz Title" required>
        <textarea name="description" placeholder="Description (optional)"></textarea>
        <button type="submit">Create Quiz</button>
    </form>
    <p><?= $message ?></p>
</div>
</body>
</html>
