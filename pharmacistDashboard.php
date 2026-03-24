<?php
session_start();
require "database/db.php";

// Only allow pharmacist
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pharmacist') {
    die("Access denied");
}

// Fetch summary data
$totalMedicines = 0;
$lowStockCount = 0;
$expiredCount = 0;
$recentLabTests = [];
$medicinesSummary = [];

try {
    // Total medicines in stock
    $stmt = $pdo->query("SELECT SUM(quantity) FROM medicine_stock");
    $totalMedicines = $stmt->fetchColumn() ?: 0;

    // Low stock alert (quantity < 10)
    $stmt = $pdo->query("SELECT COUNT(*) FROM medicine_stock WHERE quantity < 10");
    $lowStockCount = $stmt->fetchColumn() ?: 0;

    // Expired medicines
    $stmt = $pdo->query("SELECT COUNT(*) FROM medicine_stock WHERE expire_date <= CURDATE() + INTERVAL 7 DAY AND quantity > 0");
    $expiredCount = $stmt->fetchColumn() ?: 0;

    // Recent lab tests added/updated
    $stmt = $pdo->query("
        SELECT name, description, cost, lab_test_id
        FROM lab_tests
        ORDER BY lab_test_id DESC
        LIMIT 10
    ");
    $recentLabTests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Medicine summary for table
    $stmt = $pdo->query("
       SELECT m.name, ms.quantity, m.unit_price, 
       CASE 
           WHEN ms.quantity < 10 THEN '⚠️ Low Stock' 
          WHEN ms.expire_date <= CURDATE() + INTERVAL 7 DAY THEN '⚠️ Expiring Soon' 
           ELSE '✅ OK' 
       END AS status
FROM medicines m
JOIN medicine_stock ms ON m.medicine_id = ms.medicine_id
ORDER BY m.name ASC;
    ");
    $medicinesSummary = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
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

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Pharmacy Dashboard</title>
<link href="bootstrap.min.css" rel="stylesheet"/>
<style>
body { background-color: #f8f9fa; }
.summary-card { transition: 0.3s ease-in-out; }
.summary-card:hover { transform: scale(1.03); }
.activity-log { max-height: 200px; overflow-y: auto; }
</style>
</head>
<body>

<div class="container py-4">

<!-- HEADER -->
<header class="d-flex justify-content-between align-items-center mb-4">
    <h4>💊 Pharmacy Dashboard</h4>
    <div class="text-muted">
      Welcome, <?= htmlspecialchars($_SESSION['name']); ?> |
      <span id="dateTime"></span>
    </div>
</header>

<!-- SUMMARY CARDS -->
<div class="row text-center mb-4">
    <div class="col-md-4">
        <div class="card summary-card text-bg-success">
            <div class="card-body">
                <h6>📦 Total Medicines</h6>
                <p><?= number_format($totalMedicines) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card summary-card text-bg-warning">
            <div class="card-body">
                <h6>⚠️ Low Stock</h6>
                <p><?= $lowStockCount ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card summary-card text-bg-danger">
            <div class="card-body">
                <h6>❌ Expiring Medicines</h6>
                <p><?= $expiredCount ?></p>
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS + RECENT LAB TESTS -->
<div class="row mb-4">

    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">Recent Lab Tests</div>
        <div class="card-body">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Cost</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($recentLabTests as $i => $lab): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($lab['name']) ?></td>
                <td><?= htmlspecialchars($lab['description']) ?></td>
                <td><?= number_format($lab['cost'],2) ?></td>
                <td>
                  <a href="edit_lab_test.php?id=<?= $lab['lab_test_id'] ?>" class="btn btn-sm btn-success w-100">
                    ✏️ Edit
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(count($recentLabTests) === 0): ?>
                <tr><td colspan="5" class="text-center">No lab tests available</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- MEDICINE SUMMARY TABLE -->
      <div class="card">
        <div class="card-header">Medicine Summary</div>
        <div class="card-body">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($medicinesSummary as $i => $med): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($med['name']) ?></td>
                <td><?= $med['quantity'] ?></td>
                <td><?= number_format($med['unit_price'],2) ?></td>
                <td><?= $med['status'] ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if(count($medicinesSummary) === 0): ?>
                <tr><td colspan="5" class="text-center">No medicines available</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <div class="col-md-4">
      <div class="card mb-3">
        <div class="card-header">Quick Actions</div>
        <div class="card-body d-grid gap-2">
          <a href="add_new_medicine.php" class="btn btn-outline-primary">➕ Add Medicine</a>
          <a href="view_medicine.php" class="btn btn-outline-info">📦 View Inventory</a>
          <a href="labTest.php" class="btn btn-outline-success">➕ Add Lab Test</a>
          <a href="view_labTest.php" class="btn btn-outline-warning">📝 Update Lab Test</a>
        </div>
      </div>

      <div class="card">
        <div class="card-header">Notifications</div>
        <ul class="list-group list-group-flush">
          <?php if($lowStockCount > 0): ?>
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
          <?php endif; ?>
          <?php if($expiredCount > 0): ?>
            <li>
  <button class="btn btn-link text-danger text-decoration-none p-0"
          data-bs-toggle="modal"
          data-bs-target="#expiredMedicineModal">
    ❌ Expiring Medicines
  </button>
            </li>
<p><?= $expiredCount ?></p>

          <?php endif; ?>
          <?php if($lowStockCount === 0 && $expiredCount === 0): ?>
            <li class="list-group-item text-success">✅ All stock is healthy</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

</div>

</div>
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
      <!--<img src="logo.jpeg" alt="logo" style="width:50px;height:50px;">--> 
        <h2> SALAMA DISPENSARY – Low Stock Report</h2>
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

<script src="bootstrap.bundle.min.js"></script>
<script>
document.getElementById('dateTime').textContent = new Date().toLocaleString();
</script>

<!--expired modal-->
<div class="modal fade" id="expiredMedicineModal" tabindex="-1"
     aria-labelledby="expiredMedicineModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="expiredMedicineModalLabel">
          ❌ Expiring Medicines
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <table class="table table-bordered table-hover">
          <thead class="table-danger">
            <tr>
              <th>#</th>
              <th>Medicine Name</th>
              <th>Expiry date</th>
              <th>Quantity</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $stmt = $pdo->query("
SELECT m.name, ms.stock_id, ms.quantity, ms.expire_date
FROM medicines m
JOIN medicine_stock ms ON m.medicine_id = ms.medicine_id
WHERE ms.expire_date <= CURDATE() + INTERVAL 7 DAY 
  AND ms.quantity > 0
ORDER BY ms.expire_date ASC;
            ");
            $i = 1;
            $expiredMeds = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($expiredMeds):
              foreach ($expiredMeds as $med):
            ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($med['name']) ?></td>
                <td class="text-danger">
                  <?= date('d M Y', strtotime($med['expire_date'])) ?>
                </td>
                <td><?= $med['quantity'] ?></td>
              </tr>
            <?php
              endforeach;
            else:
            ?>
              <tr>
                <td colspan="4" class="text-center text-success">
                  ✅ No expiring medicines
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">
          Close
        </button>
      </div>

    </div>
  </div>
</div>

</body>
</html>
