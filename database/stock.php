<?php
session_start();
require "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: add_stock.php");
    exit;
}

$medicine_id = (int) $_POST['medicine_id'];
$quantity    = (int) $_POST['quantity'];

if ($medicine_id <= 0 || $quantity <= 0) {
    die("Invalid input");
}

try {
    // Update existing stock
    $stmt = $pdo->prepare("
        UPDATE medicine_stock
        SET quantity = quantity + ?
        WHERE medicine_id = ?
    ");
    $stmt->execute([$quantity, $medicine_id]);

    if ($stmt->rowCount() === 0) {
        die("Stock record not found for this medicine.");
    }

    header("Location: ../medicine_form.php?stock_updated=1");
    exit;

} catch (Exception $e) {
    die("Error updating stock: " . $e->getMessage());
}
