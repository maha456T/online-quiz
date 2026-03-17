<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'creator') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$creator_id = $_SESSION['userid'];
$message = "";

// Fetch quizzes for this creator
$quizzes = $conn->query("SELECT quizid, title FROM quizq WHERE created_by = $creator_id");

// Insert question
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $quizid = isset($_POST['quizid']) ? intval($_POST['quizid']) : 0;
    if ($quizid <= 0) {
        $message = "Please select a valid quiz.";
    } else {
        $question = $conn->real_escape_string($_POST['question']);
        $a = $conn->real_escape_string($_POST['option_a']);
        $b = $conn->real_escape_string($_POST['option_b']);
        $c = $conn->real_escape_string($_POST['option_c']);
        $d = $conn->real_escape_string($_POST['option_d']);
        $correct = $conn->real_escape_string($_POST['correct']);

        $sql = "INSERT INTO questionq (quizid, question_text, option_a, option_b, option_c, option_d, correct_option)
                VALUES ('$quizid', '$question', '$a', '$b', '$c', '$d', '$correct')";
        if ($conn->query($sql)) {
            $message = "✅ Question added!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Question</title>
    <style>
        body { font-family: Arial; background: #f7f7f7; }
        .container { width: 90%; max-width: 500px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #aaa; }
        h2 { text-align: center; color: #333; }
        form { display: flex; flex-direction: column; gap: 10px; }
        select, textarea, input[type="text"] { padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 10px; background: #4CAF50; color: #fff; border: none; border-radius: 5px; font-size: 16px; }
        button:hover { background: #388e3c; }
        p { text-align: center; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h2>Add Question</h2>
    <form method="POST">
        <label>Select Quiz:</label>
        <select name="quizid" required>
            <option value="">-- Select Quiz --</option>
            <?php if ($quizzes && $quizzes->num_rows > 0): ?>
                <?php while ($row = $quizzes->fetch_assoc()): ?>
                    <option value="<?= $row['quizid'] ?>"><?= htmlspecialchars($row['title']) ?></option>
                <?php endwhile; ?>
            <?php else: ?>
                <option value="">No quizzes found. Please create a quiz first.</option>
            <?php endif; ?>
        </select>
        <textarea name="question" placeholder="Question text" required></textarea>
        <input type="text" name="option_a" placeholder="Option A" required>
        <input type="text" name="option_b" placeholder="Option B" required>
        <input type="text" name="option_c" placeholder="Option C" required>
        <input type="text" name="option_d" placeholder="Option D" required>
        <label>Correct Option:</label>
        <select name="correct" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>
        <button type="submit">Add</button>
    </form>
    <p><?= $message ?></p>
</div>
</body>
</html>