<?php
session_start();
require "db.php"; // your PDO connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name        = trim($_POST['med_name']);
    $type        = trim($_POST['med_type']); // you could validate: Tablet, Capsule, Syrup, Injection
    $quantity    = (int)$_POST['quantity'];
    $price       = (float)$_POST['price'];
    $expiry_date = $_POST['expiry_date'];
    $added_date;

    try {
        $pdo->beginTransaction();

        // 1️⃣ Insert into medicines table
        $stmt = $pdo->prepare("
            INSERT INTO medicines (name, type, unit_price,quantity)
            VALUES (?, ?, ?,?)
        ");
        $stmt->execute([$name, $type, $price,$quantity]);
        $medicine_id = $pdo->lastInsertId();

        // 2️⃣ Insert initial stock into medicine_stock
        $stmt = $pdo->prepare("
            INSERT INTO medicine_stock (medicine_id, quantity,expire_date)
            VALUES (?, ?,?)
        ");
        $stmt->execute([$medicine_id, $quantity,$expiry_date]);

        $pdo->commit();

        echo "<script>
                alert('Medicine added successfully!');
                window.location.href='../add_new_medicine.php';
              </script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>
                alert('Error adding medicine: " . $e->getMessage() . "');
                window.location.href='../add_new_medicine.php';
              </script>";
    }

} else {
    header("Location: ../add_new_medicine.php");
    exit;
}
?>
