<?php
session_start();

// Database Connection
$host = "localhost";
$user = "root";
$pass = "root123";
$dbname = "collegems";
$conn = new mysqli($host, $user, $pass, $dbname);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password, role, department FROM userq WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $name, $hashed_password, $role, $department);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['userid'] = $id;
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
            $_SESSION['department'] = $department;

            // Redirect based on role
            if ($role == 'admin') {
                header("Location: admin_home.php");
            } elseif ($role == 'creator') {
                header("Location: creator_home.php");
            } else {
                header("Location: student_home.php");
            }
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Online Quiz System</title>
    <style>
        body {
            font-family: Arial;
            background: #f0f0f0;
            padding-top: 60px;
            text-align: center;
        }
        .form-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            max-width: 400px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
        }
        button {
            padding: 10px 30px;
            background: #4CAF50;
            border: none;
            color: #fff;
            border-radius: 5px;
        }
        button:hover {
            background: #45a049;
        }
        .error { color: red; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Login</h2>
    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>