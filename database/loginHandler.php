<?php
session_start();
require "db.php";

if (isset($_POST['username'], $_POST['password'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Get user info
    $sql = "
        SELECT u.user_id, u.password, s.full_name, s.role, u.is_active
        FROM users u
        JOIN staff s ON u.staff_id = s.staff_id
        WHERE u.username = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['is_active'] == 0) {
            echo "<script>
                alert('Account not activated! Please activate first.');
                window.location.href='../activateAcccount.php';
            </script>";
            exit;
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];

            if ($user['role'] === 'admin') {
                header("Location: ../adminDashboard.php");
                exit;
            } else {
                header("Location: ../dashboard.php");
                exit;
            }
        } else {
            echo "<script>alert('Incorrect password'); window.location.href='../index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Username not found'); window.location.href='../index.php';</script>";
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
