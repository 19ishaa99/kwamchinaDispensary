<?php
session_start();
require "db.php";

$visit_id = $_SESSION['visit_id'] ?? null;
$user_id = $_SESSION['user_id'] ?? null; // the logged-in user

if (!$visit_id || !$user_id) {
    die("Visit ID or user not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tests = $_POST['test_name'] ?? [];
    $results = $_POST['result'] ?? [];
    $dates = $_POST['test_date'] ?? [];

    try {
        $stmt = $pdo->prepare("INSERT INTO patient_lab_tests (visit_id, lab_test_id, result, test_date, added_by) VALUES (?, ?, ?, ?, ?)");

        foreach ($tests as $i => $test_id) {
            $result = trim($results[$i] ?? '');
            $date = $dates[$i] ?? date('Y-m-d');
            if ($test_id && $result) {
                $stmt->execute([$visit_id, $test_id, $result, $date, $user_id]);
            }
        }

        echo "<script>alert('Lab tests saved'); window.location.href='../patient_registration_form.php';</script>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
