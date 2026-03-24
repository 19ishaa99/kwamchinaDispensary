<?php
session_start();
require "db.php";

/* 🔐 Security check */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

/* 🚫 Allow POST only */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../add_lab_test.php");
    exit;
}

/* 🧹 Collect & sanitize input */
$name        = trim($_POST['labTest'] ?? '');
$description = trim($_POST['description'] ?? '');
$cost        = (float) ($_POST['cost'] ?? 0);
$created_by  = (int) $_SESSION['user_id']; // ✅ FK-safe

/* 🛑 Validation */
if ($name === '' || $cost <= 0) {
    echo "<script>
        alert('Lab test name and valid cost are required');
        window.location.href='../add_lab_test.php';
    </script>";
    exit;
}

try {
    /* 🔁 Check uniqueness */
    $check = $pdo->prepare("
        SELECT lab_test_id 
        FROM lab_tests 
        WHERE name = ?
        LIMIT 1
    ");
    $check->execute([$name]);

    if ($check->fetch()) {
        echo "<script>
            alert('This lab test already exists!');
            window.location.href='../add_lab_test.php';
        </script>";
        exit;
    }

    /* 💾 Insert lab test */
    $stmt = $pdo->prepare("
        INSERT INTO lab_tests (name, description, cost, created_by)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $name,
        $description,
        $cost,
        $created_by
    ]);

    echo "<script>
        alert('Lab test added successfully');
        window.location.href='../labTest.php';
    </script>";

} catch (Exception $e) {
    echo "<script>
        alert('Error saving lab test: " . addslashes($e->getMessage()) . "');
        window.location.href='../add_lab_test.php';
    </script>";
}
