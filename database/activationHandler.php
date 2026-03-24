<?php
require "db.php";

if (isset($_POST['activate'])) {

    $username = $_POST['username'];
    $oldPass  = $_POST['old_password'];
    $newPass  = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    if ($newPass !== $confirm) {
        echo ("Passwords do not match");
        header("Location: ../activateAcccount.php");
    }

    $stmt = $pdo->prepare("
        SELECT user_id, password, is_active
        FROM users
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        die("Invalid username");
    }

    if ($user['is_active'] == 1) {
        echo ("Account already activated");
        header("Location: ../index.php");
    }

    if (!password_verify($oldPass, $user['password'])) {
        die("Invalid default password");
    }

    $hashed = password_hash($newPass, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        UPDATE users
        SET password = ?, is_active = 1, must_change_password = 0
        WHERE user_id = ?
    ");
    $stmt->execute([$hashed, $user['user_id']]);

    echo "✅ Account activated. You can now log in.";
    header("Location: ../index.php");
}
