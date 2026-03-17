<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Quiz System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            text-align: center;
            padding-top: 80px;
        }
        h1 {
            color: #333;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: auto;
        }
        a {
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            color: white;
            background-color: #4CAF50;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #45a049;
        }
        .role-info {
            margin-top: 20px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the Online Quiz System</h1>

        <?php if (isset($_SESSION['userid'])): ?>
            <div class="role-info">
                Hello, <strong><?= $_SESSION['name'] ?></strong>!<br>
                Role: <strong><?= ucfirst($_SESSION['role']) ?></strong>
            </div>

            <?php
                if ($_SESSION['role'] === 'admin') {
                    echo "<a href='admin_home.php'>Admin Dashboard</a>";
                } elseif ($_SESSION['role'] === 'creator') {
                    echo "<a href='creator_home.php'>Creator Dashboard</a>";
                } elseif ($_SESSION['role'] === 'student') {
                    echo "<a href='student_home.php'>Student Dashboard</a>";
                }
            ?>

            <a href="logout.php">Logout</a>

        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</body>
</html>
