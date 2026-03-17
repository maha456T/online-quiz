<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['userid'];
$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch student data safely
$sql = "SELECT name, email, department, created_at FROM userq WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("No user found or query failed: " . $conn->error);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Student Home</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f7f7f7;
        }
        .container {
            width: 90%;
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px #aaa;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .info {
            margin: 15px 0;
            font-size: 18px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .btn-group {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .btn {
            padding: 12px;
            text-align: center;
            background: #3498db;
            color: white;
            border: none;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>

    <div class="info">
        <span class="label">Email:</span>
        <?= htmlspecialchars($user['email']) ?>
    </div>
    <div class="info">
        <span class="label">Role:</span>
        Student
    </div>
    <div class="info">
        <span class="label">Department:</span>
        <?= $user['department'] ? htmlspecialchars($user['department']) : 'N/A' ?>
    </div>
    <div class="info">
        <span class="label">Joined:</span>
        <?= $user['created_at'] ?>
    </div>

    <div class="btn-group">
        <a href="student_profile.php" class="btn">View Profile</a>
        <a href="student_quiz_list.php" class="btn">Take Quiz</a>
        <a href="student_results.php" class="btn">View Results</a>
        <a href="logout.php" class="btn" style="background:#e74c3c;">Logout</a>
    </div>
</div>

</body>
</html>
