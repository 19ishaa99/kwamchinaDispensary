<?php
require 'database/db.php';

/*
|--------------------------------------------------------------------------
| Search handling
|--------------------------------------------------------------------------
*/
$search = $_GET['search'] ?? '';

$sql = "
SELECT 
    p.payment_id,
    p.amount,
    p.payment_date,
    pat.full_name AS patient_name
FROM payments p
JOIN patient_visits v ON p.visit_id = v.visit_id
JOIN patients pat ON v.patient_id = pat.patient_id
WHERE pat.full_name LIKE :search
ORDER BY p.payment_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Total collected
|--------------------------------------------------------------------------
*/
$totalStmt = $pdo->prepare("
    SELECT SUM(amount) 
    FROM payments p
    JOIN patient_visits v ON p.visit_id = v.visit_id
    JOIN patients pat ON v.patient_id = pat.patient_id
    WHERE pat.full_name LIKE :search
");
$totalStmt->execute(['search' => "%$search%"]);
$totalCollected = $totalStmt->fetchColumn() ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Payments</title>
  <link href="bootstrap.min.css" rel="stylesheet"/>
</head>
<body>

<?php include 'database/sidebar.php'; ?>

<div class="main-content">
  <div class="container mt-5">

    <h3 class="mb-4 text-primary">📄 Patient Payments</h3>

    <!-- 🔍 Search Bar -->
    <form method="GET" class="input-group mb-3">
      <input 
        type="text" 
        name="search"
        class="form-control" 
        placeholder="Search by patient name..."
        value="<?= htmlspecialchars($search) ?>"
      />
      <button class="btn btn-outline-secondary">🔍</button>
    </form>

    <!-- 📋 Payments Table -->
    <table class="table table-bordered table-hover">
      <thead class="table-info">
        <tr>
          <th>#</th>
          <th>Patient Name</th>
          <th>Amount Paid (TZS)</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>

        <?php if ($payments): ?>
          <?php foreach ($payments as $index => $row): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= htmlspecialchars($row['patient_name']) ?></td>
              <td><?= number_format($row['amount'], 2) ?></td>
              <td><?= $row['payment_date'] ?></td>
              
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted">
              No payments found
            </td>
          </tr>
        <?php endif; ?>

      </tbody>
    </table>

    <!-- 💰 Total Summary -->
    <div class="mt-3">
      <h5>
        Total Collected: 
        <span class="text-success">
          TSh <?= number_format($totalCollected, 2) ?>
        </span>
      </h5>
    </div>

    <!-- 🔁 Navigation -->
    <div class="mt-4">
      <a href="payment_form.php" class="btn btn-primary">
        💳 Record New Payment
      </a>
    </div>

  </div>
</div>

</body>
</html>
