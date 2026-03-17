<?php
session_start();

// Redirect if not logged in or not a student
if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['userid'];

// Connect to DB
$conn = new mysqli("localhost", "root", "", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get quiz ID
$quizid = isset($_GET['quizid']) ? intval($_GET['quizid']) : 0;
if (!$quizid) {
    echo "Quiz not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check for previous attempt
    $check = $conn->prepare("SELECT attemptid FROM attemptq WHERE student_id = ? AND quizid = ?");
    $check->bind_param("ii", $student_id, $quizid);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('You have already attempted this quiz.'); window.location='student_results.php';</script>";
        exit;
    }

    // Count total questions
    $total_questions = $conn->query("SELECT COUNT(*) as total FROM questionq WHERE quizid = $quizid")->fetch_assoc()['total'];

    // Score calculation
    $score = 0;
    $questions = $conn->query("SELECT questionid, correct_option FROM questionq WHERE quizid = $quizid");
    while ($row = $questions->fetch_assoc()) {
        $qid = $row['questionid'];
        $correct = $row['correct_option'];
        $given = isset($_POST["q$qid"]) ? $_POST["q$qid"] : '';
        if ($given === $correct) $score++;
    }

    // Calculate percentage
    $percentage = $total_questions > 0 ? round(($score / $total_questions) * 100, 2) : 0.00;

    // Save result
    $stmt = $conn->prepare("INSERT INTO attemptq (student_id, quizid, score, percentage) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $student_id, $quizid, $score, $percentage);
    $stmt->execute();

    echo "<script>alert('Quiz submitted! You scored $score out of $total_questions (" . $percentage . "%)'); window.location='student_results.php';</script>";
    exit;
}

// Fetch quiz title
$quiz = $conn->query("SELECT title FROM quizq WHERE quizid = $quizid")->fetch_assoc();

// Fetch questions
$questions = $conn->query("SELECT * FROM questionq WHERE quizid = $quizid ORDER BY questionid");
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($quiz['title']) ?> - Quiz</title>
    <style>
        body {
            font-family: Arial;
            background: #f9f9f9;
        }
        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 10px gray;
        }
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .question {
            margin-bottom: 25px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .question p {
            margin: 0 0 10px;
        }
        label {
            display: block;
            margin: 5px 0;
        }
        button {
            padding: 12px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        button:hover {
            background: #2980b9;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #3498db;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($quiz['title']) ?></h2>

    <form method="POST">
        <?php 
        $qno = 1; 
        if ($questions->num_rows > 0):
            while ($row = $questions->fetch_assoc()): ?>
            <div class="question">
                <p><strong>Q<?= $qno++ ?>:</strong> <?= htmlspecialchars($row['question_text']) ?></p>
                <label><input type="radio" name="q<?= $row['questionid'] ?>" value="A" required> <?= htmlspecialchars($row['option_a']) ?></label>
                <label><input type="radio" name="q<?= $row['questionid'] ?>" value="B"> <?= htmlspecialchars($row['option_b']) ?></label>
                <label><input type="radio" name="q<?= $row['questionid'] ?>" value="C"> <?= htmlspecialchars($row['option_c']) ?></label>
                <label><input type="radio" name="q<?= $row['questionid'] ?>" value="D"> <?= htmlspecialchars($row['option_d']) ?></label>
            </div>
        <?php endwhile;
        else: ?>
            <p>No questions available for this quiz.</p>
        <?php endif; ?>

        <?php if ($questions->num_rows > 0): ?>
            <button type="submit">Submit Quiz</button>
        <?php endif; ?>
    </form>

    <a class="back-link" href="student_home.php">← Back to Home</a>
</div>

</body>
</html>
