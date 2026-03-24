<?php
session_start();
require "db.php";

// 🔐 Security: only logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// 🚫 POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../expenses.php");
    exit;
}

// 🧹 Collect & sanitize input
$expense_title = trim($_POST['expense_title'] ?? '');
$amount        = (float) ($_POST['amount'] ?? 0);
$date          = $_POST['date'] ?? date('Y-m-d');
$issued_by     = trim($_POST['issued_by'] ?? '');
$category_name = trim($_POST['category'] ?? '');
$notes         = trim($_POST['notes'] ?? '');
$user_id       = $_SESSION['user_id']; // Logged-in user

// 🛑 Validation
if ($expense_title === '' || $amount <= 0 || $category_name === '') {
    echo "<script>
        alert('Title, amount, and category are required.');
        window.location.href='../expenses.php';
    </script>";
    exit;
}

try {
    $pdo->beginTransaction();

    // 1️⃣ Get category_id from expense_categories (create if doesn't exist)
    $stmt = $pdo->prepare("SELECT category_id FROM expense_categories WHERE name = ?");
    $stmt->execute([$category_name]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $category_id = $category['category_id'];
    } else {
        // Insert new category
        $stmt = $pdo->prepare("INSERT INTO expense_categories (name) VALUES (?)");
        $stmt->execute([$category_name]);
        $category_id = $pdo->lastInsertId();
    }

    // 2️⃣ Insert expense
    $stmt = $pdo->prepare("
        INSERT INTO expenses (category_id, amount, expense_date, description, user_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $category_id,
        $amount,
        $date,
        $notes ?: $expense_title, // use title as default description if notes empty
        $user_id
    ]);

    $pdo->commit();

    echo "<script>
        alert('Expense recorded successfully!');
        window.location.href='../expenses.php';
    </script>";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "<script>
        alert('Error saving expense: {$e->getMessage()}');
        window.location.href='../expenses.php';
    </script>";
}

$log = $pdo->prepare("
  INSERT INTO activity_logs (activity_type, description)
  VALUES ('expense', ?)
");
$log->execute(["Expense added: $category (TZS " . number_format($amount) . ")"]);

