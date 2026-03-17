<?php
session_start();

// Database Connection
$host = "localhost";
$user = "root";
$pass = "root123";
$dbname = "collegems";
$conn = new mysqli($host, $user, $pass, $dbname);

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];
    $department = ($role === 'student') ? null : $_POST['department'];

    $stmt = $conn->prepare("INSERT INTO userq (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $role, $department);

    if ($stmt->execute()) {
        $success = "Registered successfully! You can now <a href='login.php'>Login</a>";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Online Quiz System</title>
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
        input, select {
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
        .message { color: green; }
        .error { color: red; }
        #department-field { display: none; }
    </style>
    <script>
        function showDepartment(role) {
            document.getElementById("department-field").style.display = (role === "student") ? "none" : "block";
        }
    </script>
</head>
<body>
<div class="form-box">
    <h2>Register</h2>

    <?php if ($success): ?>
        <p class="message"><?= $success ?></p>
    <?php elseif ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>

        <select name="role" onchange="showDepartment(this.value)" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="creator">Creator</option>
            <option value="admin">Admin</option>
        </select><br>

        <div id="department-field">
            <input type="text" name="department" placeholder="Department"><br>
        </div>

        <button type="submit">Register</button>
    </form>
</div>
<script>
    // Keep department field in sync on reload
    document.addEventListener('DOMContentLoaded', function() {
        var role = document.querySelector('select[name="role"]').value;
        showDepartment(role);
    });
</script>
</body>
</html>