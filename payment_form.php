<?php
require "database/db.php";

/* Fetch unpaid visits */
$stmt = $pdo->query("
    SELECT 
        pv.visit_id,
        p.full_name,
        pv.visit_date,
        pv.total_amount
    FROM patient_visits pv
    JOIN patients p ON pv.patient_id = p.patient_id
    WHERE pv.is_paid = 0
    ORDER BY pv.visit_date DESC
");
$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Record Payment</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>

<?php include 'database/sidebar.php'; ?>
<div class="main-content">
<div class="container mt-5">

<div class="card shadow-sm">
  <div class="card-header bg-info text-white">🧾 Record Patient Payment</div>
  <div class="card-body">

    <form action="database/payment.php" method="POST" class="row g-3">

      <!-- UNPAID VISITS DROPDOWN -->
      <div class="col-md-6">
        <label class="form-label">Unpaid Patient Visit</label>
        <select name="visit_id" class="form-select" required>
          <option value="">-- Select Patient Visit --</option>
          <?php foreach ($visits as $v): ?>
            <option value="<?= $v['visit_id'] ?>">
              <?= htmlspecialchars($v['full_name']) ?> |
              <?= $v['visit_date'] ?> |
              TSh <?= number_format($v['total_amount'], 2) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- AMOUNT -->
      <div class="col-md-3">
        <label class="form-label">Amount Paid (TSh)</label>
        <input type="number" name="amount_paid" class="form-control" min="0" required />
      </div>

      <!-- DATE -->
      <div class="col-md-3">
        <label class="form-label">Payment Date</label>
        <input type="date" name="payment_date"
               class="form-control"
               value="<?= date('Y-m-d') ?>" required />
      </div>

      <div class="col-md-12 text-end">
        <button type="submit" class="btn btn-success">💾 Save Payment</button>
      </div>

    </form>

  </div>
</div>

</div>
</div>
</body>
</html>
