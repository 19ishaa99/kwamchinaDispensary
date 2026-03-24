<?php
require "db.php";

$type  = $_POST['report_type'] ?? '';
$start = $_POST['start_date'] ?? '';
$end   = $_POST['end_date'] ?? '';

if (!$type || !$start || !$end) {
    exit("<p class='text-danger'>Invalid request</p>");
}

echo "<p><strong>Date Range:</strong> $start to $end</p>";

/* ===============================
   MONTHLY INCOME / EXPENSE REPORT
   =============================== */
if ($type === "income_expense") {

    // Total income
    $stmt = $pdo->prepare("
        SELECT SUM(amount) 
        FROM payments 
        WHERE payment_date BETWEEN ? AND ?
    ");
    $stmt->execute([$start, $end]);
    $income = $stmt->fetchColumn() ?: 0;

    // Total expenses
    $stmt = $pdo->prepare("
        SELECT SUM(amount) 
        FROM expenses 
        WHERE expense_date BETWEEN ? AND ?
    ");
    $stmt->execute([$start, $end]);
    $expenses = $stmt->fetchColumn() ?: 0;

    $net = $income - $expenses;

    echo "
    <table class='table table-bordered mt-3'>
      <thead>
        <tr><th>Category</th><th>Amount (TZS)</th></tr>
      </thead>
      <tbody>
        <tr><td>Income</td><td>" . number_format($income) . "</td></tr>
        <tr><td>Expenses</td><td>" . number_format($expenses) . "</td></tr>
        <tr class='table-success'>
          <td><strong>Net Balance</strong></td>
          <td><strong>" . number_format($net) . "</strong></td>
        </tr>
      </tbody>
    </table>";
}

/* ===============================
   PATIENT RECORDS REPORT
   =============================== */
elseif ($type === "patient_records") {

    $stmt = $pdo->prepare("
        SELECT p.full_name, pv.visit_date
        FROM patient_visits pv
        JOIN patients p ON p.patient_id = pv.patient_id
        WHERE DATE(pv.visit_date) BETWEEN ? AND ?
        ORDER BY pv.visit_date ASC
    ");
    $stmt->execute([$start, $end]);

    echo "
    <table class='table table-bordered mt-3'>
      <thead>
        <tr>
          <th>Patient Name</th>
          <th>Visit Date</th>
        </tr>
      </thead>
      <tbody>";

    if ($stmt->rowCount() === 0) {
        echo "<tr><td colspan='2' class='text-center'>No records found</td></tr>";
    }

    while ($row = $stmt->fetch()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['full_name']) . "</td>
                <td>" . date('d M Y', strtotime($row['visit_date'])) . "</td>
              </tr>";
    }

    echo "</tbody></table>";
}
