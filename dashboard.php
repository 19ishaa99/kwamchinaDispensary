<?php
require "database/db.php";

// Total income
$incomeStmt = $pdo->query("SELECT SUM(amount) AS total_income FROM payments");
$totalIncome = $incomeStmt->fetch(PDO::FETCH_ASSOC)['total_income'] ?? 0;

// Total expenses
$expensesStmt = $pdo->query("SELECT SUM(amount) AS total_expenses FROM expenses");
$totalExpenses = $expensesStmt->fetch(PDO::FETCH_ASSOC)['total_expenses'] ?? 0;

// Balance
$balance = $totalIncome - $totalExpenses;
?>

<?php
// Fetch low-stock medicines count
$stmt = $pdo->query("
    SELECT COUNT(*) AS low_count
    FROM medicine_stock ms
    JOIN medicines m ON ms.medicine_id = m.medicine_id
    WHERE ms.quantity < ms.min_quantity
");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$hasLowStock = $row['low_count'] > 0;
?>

<?php
require 'database/db.php';

/* Fetch expenses grouped by category */
$expensesStmt = $pdo->query("
    SELECT ec.name AS category, SUM(e.amount) AS total_amount
    FROM expenses e
    JOIN expense_categories ec 
        ON e.category_id = ec.category_id
    GROUP BY ec.name
");
$expenses = $expensesStmt->fetchAll(PDO::FETCH_ASSOC);

/* Total expenses */
$totalStmt = $pdo->query("SELECT SUM(amount) FROM expenses");
$totalExpenses = $totalStmt->fetchColumn() ?? 0;
?>

<?php
require "database/db.php";

/* Total income */
$totalIncome = $pdo->query("
    SELECT COALESCE(SUM(amount), 0) 
    FROM payments
")->fetchColumn();

/* Income breakdown (by date or source later) */
$incomeBreakdown = $pdo->query("
    SELECT 
        DATE(payment_date) AS pay_date,
        SUM(amount) AS total
    FROM payments
    GROUP BY DATE(payment_date)
    ORDER BY pay_date DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
require "database/db.php";

/* TOTAL INCOME */
$totalIncome = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM payments
")->fetchColumn();

/* TOTAL EXPENSES */
$totalExpenses = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM expenses
")->fetchColumn();

/* BALANCE */
$balance = $totalIncome - $totalExpenses;
?>

<?php
require "database/db.php";

/* TODAY'S PATIENTS */
$todayPatientsStmt = $pdo->prepare("
    SELECT DISTINCT p.full_name
    FROM patient_visits v
    JOIN patients p ON v.patient_id = p.patient_id
    WHERE v.visit_date = CURDATE()
    ORDER BY p.full_name ASC
");
$todayPatientsStmt->execute();
$todayPatients = $todayPatientsStmt->fetchAll(PDO::FETCH_ASSOC);

/* COUNT (for dashboard card if needed) */
$todayPatientsCount = count($todayPatients);
?>

<?php
/* UNPAID BILLS */
$unpaidStmt = $pdo->prepare("
    SELECT 
        p.full_name,
        v.visit_id,
        v.total_amount - IFNULL(SUM(pay.amount), 0) AS balance_due
    FROM patient_visits v
    JOIN patients p ON v.patient_id = p.patient_id
    LEFT JOIN payments pay ON v.visit_id = pay.visit_id
    GROUP BY v.visit_id
    HAVING balance_due > 0
    ORDER BY balance_due DESC
");
$unpaidStmt->execute();
$unpaidBills = $unpaidStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
require 'database/db.php'; // or your db connection file

$activities = []; // ✅ prevent undefined variable warning

try {
    $stmt = $pdo->query("
        SELECT description, created_at
        FROM activity_logs
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Optional: log error
}
?>
<?php
require 'database/db.php'; // your PDO connection

$days = [];
$incomeData = [];
$expenseData = [];

// Create last 7 days labels (YYYY-MM-DD)
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $days[] = date('D', strtotime($day)); // e.g., Mon, Tue
    $incomeData[$day] = 0;
    $expenseData[$day] = 0;
}

// Fetch payments
$stmt = $pdo->query("
    SELECT payment_date, SUM(amount) as total
    FROM payments
    WHERE payment_date >= CURDATE() - INTERVAL 6 DAY
    GROUP BY payment_date
");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $incomeData[$row['payment_date']] = (float)$row['total'];
}

// Fetch expenses
$stmt = $pdo->query("
    SELECT expense_date, SUM(amount) as total
    FROM expenses
    WHERE expense_date >= CURDATE() - INTERVAL 6 DAY
    GROUP BY expense_date
");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $expenseData[$row['expense_date']] = (float)$row['total'];
}

// Prepare JS arrays
$incomeJs = json_encode(array_values($incomeData));
$expenseJs = json_encode(array_values($expenseData));
$labelsJs = json_encode($days);
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>KWAMCHINA DISPENSARY DASHBOARD</title>
  <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>-->
  <link href="bootstrap.min.css" rel="stylesheet"/>
  <style>
    body { background-color: #f8f9fa; }
    .card-icon { font-size: 1.5rem; }
    .summary-card { transition: 0.3s ease-in-out; }
    .summary-card:hover { transform: scale(1.03); }
    .activity-log { max-height: 200px; overflow-y: auto; }
  </style>
</head>
<body>
  <div class="container py-4">
    <header class="d-flex justify-content-between align-items-center mb-4">
      <h3>🩺 Dispensary Financial Monitoring System</h3>
      <div><span id="dateTime" class="text-muted"></span></div>
    </header>
 
      

    <!-- Summary Cards -->
    <div class="row text-center mb-4">
        <!-- Total Income -->
<div class="col-md-3">
  <div class="card summary-card text-bg-success">
    <div class="card-body">
      <div class="card-icon">💰</div>
      <button class="btn btn-link text-white text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#incomeModal">
        <h6>Total Income</h6>
        <p>TZS <?= number_format($totalIncome, 0) ?></p>
      </button>
    </div>
  </div>
</div>

      <?php
// Fetch total expenses from database
$stmt = $pdo->query("SELECT SUM(amount) as total_expenses FROM expenses");
$totalExpenses = $stmt->fetch(PDO::FETCH_ASSOC)['total_expenses'] ?? 0;
?>
<div class="col-md-3">
  <div class="card summary-card text-bg-danger">
    <div class="card-body">
      <div class="card-icon">💸</div>
      <button class="btn btn-link text-white text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#expensesModal">
        <h6>Total Expenses</h6>
        <p>TZS <?= number_format($totalExpenses, 0) ?></p>
      </button>
    </div>
  </div>
</div>

    <?php
// Fetch total income
$incomeStmt = $pdo->query("SELECT SUM(amount) as total_income FROM payments");
$totalIncome = $incomeStmt->fetch(PDO::FETCH_ASSOC)['total_income'] ?? 0;

// Fetch total expenses
$expenseStmt = $pdo->query("SELECT SUM(amount) as total_expenses FROM expenses");
$totalExpenses = $expenseStmt->fetch(PDO::FETCH_ASSOC)['total_expenses'] ?? 0;

// Calculate balance
$balance = $totalIncome - $totalExpenses;
?>

<div class="col-md-3">
  <div class="card summary-card text-bg-primary">
    <div class="card-body">
      <div class="card-icon">📊</div>
      <button class="btn btn-link text-white text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#balanceModal">
        <h6>Balance</h6>
        <p>TZS <?= number_format($balance, 0) ?></p>
      </button>
    </div>
  </div>
</div>

      <!-- Patients Today -->
<?php
// Get today's date
$today = date('Y-m-d');

// Count patients visited today
$patientsTodayStmt = $pdo->prepare("SELECT COUNT(*) as total_patients FROM patient_visits WHERE visit_date = ?");
$patientsTodayStmt->execute([$today]);
$patientsToday = $patientsTodayStmt->fetch(PDO::FETCH_ASSOC)['total_patients'] ?? 0;
?>

<div class="col-md-3">
  <div class="card summary-card text-bg-info">
    <div class="card-body">
      <div class="card-icon">👥</div>
      <button class="btn btn-link text-white text-decoration-none p-0" data-bs-toggle="modal" data-bs-target="#patientsModal">
        <h6>Patients Today</h6>
        <p><?= $patientsToday ?></p>
      </button>
    </div>
  </div>
</div>
    </div>

    <!-- Charts + Actions -->
    <div class="row mb-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">Monthly Overview</div>
          <div class="card-body">
            <canvas id="expenseChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card mb-3">
          <div class="card-header">Quick Actions</div>
          <div class="card-body d-grid gap-2">
            <a href="patient_registration_form.php"
            class="btn btn-outline-primary">➕ Add patient
            </a>
            <a href="medicine_form.php"
            class="btn btn-outline-primary">➕ Add medicine
            </a>
            <a href="payment_form.php"
           class="btn btn-outline-success">🧾 Record Payment
          </a>
            <a href="view_medicine.php" class="btn btn-outline-warning">🏥 View Inventory</a>
            <a href="report.php" class="btn btn-outline-dark">📄 Generate Report</a>
          </div>
        </div>

        <div class="card">
          <div class="card-header">Notifications</div>
          <ul class="list-group list-group-flush">
           <li class="list-group-item">


<!-- Single Low Stock Button -->
<li class="list-group-item">
<?php if ($hasLowStock): ?>
  <button class="btn btn-link text-danger text-decoration-none p-0" 
          data-bs-toggle="modal" 
          data-bs-target="#lowStockModal">
    ⚠️ Low stock alert
  </button>
<?php else: ?>
  <span class="text-success">All medicines sufficiently stocked</span>
<?php endif; ?>
</li>

<li class="list-group-item">
  <button class="btn btn-link text-danger text-decoration-none p-0" 
          data-bs-toggle="modal" 
          data-bs-target="#unpaidModal">
    ⚠️ View unpaid bills
  </button>
</li>


          </ul>
        </div>
      </div>
    </div>

  <div class="card">
  <div class="card-header">Recent Activity</div>
  <ul class="list-group list-group-flush activity-log">

    <?php if (count($activities) > 0): ?>
      <?php foreach ($activities as $activity): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($activity['description']) ?>
          <small class="text-muted">
            <?= date('d M H:i', strtotime($activity['created_at'])) ?>
          </small>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <li class="list-group-item text-muted">
        No recent activity
      </li>
    <?php endif; ?>

  </ul>
</div>


<canvas id="expenseChart"></canvas>

<script src="bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('expenseChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo $labelsJs; ?>,
        datasets: [
            {
                label: 'Income',
                data: <?php echo $incomeJs; ?>,
                borderColor: 'green',
                fill: false,
                tension: 0.2
            },
            {
                label: 'Expenses',
                data: <?php echo $expenseJs; ?>,
                borderColor: 'red',
                fill: false,
                tension: 0.2
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>


  
<!-- Income Modal -->
<div class="modal fade" id="incomeModal" tabindex="-1" aria-labelledby="incomeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="incomeModalLabel">💰 Income Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <h6 class="mb-3">Payment Summary</h6>

        <?php if (empty($incomeBreakdown)): ?>
          <p class="text-muted">No income recorded yet.</p>
        <?php else: ?>
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Total Income (TZS)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($incomeBreakdown as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['pay_date']) ?></td>
                  <td><?= number_format($row['total'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>

        <hr>
        <h5 class="text-end text-success">
          Total Income: TZS <?= number_format($totalIncome, 2) ?>
        </h5>
      </div>

    </div>
  </div>
</div>


<!-- Expenses Modal -->
<div class="modal fade" id="expensesModal" tabindex="-1" aria-labelledby="expensesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="expensesModalLabel">💸 Expenses Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p class="fw-bold">Breakdown of expenses:</p>

        <table class="table table-bordered">
          <thead class="table-light">
            <tr>
              <th>Category</th>
              <th>Amount (TZS)</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($expenses): ?>
              <?php foreach ($expenses as $exp): ?>
                <tr>
                  <td><?= htmlspecialchars($exp['category']) ?></td>
                  <td><?= number_format($exp['total_amount'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="2" class="text-center text-muted">
                  No expenses recorded
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <h6 class="text-end">
          <strong>Total Expenses:</strong>
          <span class="text-danger">
            TZS <?= number_format($totalExpenses, 2) ?>
          </span>
        </h6>

        <div class="mt-3 text-end">
          <a href="expenses.php" class="btn btn-primary">
            ➕ Record Expense
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Balance Modal -->
<div class="modal fade" id="balanceModal" tabindex="-1" aria-labelledby="balanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="balanceModalLabel">📊 Balance Summary</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Current financial balance:</p>

        <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between">
            <span>Total Income</span>
            <strong class="text-success">
              TZS <?= number_format($totalIncome, 2) ?>
            </strong>
          </li>

          <li class="list-group-item d-flex justify-content-between">
            <span>Total Expenses</span>
            <strong class="text-danger">
              TZS <?= number_format($totalExpenses, 2) ?>
            </strong>
          </li>

          <li class="list-group-item d-flex justify-content-between bg-light">
            <span><strong>Remaining Balance</strong></span>
            <strong class="<?= $balance >= 0 ? 'text-primary' : 'text-danger' ?>">
              TZS <?= number_format($balance, 2) ?>
            </strong>
          </li>
        </ul>

        <?php if ($balance < 0): ?>
          <div class="alert alert-warning">
            ⚠️ Expenses exceed income!
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>


<!-- Patients Today Modal -->
<div class="modal fade" id="patientsModal" tabindex="-1" aria-labelledby="patientsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="patientsModalLabel">👥 Today's Patients</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Patients attended today (<?= date('d M Y') ?>):</p>

        <?php if (count($todayPatients) > 0): ?>
          <ul class="list-group">
            <?php foreach ($todayPatients as $patient): ?>
              <li class="list-group-item">
                <?= htmlspecialchars($patient['full_name']) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="alert alert-info mb-0">
            No patients recorded today.
          </div>
        <?php endif; ?>

      </div>

    </div>
  </div>
</div>

<!--low stock medicine modal-->
<div class="modal fade" id="lowStockModal" tabindex="-1" aria-labelledby="lowStockModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-md">
    <div class="modal-content">
<div class="modal-body" id="printArea">
  <p>The following medicines are below the minimum stock level:</p>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr>
        <th>Medicine Name</th>
        <th>Current Quantity</th>
        <th>Minimum Required</th>
        <th>Needed</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Fetch low-stock medicines dynamically
      $stmt = $pdo->query("
        SELECT m.name, ms.quantity, ms.min_quantity
        FROM medicines m
        JOIN medicine_stock ms ON m.medicine_id = ms.medicine_id
        WHERE ms.quantity < ms.min_quantity
        ORDER BY m.name ASC
      ");
      $lowStockMeds = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($lowStockMeds) {
          foreach ($lowStockMeds as $med) {
              $needed = $med['min_quantity'] - $med['quantity'];
              echo "<tr>
                      <td>" . htmlspecialchars($med['name']) . "</td>
                      <td>{$med['quantity']}</td>
                      <td>{$med['min_quantity']}</td>
                      <td>{$needed}</td>
                    </tr>";
          }
      } else {
          echo "<tr><td colspan='4' class='text-center'>All medicines are sufficiently stocked.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="printLowStock()">🖨️ Print</button>
      </div>
    </div>
  </div>
</div>
<script>
function printLowStock() {
  const printContents = document.getElementById('printArea').innerHTML;
  const originalContents = document.body.innerHTML;

  document.body.innerHTML = `
    <html>
      <head>
        <title>Low Stock Report</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; }
          table { width: 100%; border-collapse: collapse; margin-top: 10px; }
          th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
          th { background-color: #f0f0f0; }
        </style>
      </head>
      <body>
        <h2>Kamchina Dispensary – Low Stock Report</h2>
        <p>Date: ${new Date().toLocaleDateString()}</p>
        ${printContents}
        <br><br>
        <p>Signature: ____________________________</p>
      </body>
    </html>
  `;

  window.print();
  document.body.innerHTML = originalContents;
  location.reload();
}
</script>

<!-- Unpaid Bills Modal -->
<div class="modal fade" id="unpaidModal" tabindex="-1" aria-labelledby="unpaidModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="unpaidModalLabel">💰 Unpaid Bills</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <?php if (count($unpaidBills) > 0): ?>
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Patient Name</th>
                <th>Amount Due (TZS)</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($unpaidBills as $bill): ?>
                <tr>
                  <td><?= htmlspecialchars($bill['full_name']) ?></td>
                  <td class="text-danger fw-bold">
                    <?= number_format($bill['balance_due'], 0) ?>
                  </td>
                  <td>
                    <a href="payment_form.php?visit_id=<?= $bill['visit_id'] ?>" 
                       class="btn btn-sm btn-success">
                      💳 Pay
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="alert alert-success mb-0">
            🎉 All bills are fully paid!
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>


<script>
document.getElementById('unpaidModal').addEventListener('show.bs.modal', function () {
  fetch('unpaid_bills.php')
    .then(res => res.json())
    .then(data => {
      const tbody = document.getElementById('unpaidTableBody');
      tbody.innerHTML = ''; // Clear previous rows
      data.forEach(item => {
        const row = `<tr>
          <td>${item.patient_name}</td>
          <td>${item.amount_due}</td>
        </tr>`;
        tbody.innerHTML += row;
      });
    });
});
</script>

</body>
</html>