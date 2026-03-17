<?php
session_start();

if (!isset($_SESSION['userid']) || $_SESSION['role'] !== 'creator') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "root123", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$creator_id = $_SESSION['userid'];
$quizid = isset($_GET['quizid']) ? intval($_GET['quizid']) : 0;

// Get list of quizzes by creator
$quizzes = $conn->query("SELECT quizid, title FROM quizq WHERE created_by = $creator_id");

// Get questions if quiz is selected
$questions = [];
if ($quizid > 0) {
    $q = "SELECT * FROM questionq WHERE quizid = $quizid";
    $questions = $conn->query($q);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Questions</title>
    <style>
        body { font-family: Arial; background: #f2f2f2; }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            box-shadow: 0 0 10px gray;
        }
        select, button {
            padding: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%; border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc; padding: 10px;
            text-align: left;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        .question {
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>View Questions by Quiz</h2>

    <form method="GET">
        <label>Select Quiz:</label>
        <select name="quizid" onchange="this.form.submit()" required>
            <option value="">-- Select Quiz --</option>
            <?php while ($row = $quizzes->fetch_assoc()): ?>
                <option value="<?= $row['quizid'] ?>" <?= ($quizid == $row['quizid']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['title']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($quizid > 0 && $questions && $questions->num_rows > 0): ?>
        <table>
            <tr>
                <th>#</th>
                <th>Question</th>
                <th>Options</th>
                <th>Correct</th>
            </tr>
            <?php $count = 1; while ($q = $questions->fetch_assoc()): ?>
            <tr>
                <td><?= $count++ ?></td>
                <td class="question"><?= htmlspecialchars($q['question_text']) ?></td>
                <td>
                    A. <?= htmlspecialchars($q['option_a']) ?><br>
                    B. <?= htmlspecialchars($q['option_b']) ?><br>
                    C. <?= htmlspecialchars($q['option_c']) ?><br>
                    D. <?= htmlspecialchars($q['option_d']) ?>
                </td>
                <td><?= $q['correct_option'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php elseif ($quizid > 0): ?>
        <p>No questions found for this quiz.</p>
    <?php endif; ?>
</div>

</body>
</html>
