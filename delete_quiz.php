<?php
session_start();

if (!isset($_SESSION['userid']) || !in_array($_SESSION['role'], ['admin', 'creator'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['quizid'])) {
    echo "Invalid request.";
    exit;
}

$quizid = intval($_GET['quizid']);

// Database connection
$conn = new mysqli("localhost", "root", "", "collegems");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Delete quiz (and optionally related questions/attempts)
$conn->query("DELETE FROM attemptq WHERE quizid = $quizid"); // remove attempts first (foreign key relation)
$conn->query("DELETE FROM questionq WHERE quizid = $quizid"); // remove all questions of that quiz
$conn->query("DELETE FROM quizq WHERE quizid = $quizid"); // finally delete quiz itself

$conn->close();

header("Location: manage_quizzes.php");
exit;
?>
