<?php

session_start();
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Online Quiz System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            text-align: center;
            padding-top: 60px;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        a {
            text-decoration: none;
            display: inline-block;
            margin: 15px;
            padding: 12px 28px;
            color: #fff;
            background-color: #4CAF50;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #388e3c;
        }
        .logout {
            background-color: #e53935;
        }
        .logout:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
        <h2>Admin Dashboard</h2>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_quizzes.php">Manage Quizzes</a>
        <a href="view_results.php">View Results</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>