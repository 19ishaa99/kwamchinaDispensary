<?php
require "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view_medicine.php");
    exit;
}

$medicine_id = (int) $_POST['medicine_id'];

if ($medicine_id <= 0) {
    die("Invalid medicine ID");
}

try {
    $pdo->beginTransaction();

    // 1️⃣ Delete stock first
    $stmt = $pdo->prepare("DELETE FROM medicine_stock WHERE medicine_id = ?");
    $stmt->execute([$medicine_id]);

    // 2️⃣ Delete medicine
    $stmt = $pdo->prepare("DELETE FROM medicines WHERE medicine_id = ?");
    $stmt->execute([$medicine_id]);

    $pdo->commit();

    header("Location: ../view_medicine.php?deleted=1");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting medicine: " . $e->getMessage());
}
