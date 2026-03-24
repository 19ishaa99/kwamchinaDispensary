<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../record_payment.php");
    exit;
}

$visit_id     = (int) ($_POST['visit_id'] ?? 0);
$amount       = (float) ($_POST['amount_paid'] ?? 0);
$payment_date = $_POST['payment_date'] ?? date('Y-m-d');
$user_id      = $_SESSION['user_id'];

if ($visit_id <= 0 || $amount <= 0) {
    echo "<script>
        alert('Invalid payment data');
        window.history.back();
    </script>";
    exit;
}

try {
    $pdo->beginTransaction();

    // Insert payment
    $stmt = $pdo->prepare("
        INSERT INTO payments (visit_id, amount, payment_date, user_id)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$visit_id, $amount, $payment_date, $user_id]);

    // Get visit total
    $stmt = $pdo->prepare("SELECT total_amount FROM patient_visits WHERE visit_id = ?");
    $stmt->execute([$visit_id]);
    $total_amount = (float) $stmt->fetchColumn();

    // Sum payments
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE visit_id = ?");
    $stmt->execute([$visit_id]);
    $total_paid = (float) $stmt->fetchColumn();

    // Mark paid if complete
    if ($total_paid >= $total_amount) {
        $stmt = $pdo->prepare("UPDATE patient_visits SET is_paid = 1 WHERE visit_id = ?");
        $stmt->execute([$visit_id]);
    }

    $pdo->commit();

    echo "<script>
        alert('Payment recorded successfully');
        window.location.href='../payment_form.php';
    </script>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script>
        alert('Error: " . addslashes($e->getMessage()) . "');
        window.history.back();
    </script>";
}

$log = $pdo->prepare("
  INSERT INTO activity_logs (activity_type, description)
  VALUES ('payment', ?)
");
$log->execute(["Payment received: $patientName (TZS " . number_format($amount) . ")"]);

