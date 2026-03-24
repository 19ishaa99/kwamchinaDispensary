<?php
require "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../view_medicine.php");
    exit;
}

$medicine_id = (int) $_POST['medicine_id'];
$name        = trim($_POST['name']);
$type        = trim($_POST['type']);
$unit_price  = (float) $_POST['unit_price'];
$expiry_date = $_POST['expiry_date'];

if ($medicine_id <= 0 || $name === "" || $unit_price < 0) {
    die("Invalid input");
}

try {
    $stmt = $pdo->prepare("
        UPDATE medicines
        SET name = ?, type = ?, unit_price = ?, expiry_date = ?
        WHERE medicine_id = ?
    ");
    $stmt->execute([
        $name,
        $type,
        $unit_price,
        $expiry_date,
        $medicine_id
    ]);

    header("Location: ../view_medicine.php?updated=1");
    exit;

} catch (Exception $e) {
    die("Error updating medicine: " . $e->getMessage());
}
