<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$userid = $_SESSION['userid'];
$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch student data safely (include role!)
$sql = "SELECT name, email, role, department, created_at FROM userq WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("No user found or query failed: " . $conn->error);
}

// Fetch quiz statistics for this student
$sqlStats = "SELECT COUNT(*) AS total_attempts, AVG(score) AS avg_score 
             FROM quiz_results WHERE user_id = ?";
$stmtStats = $conn->prepare($sqlStats);
$stmtStats->bind_param("i", $userid);
$stmtStats->execute();
$resultStats = $stmtStats->get_result();
$stats = $resultStats->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <style>
        body { font-family: Arial; background-color: #f0f0f0; }
        .container { width: 90%; max-width: 700px; margin: 50px auto; background: white; padding: 30px; box-shadow: 0 0 10px gray; }
        h2 { text-align: center; margin-bottom: 25px; }
        .profile-item { margin: 15px 0; font-size: 18px; }
        .label { font-weight: bold; display: inline-block; width: 180px; }
        .value { color: #333; }
        .stats { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ccc; }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Profile</h2>

    <div class="profile-item"><span class="label">Name:</span><span class="value"><?= htmlspecialchars($user['name']) ?></span></div>
    <div class="profile-item"><span class="label">Email:</span><span class="value"><?= htmlspecialchars($user['email']) ?></span></div>
    <div class="profile-item"><span class="label">Role:</span><span class="value"><?= ucfirst($user['role']) ?></span></div>
    <div class="profile-item"><span class="label">Department:</span><span class="value"><?= $user['department'] ? htmlspecialchars($user['department']) : 'N/A' ?></span></div>
    <div class="profile-item"><span class="label">Joined On:</span><span class="value"><?= $user['created_at'] ?></span></div>

    <div class="stats">
        <div class="profile-item"><span class="label">Total Quizzes Attempted:</span><span class="value"><?= $stats['total_attempts'] ?? 0 ?></span></div>
        <div class="profile-item"><span class="label">Average Score:</span><span class="value"><?= $stats['avg_score'] ? round($stats['avg_score'], 2) : '0' ?></span></div>
    </div>
</div>
</body>
</html>
