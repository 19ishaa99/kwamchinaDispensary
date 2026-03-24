<?php
require "db.php";
if (isset($_POST['create'])) {

    $full_name    = $_POST['full_name'];
    $staff_number = $_POST['staff_number'];
    $role         = $_POST['role'];
    $username     = $_POST['username'];

    try {
        $pdo->beginTransaction();

        // Insert staff
        $stmt = $pdo->prepare("
            INSERT INTO staff (full_name, staff_number, role)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$full_name, $staff_number, $role]);
        $staff_id = $pdo->lastInsertId();

        // Insert user (inactive)
        $defaultPassword = password_hash("Activate@123", PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (staff_id, username, password, must_change_password, is_active)
            VALUES (?, ?, ?, 1, 0)
        ");
        $stmt->execute([$staff_id, $username, $defaultPassword]);

     $pdo->commit();
header("Location: ../register.php?success=1");
exit;


    } catch (Exception $e) {
        $pdo->rollBack();
        echo "❌ Error: " . $e->getMessage();
    }
}
