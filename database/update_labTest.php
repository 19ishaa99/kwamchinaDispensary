<?php
session_start();
require "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

$lab_test_id = (int) $_POST['lab_test_id'];
$name        = trim($_POST['name']);
$description = trim($_POST['description']);
$cost        = (float) $_POST['cost'];

if ($name === '' || $cost <= 0) {
    die("Invalid input");
}

try {
    $stmt = $pdo->prepare("
        UPDATE lab_tests
        SET name = ?, description = ?, cost = ?
        WHERE lab_test_id = ?
    ");
    $stmt->execute([$name, $description, $cost, $lab_test_id]);

    // Optional activity log
    $log = $pdo->prepare("
        INSERT INTO activity_logs (activity_type, description)
        VALUES ('lab', ?)
    ");
    $log->execute(["Lab test updated: $name"]);

    header("Location: ../view_labTest.php?updated=1");
    exit;

} catch (PDOException $e) {
    die("Update failed: " . $e->getMessage());
}
