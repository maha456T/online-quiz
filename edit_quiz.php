<?php
session_start();
if (!isset($_SESSION['userid']) || !in_array($_SESSION['role'], ['admin', 'creator'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$quizid = isset($_GET['quizid']) ? intval($_GET['quizid']) : 0;
if (!$quizid) {
    echo "Invalid Quiz ID.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = trim($_POST['title']);
    if ($new_title === '') {
        $error = "Title cannot be empty.";
    } else {
        $stmt = $conn->prepare("UPDATE quizq SET title = ? WHERE quizid = ?");
        $stmt->bind_param("si", $new_title, $quizid);
        $stmt->execute();
        header("Location: manage_quizzes.php");
        exit;
    }
}

// Fetch current quiz data
$stmt = $conn->prepare("SELECT title FROM quizq WHERE quizid = ?");
$stmt->bind_param("i", $quizid);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Quiz not found.";
    exit;
}
$quiz = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Quiz</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 40px;
        }
        .container {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #2980b9;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .back {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        .back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Quiz Title</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="title">Quiz Title:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($quiz['title']) ?>" required>

        <button type="submit">Update Quiz</button>
    </form>

    <a href="manage_quizzes.php" class="back">← Back to Quiz List</a>
</div>

</body>
</html>
